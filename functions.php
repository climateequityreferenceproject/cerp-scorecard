<?php
/**
 * functions.php
 * 
 * PHP Version 5
 *
 * @package GDRsScorecard
 * @copyright 2011-2012 EcoEquity and Stockholm Environment Institute
 * @license All rights reserved
 * @link http://www.gdrights.org/
 */

require_once "HTTP/Request.php";
require_once "class/GDRsAPI/GDRsAPI.php";
require_once "class/HWTHelp/HWTHelp.php";


/**
 * Say whether the scoreard is being called from a directory with "dev" in the name
 * @return boolean 
 */
function isDev() {
    // Note: might return "0" (a correct, non-false value)--have to check for false
    if (strpos($_SERVER['PHP_SELF'], 'dev')===false) {
        return false;
    } else {
        return true;
    }
}

/**
 * Get the path to the calculator, with no trailing slash
 * 
 * @return string 
 */
function getCalcPath() {
    if (isDev()) {
        return 'http://gdrights.org/calculator_dev';
    } else {
        return 'http://gdrights.org/calculator';
    }
}

/**
 * Build URL for GDRs calculator country report page
 * 
 * @param string $iso3 3-letter code for currently-displayed country
 * 
 * @return URL for GDRs calculator country report page
 */
function getCalcUrl($iso3, $pathway_id)
{
    if (isset($_SESSION['gdrs_db'])) {
        $key = GDRsAPI::get_db_key($pathway_id);
        if (isset($_SESSION['gdrs_db'][$key])) {
            $db_string = '&db=' . $_SESSION['gdrs_db'][$key];
        } else {
            $db_string = '';
        }
    } else {
        $db_string = '';
    }
    return getCalcPath() . '/?iso3=' . $iso3 . $db_string;
}

/**
 * Connect to the MYSQL pledges database--expects calling function to disconnect
 * 
 * @return mysqldb A database connection for the pledges database
 */
function pledgeDBConnect()
{
//    if (isDev()) {
//        $db_name = 'pledges-dev';
//    } else {
//        $db_name = 'pledges';
//    }
    $db_name = 'pledges';
    $db = mysql_connect('localhost', $db_name, '***REMOVED***');
    if (!$db) {
        die('Could not connect: ' . mysql_error());
    }
    mysql_select_db($db_name, $db);
    
    return $db;
}

/**
 * Return an array of query results from the pledge DB
 * 
 * @param string $query SQL string
 * 
 * @return array query result
 */
function queryPledgeDB($query)
{
    $db = pledgeDBConnect();
    
    $result = mysql_query($query, $db);
    if (!$result) {
        mysql_close($db);
        die('Invalid query: ' . mysql_error());
    }
    
    mysql_close($db);
    
    return $result;
}

/**
 * Produce a nicely formatted value as a string
 * 
 * @param number $val A floating-point number
 * 
 * @return string A formatted string that either uses number_format() or "<0.5",">-0.5" for small but nonzero values
 */
function niceNumber($val)
{
    if ($val > 0 && $val < 0.1) {
        $val_string = "<0.1";
    } elseif ($val < 0 && $val > -0.1) {
        $val_string = ">-0.1";
    } elseif (abs($val) < 10 && $val != 0) {
        $val_string = number_format($val, 1);
    } else {
        $val_string = number_format($val);
    }
    return $val_string;
}

/**
 * Returns pledge in MtCO2e, based on pledged dollar amount and carbon price
 * 
 * @param string  $iso3 A 3-letter ISO code for the country
 * @param integer $year The four-digit year
 * 
 * @return array An array with the pledge amount and a string containing notes 
 * 
 * @todo Extend to regions
 */
function getIntlPledge($iso3, $year)
{
    if (!$iso3 || !$year) {
        return array('intl_pledge' => null, 'intl_source' => null);
    }
    $sql = "SELECT iso3, pledge_mln_USD AS pledge, source FROM intl_pledge WHERE iso3='" . $iso3 . "'";
    $result = queryPledgeDB($sql);
    $sources_array = array();
    $pledge_mlnUSD = 0;
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $pledge_mlnUSD += $row['pledge'];
        $sources_array[] = $row['source'];
    }
    $sources = join("; ", $sources_array);
    
    $sql = "SELECT c_price_USD_per_tCO2e AS price FROM carbon_price WHERE year='" . $year . "'";
    $result = queryPledgeDB($sql);
    // Only one row
    $row = mysql_fetch_row($result);
    $price = $row[0];
    
    $pledge_MtCO2e = $pledge_mlnUSD/$price;
    
    return array('intl_pledge' => $pledge_MtCO2e, 'intl_source' => $sources);
}

/**
 * Return an options list consisting of iso3 codes (as option value) and country names (as displayed text)
 * 
 * @param string $iso3 ISO 3-letter code for selected country, defaults to null
 * 
 * @return string HTML-formatted option list
 */
function availCountriesOptions($iso3=null)
{
    $db = pledgeDBConnect();
    
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

/**
 * Return an options list consisting of GDRs region codes (as option value) and region names (as displayed text)
 * 
 * @param string $region Code for selected region, defaults to null
 * 
 * @return string HTML-formatted option list
 */
function availRegionsOptions($region=null)
{
    $db = pledgeDBConnect();
    
    $sql = "SELECT pledge.region AS region, name FROM region, pledge WHERE pledge.region = region.region_code AND public = 1 ORDER BY name;";
    
    $result = mysql_query($sql, $db);
    if (!$result) {
        mysql_close($db);
        die('Invalid query: ' . mysql_error());
    }
    mysql_close($db);
    
    $html = "";
    $keys = array();
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        if (!in_array($row['region'], $keys, true)) {
            if ($row['region']===$region) {
                $sel_string = ' selected="selected"';
            } else {
                $sel_string = '';
            }
                
            $html .= '<option value=' . $row['region'] . $sel_string . '>'  . $row['name'] . '</option>';
            $keys[] = $row['region'];
        }
    }
    mysql_free_result($result);
    
    return $html;
}

/**
 * Return a boolean saying whether a region/country code refers to a country
 * 
 * @param string $code The code to check
 * 
 * @return boolean True if it is a country, false otherwise (presumably a region, but not checking)
 */
function isCountry($code)
{
    $db = pledgeDBConnect();
    
    $sql = 'SELECT iso3 FROM country WHERE iso3="' . $code . '";';
    
    $result = mysql_query($sql, $db);
    if (!$result) {
        mysql_close($db);
        die('Invalid query: ' . mysql_error());
    }
    mysql_close($db);
    
    return mysql_num_rows($result) > 0;
}

/**
 * Get the target year for the country or region and type of pledge; if multiple pledges, get minimum year
 * 
 * @param string  $code        ISO 3-letter code or GDRs region code
 * @param boolean $conditional Whether to fetch for conditional pledge or unconditional
 * 
 * @return integer Four-digit year 
 */
function getMinTargetYear($code, $conditional)
{
    // To protect against SQL injection, force conditional to be boolean
    $conditional_bool = $conditional ? 1 : 0;
    if (isCountry($code)) {
        $ctryrgn_str = 'iso3="' . $code . '"';
    } else {
        $ctryrgn_str = 'region="' . $code . '"';
    }
    $sql = 'SELECT MIN(by_year) AS year FROM pledge WHERE conditional=' . $conditional_bool . ' AND ' . $ctryrgn_str . ' AND public = 1;';
    $result = queryPledgeDB($sql);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    mysql_free_result($result);
    if (!$row) {
        return NULL;
    } else {
        return $row['year'];
    }
}

/**
 * Get the country or region name corresponding to an iso3 code or GDRs region code
 * 
 * @param string $code ISO 3-letter code or GDRs region code
 * 
 * @return string Corresponding country or region name
 */
function getCountryRegionName($code)
{
    if (isCountry($code)) {
        $sql = 'SELECT name FROM country WHERE iso3="' . $code . '";';
    } else {
        $sql = 'SELECT name FROM region WHERE region_code="' . $code . '";';
    }
    $result = queryPledgeDB($sql);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    mysql_free_result($result);
    return $row['name'];
}

/**
 * Get all pledge information for a specific country or region, type of pledge, and year
 * 
 * @param string  $code        ISO 3-letter code or GDRs region code
 * @param boolean $conditional Whether pledge is conditional or unconditional
 * @param integer $year        4-digit year
 * 
 * @return array Collected information about the pldege
 */
function getPledgeInformation($code, $conditional, $year)
{
    // Protect against injection
    $conditional_bool = $conditional ? 1 : 0;
    $year_checked = intval($year);
    if (isCountry($code)) {
        $ctryrgn_str = 'iso3="' . $code . '"';
    } else {
        $ctryrgn_str = 'region="' . $code . '"';
    }
    $sql = 'SELECT * FROM pledge WHERE conditional=' . $conditional_bool . ' AND ' . $ctryrgn_str . ' AND by_year=' . $year_checked . ';';
    $result = queryPledgeDB($sql);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    mysql_free_result($result);
    if (!$row) {
        return null;
    } else {
        $intl_pledge = getIntlPledge($code, $year_checked);
        return $row + $intl_pledge;
    }
}

/**
 * Return Boolean saying whether there is a conditional pledge for a specific country or region and year
 * 
 * @param string  $code        ISO 3-letter code or GDRs region code
 * @param integer $year        4-digit year
 * 
 * @return Boolean
 */
function hasConditionalPledge($code, $year=null) 
{
    // Protect against injection
    if (isCountry($code)) {
        $ctryrgn_str = 'iso3="' . $code . '"';
    } else {
        $ctryrgn_str = 'region="' . $code . '"';
    }
    if ($year) {
        $year_checked = intval($year);
        $sql = 'SELECT * FROM pledge WHERE conditional=1 AND ' . $ctryrgn_str . ' AND by_year=' . $year_checked . ';';
    } else {
        $sql = 'SELECT * FROM pledge WHERE conditional=1 AND ' . $ctryrgn_str . ' AND by_year <= ' . GDRsAPI::$maxYear;
    }
    
    return mysql_num_rows(queryPledgeDB($sql)) != 0;
}


/**
 * Return Boolean saying whether there is an unconditional pledge for a specific country or region and year
 * 
 * @param string  $code        ISO 3-letter code or GDRs region code
 * @param integer $year        4-digit year
 * 
 * @return Boolean
 */
function hasUnconditionalPledge($code, $year=null) 
{
    // Protect against injection
    if (isCountry($code)) {
        $ctryrgn_str = 'iso3="' . $code . '"';
    } else {
        $ctryrgn_str = 'region="' . $code . '"';
    }
    if ($year) {
        $year_checked = intval($year);
        $sql = 'SELECT * FROM pledge WHERE conditional=0 AND ' . $ctryrgn_str . ' AND by_year=' . $year_checked . ';';
    } else {
        $sql = 'SELECT * FROM pledge WHERE conditional=0 AND ' . $ctryrgn_str . ' AND by_year <= ' . GDRsAPI::$maxYear;
    }
    
    return mysql_num_rows(queryPledgeDB($sql)) != 0;
}



/**
 * Major workhorse function: Evaluate the pledge against the GDRs requirement
 * 
 * @param array   $pledge_info Array of pledge information returned by getPledgeInformation
 * @param integer $pathway     ID for the selected pathway
 * 
 * @return array Contains information about the pledge and how it matches up to the GDRs requirement
 */
function getGdrsInformation($pledge_info, $pathway, $kab_score = 'option1')
{
    $glossary = new HWTHelp('def_link', 'glossary.php');
    $retval = array();
    
    if (!$pledge_info) {
        return null;
    }
    
    $params = GDRsAPI::connection()->get('params');
    
    $use_lulucf = (array) $params['use_lulucf'];
    $use_nonco2 = (array) $params['use_nonco2'];
    
    // Announce that we'd like to free up memory before reusing the variable
    // unset($req);
    
    // Build up API query
    if ($pledge_info['rel_to_year']) {
        $years = $pledge_info['rel_to_year'] . "," . $pledge_info['by_year'];
    } else {
        $years = $pledge_info['by_year'];
    }
    if (isset($pledge_info['iso3'])) {
        $ctrycode = $pledge_info['iso3'];
        $ctryrgn =  $ctrycode . ',world';
    } elseif (isset($pledge_info['region'])) {
        $ctrycode = $pledge_info['region'];
        $ctryrgn =  $ctrycode . ',world';
    } else {
        $ctryrgn = null;
    }
    // TODO: For regions, it is not getting values for 1990
    $post_array = array('years' => $years, 'countries' => $ctryrgn);
    if (kabsOn()) {
        $kab = 'all';
    } else {
        $kab = 'none';
    }
    $response = GDRsAPI::connection()->post($post_array, $pathway, $kab);
    
    foreach ($response as $year_data_obj) {
        $year_data = (array) $year_data_obj;
        $year = $year_data['year'];
        if ($year_data['code'] === $ctrycode) {
            $gdp[$year] = $year_data['gdp_blnUSDMER'];
            $bau[$year] = $year_data['fossil_CO2_MtCO2'];
            $bau_kab[$year] = $bau[$year] - $year_data['kyoto_gap_MtCO2'];
            $c_frac[$year] = $year_data['gdrs_c_frac'];
            $r_frac[$year] = $year_data['gdrs_r_frac'];
            $rci[$year] = $year_data['gdrs_rci'];
            if ($use_lulucf['value']) {
                $bau[$year] += $year_data['LULUCF_MtCO2'];
            }
            if ($use_nonco2['value']) {
                $bau[$year] += $year_data['NonCO2_MtCO2e'];
            }
            $alloc[$year] = $year_data['gdrs_alloc_MtCO2'];
        } else {
            $bau_world[$year] = $year_data['fossil_CO2_MtCO2'];
            if ($use_lulucf['value']) {
                $bau_world[$year] += $year_data['LULUCF_MtCO2'];
            }
            if ($use_nonco2['value']) {
                $bau_world[$year] += $year_data['NonCO2_MtCO2e'];
            }
            $alloc_world[$year] = $year_data['gdrs_alloc_MtCO2'];
        }
    }
    
    if (kabsOn()) {
        if ($kab_score === 'option1') {
            $gdrs_reduction = $bau[$pledge_info['by_year']] - $alloc[$pledge_info['by_year']];
        } else {
            $gdrs_reduction = $bau_kab[$pledge_info['by_year']] - $alloc[$pledge_info['by_year']];
        }
    } else {
        $gdrs_reduction = $bau[$pledge_info['by_year']] - $alloc[$pledge_info['by_year']];
    }
    $gdrs_reduction_world = $bau_world[$pledge_info['by_year']] - $alloc_world[$pledge_info['by_year']];
    
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
                $description .= $glossary->getLink('gloss_bau', true);
                $pledged_reduction = (1 - $factor) * $bau[$pledge_info['by_year']];
            } else {
                $description .= $pledge_info['rel_to_year'];
                $pledged_reduction = $bau[$pledge_info['by_year']] - $factor * $bau[$pledge_info['rel_to_year']];
            }
            break;
        case 'intensity':
            $description .= $glossary->getLink('gloss_ei', true) . ' by ' . $by_factor . '% compared to ';
            if ($pledge_info['year_or_bau'] === 'bau') {
                // This option actually makes no sense, but take care of it just in case:
                 $description .= $glossary->getLink('gloss_bau', true);
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
    
    $retval['pledge_description'] = $description . '.';
    
    $retval['cap'] = 100.0 * $c_frac[$pledge_info['by_year']];
    $retval['resp'] = 100.0 * $r_frac[$pledge_info['by_year']];
    
    $gdrs_reduction_perc_bau = 100 * $gdrs_reduction/$bau[$pledge_info['by_year']];
    $pledged_reduction_perc_bau = 100 * $pledged_reduction/$bau[$pledge_info['by_year']];
    $retval['score'] = 100 - ($gdrs_reduction_perc_bau - $pledged_reduction_perc_bau);
    
    $retval['fair_share_perc'] = 100 * $rci[$pledge_info['by_year']];
    // Don't allow negative values (at least for some)
    $retval['pledged_reduct_perc'] = 100 * max(0, $pledged_reduction)/$gdrs_reduction_world;
    $retval['pledged_reduct_MtCO2'] = $pledged_reduction;
    $retval['pledge_gap_MtCO2'] = max(0, $gdrs_reduction - $pledged_reduction);
    $retval['pledge_gap_perc_bau'] = max(0, $gdrs_reduction_perc_bau - $pledged_reduction_perc_bau);
    
    return $retval;
}

/**
 * Generate the HTML containint the divs that represent the bars on the bar chart
 * 
 * @param integer $pledge The value of the pledge as a percentage of GDRs obligation (may be > 100%)
 * @param string  $class  The CSS class used to style the bar
 * 
 * @return array The remaining part of the bar and the HTML to render the current bar
 */
function drawBarsGetRemainder($pledge, $class)
{
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

/**
 * Generate HTML for full set of bars
 * 
 * The final part of the bar has class "gap"
 * 
 * @param integer $pledge1 The percentage of GDRs obligation covered by pledge of class1
 * @param string  $class1  The CSS class label for 1st part of bar (international pledge)
 * @param integer $pledge2 The percentage of GDRs obligation covered by pledge of class2
 * @param string  $class2  The CSS class label for 2nd part of bar (domestic effort)
 * 
 * @return string The HTML to render
 */
// TODO determine whether drawGraph needs to continue to accommodate international pledges,
// and if not, change function to simplify arguments

function drawGraph($pledge1,$class1,$pledge2,$class2, $show_pledge1 = true)
{
    if ($show_pledge1) {
        $pledge1 = round($pledge1);
    } else {
        $pledge1 = 0.0;
    }
    $pledge2 = round($pledge2);
    if (($pledge1 + $pledge2) >= 100) {
        $gap = 0;
    } else {
        // In theory this is what it is, but was getting rounding errors
        $gap = 100 - ($pledge1 + $pledge2);
    }
    //$bar_info1 = drawBarsGetRemainder($pledge1, $class1);
    //$retval .= $bar_info1['html'];
    $retval = '';
    $remainder_pledge1 = 100; //$remainder_pledge1 = $bar_info1['remainder'];
    if ($pledge2 <= $remainder_pledge1) {
        $retval .= '<div class="' . $class2 . '" style="width:' . $pledge2 . '%"></div>';
    } else {
        $retval .= '<div class="' . $class2 . '" style="width:' . $remainder_pledge1 . '%"></div>';
        $pledge2 = $pledge2 - $remainder_pledge1;
        $bar_info2 = drawBarsGetRemainder($pledge2, $class2);
        $retval .= $bar_info2['html'];
    }
    $retval .= '<div class="gap" style="width:' . $gap . '%"></div>';
    return $retval;
}

/**
 * Generate HTML for full set of bars, -50 to 50
 * 
 * @param integer $score 
 * 
 * @return string The HTML to render
 */
function drawGraph5050($score)
{
    switch ($score) {
        case ($score < -50):
            $score = 50;
            $score_class = 'neg';
            $plug = 0;
            $gap = 50;
            $below_range = true;
            break;
        case ($score == -50):
            $score = 50;
            $score_class = 'neg';
            $plug = 0;
            $gap = 50;
            break;
        case ($score > -50 and $score < 0):
            $score = abs($score);
            $score_class = 'neg';
            $plug = 50 - $score;
            $gap = 50;
            break;
        case ($score == 0):
            $score_class = 'zero';
            $plug = 50;
            $gap = 50;
            break;
        case ($score > 0 and $score < 50):
            $score_class = 'pos';
            $plug = 50;
            $gap = 50 - $score;
            break;
        case ($score == 50):
            $score_class = 'pos';
            $plug = 50;
            $gap = 0;
            break;
        case ($score > 50):
            $score = 50;
            $score_class = 'pos';
            $plug = 50;
            $gap = 0;
            $above_range = true;
            break;
    }
    
    $retval = '';
    $retval .= '<div class="plug" style="width:' . $plug . '%"></div>';
    $retval .= '<div class="' . $score_class . '" style="width:' . $score . '%"></div>';
    $retval .= '<div class="gap" style="width:' . $gap . '%"></div>';
    $retval .= '<div class="zero_line"></div>';
    return $retval;
}


/**
 * Generate HTML for full set of bars, -100 to 100
 * 
 * @param integer $score 
 * 
 * @return string The HTML to render
 */
function drawGraph100100($score)
{
    switch ($score) {
        case ($score < -100):
            $score = 99.5;
            $score_class = 'neg';
            $plug = 0;
            $gap = 100;
            $below_range = true;
            break;
        case ($score == -100):
            $score = 99.5;
            $score_class = 'neg';
            $plug = 0;
            $gap = 100;
            break;
        case ($score > -100 and $score < 0):
            $score = abs($score);
            $score_class = 'neg';
            $plug = 100 - $score;
            $gap = 100;
            break;
        case ($score == 0):
            $score_class = 'zero';
            $plug = 100;
            $gap = 100;
            break;
        case ($score > 0 and $score < 100):
            $score_class = 'pos';
            $plug = 99.5;
            $gap = 100 - $score;
            break;
        case ($score == 100):
            $score_class = 'pos';
            $plug = 99.5;
            $gap = 0;
            break;
        case ($score > 100):
            $score = 100;
            $score_class = 'pos';
            $plug = 99.5;
            $gap = 0;
            $above_range = true;
            break;
    }
    
    // graph is 200 wide, so divide by 2 to get percentage
    $plug = $plug /2;
    $score = $score /2;
    $gap = $gap /2;
    if (($plug + $score + $gap) > 100) {
        $gap = $gap -1;
    } else if (($plug + $score + $gap) <=99) {
        $plug = $plug +1;
    }
    
    $retval = '';
    $retval .= '<div class="plug" style="width:' . $plug . '%"></div>';
    $retval .= '<div class="' . $score_class . '" style="width:' . $score . '%"></div>';
    $retval .= '<div class="gap" style="width:' . $gap . '%"></div>';
    $retval .= '<div class="zero_line"></div>';
    $retval .= '<div class="axis_labels">';
    $retval .= '  <span class="label_left">-100</span><span class="label_center">0</span><span class="label_right">100</span>';
    $retval .= '</div>';
    return $retval;
}


/**
 * Trim spaces and remove any trailing punctuation: we want consistent punctuations
 * 
 * @param string $string Possibly "dirty" string with extra spaces and punctuation
 * 
 * @return string Cleaned string suitable for putting into displayed comments
 */
function cleanText($string)
{
    $retval = trim($string);
    // Remove trailing punctuation
    $end = substr($retval, -1);
    while (in_array($end, array('.', ',', ';', '?', '!'))) {
        $retval = trim(substr($retval, 0, -1));
        $end = substr($retval, -1);
    }
    return $retval;
}

/**
 * Using current GET & POST settings, determine whether to use Kyoto-adjusted baselines or not
 * 
 * @return boolean Are KABs "on"? That is, are we using Kyoto-adjusted baselines?
 */
function kabsOn() {
    if (!isset($_GET['kab']) and !isset($_POST['kab'])) {
        // Default to KABs on
        $retval = true;
    } else {
        if ((isset($_GET['kab']) and ($_GET['kab'] == 'yes')) or (isset($_POST['kab']) and $_POST['kab'] == 'yes')) {
            // KABs are on
            $retval = true;
        } else {
            // KABs are off
            $retval = false;
        }
    }
    return $retval;
}