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
    $req =& new HTTP_Request("http://gdrights.org/calculator_dev/api/?q=pathways");
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

