<?php
/**
 * scorecard_results.php
 * 
 * PHP Version 5
 *
 * @package GDRsScorecard
 * @copyright 2011-2012 EcoEquity and Stockholm Environment Institute
 * @license All rights reserved
 * @link http://www.gdrights.org/
 */

session_start();
require_once('i18n.php');
require_once 'functions.php';
require_once "class/HWTHelp/HWTHelp.php";

if (!isset($glossary)) {
    $glossary = new HWTHelp('glossary.xml', 'def_link', 'glossary.php');
}

function resultsTest() 
{
    $retval = print_r($_POST, true);
    $retval .= '<br /><br />';
    
    if (!isset($_POST['scoreview'])) {
        $scoreview = 'scorebasic'; 
    }
    elseif (!isset($_POST['switch_view'])) {
        $scoreview = $_POST['scoreview'];
    }
    elseif ($_POST['scoreview'] == 'scorebasic') {
        $scoreview = 'scoreadv';
    }
    else {
        $scoreview = 'scorebasic';
    }
    $retval .= '<input type="hidden" value=' . $scoreview . ' name="scoreview" id="scoreview" />';
    $retval .= '<input type="submit" value="switch view" name="switch_view" id="switch_view" />';
    $retval .= '<br />' . $scoreview . '<br /><br />';

    
    $pledge1 = 0.0; 
    //$score_kab = $effort_array['kab_pledge']; // TODO make this real
    $score_kab = 30.0; // placeholder for testing
    //$score_no_kab = $effort_array['no_kab_pledge']; // TODO make this real
    $score_no_kab = 40.0; // placeholder for testing
 
    $glossary = new HWTHelp('glossary.xml', 'def_link', 'glossary.php');
    $pathwayIds = GDRsAPI::connection()->pathwayIds;
    $pathwayLabel = GDRsAPI::connection()->pathwayLabel;
    $params = array();

    $params['min_target_year'] = getMinTargetYear($_POST['country'], $_POST['conditional']);
    $conditional_or_not = $_POST['conditional'];
    $by_year = $params['min_target_year'];
    $params['country_name'] = getCountryRegionName($_POST['country']);
    $country = $params['country_name'];
    $pathway_id = $_POST['ambition'];
    $params['ambition'] = $pathwayLabel[array_search($pathway_id, $pathwayIds)];
    $ambition = $params['ambition'];

    $pledge_info = getPledgeInformation($_POST['country'], $_POST['conditional'], $params['min_target_year']);
    
    
    $retval .= '<p><span class="score">Score: ' . $score_kab;
    $retval .= '</span>&nbsp; with baseline adjusted for Kyoto commitments</p>';
    $retval .= '<div class="graph group">';
    $retval .= drawGraph($pledge1, 'intl', $score_kab, 'kab', false);
    $retval .= '</div><!-- end .graph -->';

    $retval .= '<p><span class="score">Score: ' . $score_no_kab;
    $retval .= '</span>&nbsp; with no baseline adjustment</p>';
    $retval .= '<div class="graph group">';
    $retval .= drawGraph($pledge1, 'intl', $score_no_kab, 'no_kab', false);
    $retval .= '</div><!-- end .graph -->';

    $retval .= '<div id="key" class="group">';
    if ($effort_val < 100) {
        $retval .= '<p><span class="gap"></span> Shortfall = gap between fair share and pledge, as percentage of baseline</p>';
    }
    $retval .= '<p><span class="dom"></span> Score = ( baseline – shortfall ) / baseline</p></div><!-- end #key -->';
    if ($scoreview == 'scoreadv') {
        $retval .= <<<LONGTEXT
        <p><span class="score">$country</span>'s [fair share] of the global mitigation burden associated with the $ambition marker pathway is _fair_share%. This fair share is calculated as the simple average of its share of global capacity and global responsibility. ($country is projected in $by_year to have _capacity_share% of global capacity and _responsibility_share% of global responsibility.)</p>

        <p>$country has pledged to do _pledge_percent% of the mitigation that would be needed, globally, to reach the $ambition marker pathway.</p>

        <p>Given a [$ambition target], $country's $by_year _conditional_or_not [pledge] to mitigate _pledge_tons tons falls short of its fair share by _pledge_gap_tons tons. To close that gap, $country should raise its pledge by an additional _pledge_gap_percent% of its [business-as-usual] emissions.</p>
LONGTEXT;
    } elseif ($scoreview == 'scorebasic') {
        $retval .= <<<SHORTTEXT
        <p>Given a [$ambition target], <span class="score">$country&#8217;s</span> $by_year _conditional_or_not [pledge] to mitigate _pledge_tons tons falls short of its fair share by _pledge_gap_tons tons. To close that gap, $country should raise its pledge by an additional _pledge_gap_percent% of its [business-as-usual] emissions.</p>
SHORTTEXT;
    } else {
        
    }
    
    return $retval;
}


$resultsDefault = '<p>How do countries&#8217; emission-reduction pledges &#8211; their international promises &#8211; compare to the efforts they should be making, their ' . $glossary->getLink('gloss_fair', true) . ' of the global effort needed to limit dangerous and avoidable climate change? <strong>This is the basic question that this Scorecard tries to answer.</strong></p>
        
        <p>The Climate Equity Scorecard aims to express the principle of &#8220;Common but differentiated responsibilities and respective capabilities&#8221; &#8211; a keystone of global climate diplomacy &#8211; in terms of a simple but meaningful analysis of national pledges.</p>
        <p>The Scorecard represents a country&#8217;s (or group of countries&#8217;) pledge to act, relative to its fair share of the international effort that would be needed to reach an ambitious temperate-stabilization target.</p>
        <p>This calculation is based on the Greenhouse Development Rights (GDRs) effort-sharing framework. The underlying <a href="http://gdrights.org/calculator/" target="_blank">GDRs calculator</a> offers much more detail, and many more options for exploring national fair shares.';

/**
 * Generate HTML to diplay bar chart and text information about pledge
 * 
 * @return string: Nicely-formatted HTML for displaying information about the pledge
 */
function getResults() {
    $retval = print_r($_POST, true);
    $retval .= '<br /><br />';
    
    if (!isset($_POST['scoreview'])) {
        $scoreview = 'scorebasic'; 
    }
    elseif (!isset($_POST['switch_view'])) {
        $scoreview = $_POST['scoreview'];
    }
    elseif ($_POST['scoreview'] == 'scorebasic') {
        $scoreview = 'scoreadv';
    }
    else {
        $scoreview = 'scorebasic';
    }
    $retval .= '<input type="hidden" value=' . $scoreview . ' name="scoreview" id="scoreview" />';
    $retval .= '<input type="submit" value="switch view" name="switch_view" id="switch_view" />';
    $retval .= '<br />' . $scoreview;
    // TODO replace 'scorebasic' with a function generating basic/short results, 
    // and replace 'scoreadv' with a function generating longer/more-info results
    
    return $retval;
}

if (isset($_POST['ajax']) ) {
    if ($_POST['country']!=='none') {
        echo getResults($_POST, $pathwayIds, $pathwayLabel);
    } else {
        echo $resultsDefault;
    }
}

?>
