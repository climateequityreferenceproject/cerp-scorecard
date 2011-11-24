<?php
require_once "HTTP/Request.php";

function db_connect() {
    $db = mysql_connect('localhost', 'pledges', '***REMOVED***');
    if (!$db) {
        die('Could not connect: ' . mysql_error());
    }
    mysql_select_db("pledges", $db);
    
    return $db;
}

function query_db($query) {
    $db = db_connect();
    
    $result = mysql_query($query, $db);
    if (!$result) {
        mysql_close($db);
        die('Invalid query: ' . mysql_error());
    }
    
    mysql_close($db);
    
    return $result;
}

function avail_countries_options() {
    $db = db_connect();
    
    $sql = "SELECT pledge.iso3 AS iso3, name FROM country, pledge WHERE pledge.iso3 = country.iso3 ORDER BY name;";
    
    $result = mysql_query($sql, $db);
    if (!$result) {
        mysql_close($db);
        die('Invalid query: ' . mysql_error());
    }
    mysql_close($db);
    
    $html = "";
    $keys = array();
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        if (!in_array($row['iso3'], $keys, true)) {
            $html .= '<option value=' . $row['iso3'] . '>'  . $row['name'] . '</option>';
            $keys[] = $row['iso3'];
        }
    }
    mysql_free_result($result);
    
    return $html;
}

// $pathways is an array of the type array('label' => 'short_code', ...): returns 'label'=>'id'
function get_pathways($pathways) {
    $req =& new HTTP_Request("http://gdrights.org/calculator/api/?q=pathways");
    $req->setMethod(HTTP_REQUEST_METHOD_GET);
    if (!PEAR::isError($req->sendRequest())) {
         $response = json_decode($req->getResponseBody());
    } else {
        $response = "";
    }
    $retval = array();
    foreach ($pathways as $key => $val) {
        foreach ($response as $pathway) {
            // The json_decode function returns these arrays as type StdClass
            $pathway_array = (array) $pathway;
            if ($pathway_array['short_code'] === $val) {
                $retval[$key] = $pathway_array['id'];
                break;
            }
        }
    }
    return $retval;
}

// $iso3 is the three-letter code, $conditional is a boolean saying whether it's conditional or not
function get_min_target_year($iso3, $conditional) {
    // To protect against SQL injection, force conditional to be boolean & iso3 to be three first characters
    $conditional_bool = $conditional ? 1 : 0;
    $iso3_3lett = substr($iso3, 0, 3);
    $sql = 'SELECT MIN(by_year) AS year FROM pledge WHERE conditional=' . $conditional_bool . ' AND iso3="' . $iso3_3lett . '";';
    $result = query_db($sql);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    mysql_free_result($result);
    return $row['year'];
}

function get_country_name($iso3) {
    // To protect against SQL injection, force iso3 to be three first characters
    $iso3_3lett = substr($iso3, 0, 3);
    $sql = 'SELECT name FROM country WHERE iso3="' . $iso3_3lett . '";';
    $result = query_db($sql);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    mysql_free_result($result);
    return $row['name'];
}

function get_pledge_information($iso3, $conditional, $year) {
    // Protect against injection
    $conditional_bool = $conditional ? 1 : 0;
    $iso3_3lett = substr($iso3, 0, 3);
    $year_checked = intval($year);
    $sql = 'SELECT * FROM pledge WHERE conditional=' . $conditional_bool . ' AND iso3="' . $iso3_3lett . '" AND by_year=' . $year_checked . ';';
    $result = query_db($sql);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    mysql_free_result($result);
    return $row;
}

function get_gdrs_information($pledge_info, $pathway) {
    if (!$pledge_info) {
        return NULL;
    }
    // First, get the parameter values used by the database
    $req =& new HTTP_Request("http://gdrights.org/calculator/api/?q=params");
    $req->setMethod(HTTP_REQUEST_METHOD_GET);
    if (!PEAR::isError($req->sendRequest())) {
         $params = (array) json_decode($req->getResponseBody());
    } else {
        $params = NULL;
    }
    
    $use_lulucf = (array) $params['use_lulucf'];
    $use_nonco2 = (array) $params['use_nonco2'];
    
    // Announce that we'd like to free up memory before reusing the variable
    unset($req);
    
    // Build up API query
    $req =& new HTTP_Request("http://gdrights.org/calculator/api/");
    $req->setMethod(HTTP_REQUEST_METHOD_POST);
    if ($pledge_info['rel_to_year']) {
        $years = $pledge_info['rel_to_year'] . "," . $pledge_info['by_year'];
    } else {
        $years = $pledge_info['by_year'];
    }
    $req->addPostData("years", $years);
    $req->addPostData("countries", $pledge_info['iso3']);
    $req->addPostData("emergency_path", $pathway);
    if (!PEAR::isError($req->sendRequest())) {
         $response = json_decode($req->getResponseBody());
         // Oddly, the decode procedure seems to duplicate the first element, so get the tail:
         $response = array_slice($response, 1);
    } else {
        return NULL;
    }
    
    foreach ($response as $year_data_obj) {
        $year_data = (array) $year_data_obj;
        $year = $year_data['year'];
        $gdp[$year] = $year_data['gdp_blnUSDMER'];
        $bau[$year] = $year_data['fossil_CO2_MtCO2'];
        if ($use_lulucf['value']) {
            $bau[$year] += $year_data['LULUCF_MtCO2'];
        }
        if ($use_nonco2['value']) {
            $bau[$year] += $year_data['NonCO2_MtCO2e'];
        }
        $alloc[$year] = $year_data['gdrs_alloc_MtCO2'];
    }
    
    $gdrs_reduction = $bau[$pledge_info['by_year']] - $alloc[$pledge_info['by_year']];
    switch ($pledge_info['rel_to']) {
        case 'below':
            $factor = 1 - $pledge_info['reduction_percent']/100.0;
            break;
        case 'of':
            $factor = $pledge_info['reduction_percent']/100.0;
            break;
        default:
            // Shouldn't get here
    }
    switch ($pledge_info['quantity']) {
        case 'absolute':
            if ($pledge_info['year_or_bau'] === 'bau') {
                $pledged_reduction = (1 - $factor) * $bau[$pledge_info['by_year']];
            } else {
                $pledged_reduction = $bau[$pledge_info['by_year']] - $factor * $bau[$pledge_info['rel_to_year']];
            }
            break;
        case 'intensity':
            if ($pledge_info['year_or_bau'] === 'bau') {
                // This option actually makes no sense, but take care of it just in case:
                $pledged_reduction = (1 - $factor) * $bau[$pledge_info['by_year']];
            } else {
                $scaled_emiss = $gdp[$pledge_info['by_year']] * $bau[$pledge_info['rel_to_year']]/$gdp[$pledge_info['rel_to_year']];
                $pledged_reduction = $bau[$pledge_info['by_year']] - $factor * $scaled_emiss;
            }
            break;
        default:
            // Shouldn't reach here
    }
    
/*
    echo "Pledged: " . $pledged_reduction;
    echo '<br/><br/>';
    echo "GDRs: " . $gdrs_reduction;
    echo '<br/><br/>';
    echo "Fraction: " . $factor;
    echo '<br/><br/>';
*/   
    
    //$pledged_reduction = min(max(0, $pledged_reduction), $gdrs_reduction);
    $retval['dom_pledge'] = 100.0 * $pledged_reduction/$gdrs_reduction;
    $retval['gap'] = 100.0 - $retval['dom_pledge'];
    
    return $retval;
}

