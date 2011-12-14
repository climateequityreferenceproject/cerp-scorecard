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

function nice_number($val) {
    if ($val > 0 && $val < 0.5) {
        $val_string = "<0.5";
    } elseif ($val < 0 && $val > -0.5) {
        $val_string = ">-0.5";
    } else {
        $val_string = number_format($val);
    }
    return $val_string;
}

// Returns pledge in MtCO2e, based on pledged dollar amount and carbon price
function get_intl_pledge($iso3, $year) {
    if (!$iso3 || !$year) {
        return array('intl_pledge' => NULL, 'intl_source' => NULL);
    }
    $sql = "SELECT iso3, pledge_mln_USD AS pledge, source FROM intl_pledge WHERE iso3='" . $iso3 . "'";
    $result = query_db($sql);
    $sources_array = array();
    $pledge_mlnUSD = 0;
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $pledge_mlnUSD += $row['pledge'];
        $sources_array[] = $row['source'];
    }
    $sources = join("; ", $sources_array);
    
    $sql = "SELECT c_price_USD_per_tCO2e AS price FROM carbon_price WHERE year='" . $year . "'";
    $result = query_db($sql);
    // Only one row
    $row = mysql_fetch_row($result);
    $price = $row[0];
    
    $pledge_MtCO2e = $pledge_mlnUSD/$price;
    
    return array('intl_pledge' => $pledge_MtCO2e, 'intl_source' => $sources);
}

function avail_countries_options($iso3=NULL) {
    $db = db_connect();
    
    $sql = "SELECT pledge.iso3 AS iso3, name FROM country, pledge WHERE pledge.iso3 = country.iso3 AND public = 1 ORDER BY name;";
    
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
            if ($row['iso3']===$iso3) {
                $sel_string = ' selected="selected"';
            } else {
                $sel_string = '';
            }
                
            $html .= '<option value=' . $row['iso3'] . $sel_string . '>'  . $row['name'] . '</option>';
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
    if (!$row) {
        return NULL;
    } else {
        $intl_pledge = get_intl_pledge($iso3_3lett, $year_checked);
        return $row + $intl_pledge;
    }
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
    $description = 'Pledge to reduce ';
    $by_factor = $pledge_info['reduction_percent'];
    switch ($pledge_info['quantity']) {
        case 'absolute':
            $description .= 'total emissions by ' . $by_factor . '% compared to ';
            if ($pledge_info['year_or_bau'] === 'bau') {
                $description .= '<a class="def_link" href="glossary.php#gloss_bau" target="_blank">business-as-usual</a>';
                $pledged_reduction = (1 - $factor) * $bau[$pledge_info['by_year']];
            } else {
                $description .= $pledge_info['rel_to_year'];
                $pledged_reduction = $bau[$pledge_info['by_year']] - $factor * $bau[$pledge_info['rel_to_year']];
            }
            break;
        case 'intensity':
            $description .= '<a class="def_link" href="glossary.php#gloss_ei" target="_blank">emissions intensity</a> by ' . $by_factor . '% compared to ';
            if ($pledge_info['year_or_bau'] === 'bau') {
                // This option actually makes no sense, but take care of it just in case:
                $description .= '<a class="def_link" href="glossary.php#gloss_bau" target="_blank">business-as-usual</a>';
                $pledged_reduction = (1 - $factor) * $bau[$pledge_info['by_year']];
            } else {
                $description .= $pledge_info['rel_to_year'];
                $scaled_emiss = $gdp[$pledge_info['by_year']] * $bau[$pledge_info['rel_to_year']]/$gdp[$pledge_info['rel_to_year']];
                $pledged_reduction = $bau[$pledge_info['by_year']] - $factor * $scaled_emiss;
            }
            break;
        default:
            // Shouldn't reach here
    }
    $retval['pledge_over_bau'] = 100 * (1 - $pledged_reduction/$bau[$pledge_info['by_year']]);
    $retval['pledge_description'] = $description . '.';
    
    //$pledged_reduction = min(max(0, $pledged_reduction), $gdrs_reduction);
    $retval['intl_pledge'] = 100.0 * $pledge_info['intl_pledge']/$gdrs_reduction;
    $retval['dom_pledge'] = 100.0 * $pledged_reduction/$gdrs_reduction;
    $retval['gap'] = 100.0 - $retval['dom_pledge'] - $retval['intl_pledge'];
    
    return $retval;
}

function draw_bars_get_remainder($pledge,$class) {
    $html = '';
    for ($i = $pledge; $i >= 100; $i -= 100) {
        $html .= '<div class="' . $class . '" style="width:100%"></div>';
    }     
    $html .= '<div class="' . $class . '" style="width:' . $i . '%"></div>';
    return array(
        'remainder' => (100 - $i),
        'html' => $html
    );
}

function draw_graph($pledge1,$class1,$pledge2,$class2) {
    $pledge1 = round($pledge1);
    $pledge2 = round($pledge2);
    if (($pledge1 + $pledge2) >= 100) {
        $gap = 0;
    } else {
        // In theory this is what it is, but was getting rounding errors
        $gap = 100 - ($pledge1 + $pledge2);
    }
    $bar_info1 = draw_bars_get_remainder($pledge1,$class1);
    $retval = $bar_info1['html'];
    $remainder_pledge1 = $bar_info1['remainder'];
    if ($pledge2 <= $remainder_pledge1) {
        $retval .= '<div class="' . $class2 . '" style="width:' . $pledge2 . '%"></div>';
    } else {
        $retval .= '<div class="' . $class2 . '" style="width:' . $remainder_pledge1 . '%"></div>';
        $pledge2 = $pledge2 - $remainder_pledge1;
        $bar_info2 = draw_bars_get_remainder($pledge2,$class2);
        $retval .= $bar_info2['html'];
    }
    $retval .= '<div class="gap" style="width:' . $gap . '%"></div>';
    return $retval;
}

function clean_text($string) {
    $retval = trim($string);
    // Remove trailing punctuation
    $end = substr($retval, -1);
    while (in_array($end, array('.', ',', ';', '?', '!'))) {
        $retval = trim(substr($retval, 0, -1));
        $end = substr($retval, -1);
    }
    return $retval;
}