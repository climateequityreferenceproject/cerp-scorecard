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
$resultsDefault = '<p>How do countries&#8217; emission-reduction pledges &#8211; their international promises &#8211; compare to the efforts they should be making, their ' . $glossary->getLink('gloss_fair', true, 0) . ' of the global effort needed to limit dangerous and avoidable climate change? <strong>This is the basic question that this Scorecard tries to answer.</strong></p>
        <p>The Climate Equity Scorecard aims to express the principle of &#8220;Common but differentiated responsibilities and respective capabilities&#8221; &#8211; a keystone of global climate diplomacy &#8211; in terms of a simple but meaningful analysis of national pledges.</p>
        <p>The Scorecard represents a country&#8217;s (or group of countries&#8217;) pledge to act, relative to its fair share of the international effort that would be needed to reach an ambitious temperate-stabilization target.</p>
        <p>This calculation is based on the Greenhouse Development Rights (GDRs) effort-sharing framework. The underlying <a href="http://gdrights.org/calculator_dev/" target="_blank">GDRs calculator</a> offers much more detail, and many more options for exploring national fair shares.';

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

    //$help_entries = $HWTHelp->getEntries();

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

//    echo '<br />';
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
    
    $retval = '';
//    // Collect content for output
//    $retval .= '<p><span class="score">Score: ' . $score_kab . '%';
//    //$retval = '<p><span class="score">Score: ' . $score_kab . '%';
//    $retval .= '</span>&nbsp; with baseline adjusted for Kyoto commitments</p>';
//    $retval .= '<div class="graph group">';
//    $retval .= drawGraph($pledge1, 'intl', $score_kab, 'kab', false);
//    $retval .= '</div><!-- end .graph -->';
//
//    $retval .= '<p><span class="score">Score: ' . $score_no_kab . '%';
//    $retval .= '</span>&nbsp; with no baseline adjustment</p>';
//    $retval .= '<div class="graph group">';
//    $retval .= drawGraph($pledge1, 'intl', $score_no_kab, 'no_kab', false);
//    $retval .= '</div><!-- end .graph -->';
//
//    $retval .= '<div id="key" class="group">';
////    if ($effort_val < 100) {
//        $retval .= '<p><span class="gap"></span> Shortfall = gap between fair share and pledge, as percentage of baseline</p>';
////    }
//    $retval .= '<p><span class="dom"></span> Score = ( baseline – shortfall ) / baseline</p></div><!-- end #key -->';
//
//    $retval .= '<br />';
//
//    // -50 to 50 graph
//    $score_adj = round($score_no_kab - 100);
//    $retval .= '<p><span class="score">Score: ' . $score_adj;
//    $retval .= '<div class="graph group" id="fifty_fifty">';
//    $retval .= drawGraph5050($score_adj);
//    $retval .= '</div><!-- end .graph -->';
//    $retval .= '<p>Left end is -50, line in middle is 0, right end is +50.</p>';
//    $retval .= '<br />';
    
    // -100 to 100 graph
    //$kab_choice = 'yes';
//    if (isset($_GET['kab'])) {
//        $kab_choice = $_GET['kab'];
//    }
//    if (isset($_POST['kab'])) {
//        $kab_choice = $_POST['kab'];
//    }
    
    switch ($kab_choice) {
         case 'yes':
            $score_adj = round($score_kab - 100);
            break;
        case 'no':
        default:
            $score_adj = round($score_no_kab - 100);
            break;
    }
    $retval .= '<p><span class="score';
    if ($score_adj < 0) {
        $retval .= ' negative';
    }
    $retval .= '">Score: ' . $score_adj . '</span>';
    $retval .= '<div class="graph group" id="fifty_fifty">';
    $retval .= drawGraph100100($score_adj);
    $retval .= '</div><!-- end .graph -->';
    
    $retval .= '<input type="hidden" value=' . $scoreview . ' name="scoreview" id="scoreview" />';
    if ($scoreview == 'scorebasic') {
        $switchview = 'Show more';
    } else {
        $switchview = 'Show less';
    }
    $retval .= '<input type="submit" value="' . $switchview . '" name="switch_view" id="switch_view" />';
    
    foreach ($glossary->getIds() as $gloss_id) {
        $link_lower[$gloss_id] = $glossary->getLink($gloss_id, true, 0);
    }
    $marker_pathway = $glossary->getLink('gloss_path', true, 'marker pathway');
    // $help_bau = $glossary->getLink('gloss_bau', true, 0);
    
    if ($pledge_gap_MtCO2 > 0) {
$action_string = <<<FALLSSHORTTEXT
falls short of its $link_lower[gloss_fair] by $pledge_gap_MtCO2 million tonnes. To close that $link_lower[gloss_gap], $country should raise its pledge by an additional $pledge_gap_perc_bau% of its $link_lower[gloss_bau] emissions.
FALLSSHORTTEXT;
    } else {
$action_string = <<<MEETSTEXT
meets its $link_lower[gloss_fair].
MEETSTEXT;
    }
    

    if ($scoreview == 'scoreadv') {
        $retval .= '<p><span class="score';
        if ($score_adj < 0) {
            $retval .= ' negative';
        }
        $retval .= <<<LONGTEXT
        ">$country</span>'s $link_lower[gloss_fair] of the global mitigation burden associated with the $ambition $marker_pathway is $fair_share_perc%. This fair share is calculated as the simple average of its share of global $link_lower[gloss_capacity] and global $link_lower[gloss_responsibility]. ($country is projected in $by_year to have $cap% of global capacity and $resp% of global responsibility.)</p>

        <p>$country has pledged to do $pledged_reduct_perc% of the mitigation that would be needed, globally, to reach the $ambition marker pathway.</p>

        <p>Given a $ambition target, $country&#8217;s $by_year $condition_string pledge to mitigate $pledged_reduct_MtCO2 million tonnes $action_string</p>
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
    $retval .= '</div><!-- end #details-->';
        
    } elseif ($scoreview == 'scorebasic') {
        $retval .= '<p>Given a ' .  $ambition . ' target, <span class="score';
        if ($score_adj < 0) {
            $retval .= ' negative';
        }

        $retval .= '">' . $country . '</span>&#8217;s ' . $by_year . ' ' . $condition_string;
        $retval .= ' pledge to mitigate ' .  $pledged_reduct_MtCO2 . ' million tonnes ' . $action_string . '</p>';
    } else {
        // TODO make sure nothing else needs to go here
    }

    $retval .= '<div class="results_links"><a href="what.php" target="_blank">How do I interpret these scores?</a> &nbsp;|&nbsp; ';

    $calc_url = getCalcUrl($iso3, $pathway_id);
    $retval .= '<a href="' . $calc_url . '" target="_blank">';
    $retval .= 'More detailed calculations &#187;</a></div>';
    
    return $retval;
}


if (isset($_POST['ajax']) ) {
    if ($_POST['country']!=='none') {
        echo getResults();
    } else {
        echo $resultsDefault;
    }
}

?>
