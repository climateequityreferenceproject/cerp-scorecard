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

// Maintain a session cookie
session_start();

require_once 'i18n.php';
// Note: functions.php also loads GDRsAPI
require_once 'functions.php';
require_once "class/HWTHelp/HWTHelp.php";

if (!isset($glossary)) {
    $glossary = new HWTHelp('glossary.xml', 'def_link', 'glossary.php');
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
function getResults() 
{
    $glossary = new HWTHelp('glossary.xml', 'def_link', 'glossary.php');

    // Check/set basic/advanced scorecard view (short/long text)
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
    
    // Get general pathway information
    $pathwayIds = GDRsAPI::connection()->pathwayIds;
    $pathwayLabel = GDRsAPI::connection()->pathwayLabel;
    
    // Get params
    $params = array();
    $params['min_target_year'] = getMinTargetYear($_POST['country'], $_POST['conditional']);
    $conditional_or_not = $_POST['conditional'];
    $by_year = $params['min_target_year'];
    $iso3 = $_POST['country'];
    $params['country_name'] = getCountryRegionName($_POST['country']);
    $country = $params['country_name'];
    $pathway_id = $_POST['ambition'];
    $params['ambition'] = $pathwayLabel[array_search($pathway_id, $pathwayIds)];
    $ambition = $params['ambition'];

    $pledge_info = getPledgeInformation($iso3, $conditional_or_not, $by_year);

    // Remove surrounding spaces and any ending punctuation: we have no control over this text, so clean it up a bit
    // TODO cull unnecessary stuff here - TKB copied all over from earlier version for now
//    $details = cleanText($pledge_info['details']);
    $source_dom = cleanText($pledge_info['source']);
    $source_intl = cleanText($pledge_info['intl_source']);

    echo '<br />';
    if (isset($_POST['kab_score'])) {
        $kab_score = $_POST['kab_score'];
    } else if (isset($_GET['kab_score'])) {
        $kab_score = $_GET['kab_score'];
    } else {
        $kab_score = 'option1';
    }
    $pledge1 = 0.0; 
    $effort_array = getGdrsInformation($pledge_info, $pathway_id, $kab_score);
    $score_kab = niceNumber($effort_array['score_kab']);
    $score_no_kab = niceNumber($effort_array['score']);
//    $effort_val = $effort_array['dom_pledge'] + $effort_array['intl_pledge'];
//    $effort = number_format($effort_val);

//    $intl = niceNumber($effort_array['intl_pledge']);
//    $dom = niceNumber($effort_array['dom_pledge']);
//    $gap = niceNumber($effort_array['gap']);
//    $pledge_over_bau = niceNumber($effort_array['pledge_over_bau']);
    $cap = niceNumber($effort_array['cap']);
    $resp = niceNumber($effort_array['resp']);
//    $dom_rel_global = niceNumber($effort_array['dom_rel_global']);
    $fair_share_perc = niceNumber($effort_array['fair_share_perc']);
    $pledged_reduct_perc = niceNumber($effort_array['pledged_reduct_perc']);
    $pledged_reduct_MtCO2 = niceNumber($effort_array['pledged_reduct_MtCO2']);
    $pledge_gap_MtCO2 = niceNumber($effort_array['pledge_gap_MtCO2']);
    $pledge_gap_perc_bau = niceNumber($effort_array['pledge_gap_perc_bau']);

    $iso3 = $_POST['country'];

    $condition_string = $_POST['conditional'] ? 'conditional' : 'unconditional';
    
    // Collect content for output
    $retval = '<p><span class="score">Score: ' . $score_kab . '%';
    //$retval = '<p><span class="score">Score: ' . $score_kab . '%';
    $retval .= '</span>&nbsp; with baseline adjusted for Kyoto commitments</p>';
    $retval .= '<div class="graph group">';
    $retval .= drawGraph($pledge1, 'intl', $score_kab, 'kab', false);
    $retval .= '</div><!-- end .graph -->';

    $retval .= '<p><span class="score">Score: ' . $score_no_kab . '%';
    $retval .= '</span>&nbsp; with no baseline adjustment</p>';
    $retval .= '<div class="graph group">';
    $retval .= drawGraph($pledge1, 'intl', $score_no_kab, 'no_kab', false);
    $retval .= '</div><!-- end .graph -->';

    $retval .= '<div id="key" class="group">';
//    if ($effort_val < 100) {
        $retval .= '<p><span class="gap"></span> Shortfall = gap between fair share and pledge, as percentage of baseline</p>';
//    }
    $retval .= '<p><span class="dom"></span> Score = ( baseline – shortfall ) / baseline</p></div><!-- end #key -->';

    $retval .= '<br />';
    $retval .= '<input type="hidden" value=' . $scoreview . ' name="scoreview" id="scoreview" />';
    if ($scoreview == 'scorebasic') {
        $switchview = 'Show more';
    } else {
        $switchview = 'Show less';
    }
    $retval .= '<input type="submit" value="' . $switchview . '" name="switch_view" id="switch_view" />';
    
    $retval .= '<br />';
    
    
    if ($scoreview == 'scoreadv') {
        $retval .= <<<LONGTEXT
        <p><span class="score">$country</span>'s [fair share] of the global mitigation burden associated with the $ambition marker pathway is $fair_share_perc%. This fair share is calculated as the simple average of its share of global capacity and global responsibility. ($country is projected in $by_year to have $cap% of global capacity and $resp% of global responsibility.)</p>

        <p>$country has pledged to do $pledged_reduct_perc% of the mitigation that would be needed, globally, to reach the $ambition marker pathway.</p>

        <p>Given a [$ambition target], $country's $by_year $condition_string [pledge] to mitigate $pledged_reduct_MtCO2 million tonnes falls short of its fair share by $pledge_gap_MtCO2 million tonnes. To close that gap, $country should raise its pledge by an additional $pledge_gap_perc_bau% of its [business-as-usual] emissions.</p>
LONGTEXT;
    
    $retval .= '<div id="details">';
    $retval .= '<h2>Details about this pledge</h2>';
    $retval .= '<p>' . $effort_array['pledge_description'];
    
    if ($source_dom) {
        $retval .= '<p>Source for domestic effort: ' . $source_dom . '.</p>';            
    }
    if ($source_intl) {
        $retval .= '<p class="source">Source for international support: ' . $source_intl . '.</p>';            
    }
    $retval .= '<p><strong>Warning: the scores here are only meaningful if the underlying national pledges are in good faith.</strong></p>';
    $retval .= '</div>';
        
    } elseif ($scoreview == 'scorebasic') {
        $retval .= <<<SHORTTEXT
        <p>Given a [$ambition target], <span class="score">$country&#8217;s</span> $by_year $condition_string [pledge] to mitigate $pledged_reduct_MtCO2 million tonnes falls short of its fair share by $pledge_gap_MtCO2 million tonnes. To close that gap, $country should raise its pledge by an additional $pledge_gap_perc_bau% of its [business-as-usual] emissions.</p>
SHORTTEXT;
    } else {
        // TODO make sure nothing else needs to go here
    }

    $retval .= '<p><a href="what.php" target="_blank">How do I interpret these scores?</a> &nbsp;|&nbsp; ';

    $calc_url = getCalcUrl($iso3, $pathway_id);
    $retval .= '<a href="' . $calc_url . '" target="_blank">';
    $retval .= 'More detailed calculations &#187;</a></p>';
    
    //$retval .= '<br /><br />';
    //$retval .= print_r($_POST, true);
    
    return $retval;
}

/**
 * Generate HTML to diplay bar chart and text information about pledge
 * 
 * @return string: Nicely-formatted HTML for displaying information about the pledge
 */
//function getResults() {
//    $retval = print_r($_POST, true);
//    $retval .= '<br /><br />';
//    
//    if (!isset($_POST['scoreview'])) {
//        $scoreview = 'scorebasic'; 
//    }
//    elseif (!isset($_POST['switch_view'])) {
//        $scoreview = $_POST['scoreview'];
//    }
//    elseif ($_POST['scoreview'] == 'scorebasic') {
//        $scoreview = 'scoreadv';
//    }
//    else {
//        $scoreview = 'scorebasic';
//    }
//    $retval .= '<input type="hidden" value=' . $scoreview . ' name="scoreview" id="scoreview" />';
//    $retval .= '<input type="submit" value="switch view" name="switch_view" id="switch_view" />';
//    $retval .= '<br />' . $scoreview;
//    // TODO replace 'scorebasic' with a function generating basic/short results, 
//    // and replace 'scoreadv' with a function generating longer/more-info results
//    
//    return $retval;
//}

if (isset($_POST['ajax']) ) {
    if ($_POST['country']!=='none') {
        echo getResults();
    } else {
        echo $resultsDefault;
    }
}

?>
