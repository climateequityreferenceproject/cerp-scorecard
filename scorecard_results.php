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
    $glossary = new HWTHelp('def_link', 'glossary.php', 'sc_gloss');
}
$calc_path = getCalcPath() . '/';
//TODO store default 'results' text in Help DB where authors can edit it, pull into page on first load
$resultsDefault = '<p>How do countries&#8217; emission-reduction pledges &#8211; their international promises &#8211; compare to the efforts they should be making, their ' . $glossary->getLink('gloss_fair', true, 0) . ' of the global effort needed to limit dangerous and avoidable climate change? <strong>This is the basic question that this Scorecard tries to answer.</strong></p>
        <p>The Climate Equity Scorecard aims to express the principle of &#8220;Common but differentiated responsibilities and respective capabilities&#8221; &#8211; a keystone of global climate diplomacy &#8211; in terms of a simple but meaningful analysis of national pledges.</p>
        <p>The Scorecard represents a country&#8217;s (or group of countries&#8217;) pledge to act, relative to its fair share of the international effort that would be needed to reach an ambitious temperate-stabilization target.</p>
        <p>This calculation is based on the Greenhouse Development Rights (GDRs) effort-sharing framework. The underlying <a href="' . $calc_path . '" target="_blank">GDRs calculator</a> offers much more detail, and many more options for exploring national fair shares.</p>';

/**
 * Generate HTML to diplay bar chart and text information about pledge
 * 
 * @return string: Nicely-formatted HTML for displaying information about the pledge
 */
function getResults() 
{
    $glossary = new HWTHelp('def_link', 'glossary.php', 'sc_gloss');
    
    // Figure out if Annex 1 or not
    $is_annex_1 = array_key_exists('annex_1', getRegions($_POST['country']));
    
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
    
    if ($scoreview === 'scorebasic') {
        $display = 'basic';
    } else {
        $display = 'brackets';
    }
    // Tyler: Clean this up -- it should figure it out from the interface
    // $display = 'brackets';
    // $scoreview = 'scoreadv';

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
//    $source_intl = cleanText($pledge_info['intl_source']);

//    echo '<br />';
    $effort_array = getGdrsInformation($pledge_info, $pathway_id);
    $score = number_format($effort_array['score']); // No decimals
    $cap = niceNumber($effort_array['cap']);
    $resp = niceNumber($effort_array['resp']);
    $fair_share_perc = niceNumber($effort_array['fair_share_perc']);
    $glob_mit_req_MtCO2 = niceNumber($effort_array['glob_mit_req_MtCO2']);
//    $pledged_reduct_perc = niceNumber($effort_array['pledged_reduct_perc']);
    if ($effort_array['pledged_reduct_MtCO2'] < 0) {
        $pledge_is_negative = true;
    } else {
        $pledge_is_negative = false;
    }
//    $pledged_reduct_MtCO2 = niceNumber(abs($effort_array['pledged_reduct_MtCO2']));
    $pledge_gap_MtCO2 = niceNumber(abs($effort_array['pledge_gap_MtCO2']));
//print_r($effort_array); echo '<br /><br />';
//    $pledge_gap_perc_bau = niceNumber(abs($effort_array['pledge_gap_perc_bau']));
    $pledge_gap_as_score = number_format(abs($effort_array['score']));
    $gdrs_perc_1990 = niceNumber($effort_array['gdrs_perc_1990']);
    $pledge_perc_1990 = niceNumber($effort_array['pledge_perc_1990']);

    $iso3 = $_POST['country'];

    $condition_string = $_POST['conditional'] ? 'conditional' : 'unconditional';
    if ($score < 0) {
        $score_class = 'negative';
    } else {
        $score_class = 'positive';
    }
    
    $retval = '';
    $retval .= '<p><span class="score ' . $score_class . '">';
    $retval .= 'Score: ' . $score . '</span></p>';
    $retval .= drawScoreBar($effort_array['score'], $effort_array['bau_score'], $display); 
    
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
    $marker_pathway = $glossary->getLink('gloss_path', true, $ambition);
    // $help_bau = $glossary->getLink('gloss_bau', true, 0);
    
    // case 1 means score is negative, but pledge is between BAU and fair share (zero score)
    // case 2 means score is positive
    // case 3 means score is negative, and plege is worse than BAU, to the left of BAU on the graph
    switch ($effort_array['case']) {
        case 2:
$simple_text = <<<EOHTML
   <p>Given a $marker_pathway target, the $by_year $link_lower[gloss_mitreq] is $glob_mit_req_MtCO2 tonnes.</p>
            
       <p><span class="score $score_class">$country</span>&#8217;s $by_year $condition_string mitigation pledge
       exceeds its $link_lower[gloss_fair]
       ($fair_share_perc%) of that global requirement by $pledge_gap_MtCO2 million tonnes. This is $pledge_gap_as_score% of its $by_year
       $link_lower[gloss_bau] (BAU) emissions. Its score is therefore $score.</p>
EOHTML;
            break;
        case 1:
        case 3:
$simple_text = <<<EOHTML
   <p>Given a $marker_pathway target, the $by_year $link_lower[gloss_mitreq] is $glob_mit_req_MtCO2 tonnes.</p>
            
       <p><span class="score $score_class">$country</span>&#8217;s $by_year $condition_string mitigation pledge
       falls short of its $link_lower[gloss_fair]
       ($fair_share_perc%) of that global requirement by $pledge_gap_MtCO2 million tonnes. To close this $link_lower[gloss_gap],
       $country should raise its pledge by an additional $pledge_gap_as_score% of its $by_year
       $link_lower[gloss_bau] (BAU) emissions. Its score is therefore $score.</p>
EOHTML;
            break;
        default:
            throw new Exception('Invalid case id: ' . $effort_array['case']);
    }
    
    if ($is_annex_1) {
$annex1_text = <<<EOHTML
<p>In 1990 terms: To meet its $link_lower[gloss_mitob], $country should limit its emissions in $by_year to
    $gdrs_perc_1990% of its 1990 emissions. Its current pledge implies $by_year emissions of $pledge_perc_1990% of 1990 emissions.</p>
EOHTML;
    } else {
        $annex1_text = ''; // NOT ANNEX 1
    }
    
    if ($scoreview === 'scoreadv') {
        if ($effort_array['fund_others']) {
$net_donor_text = <<<EOHTML
 To meet its mitigation obligation, $country could either limit its own emissions, or contribute to reducing global emissions,
or act both domestically and internationally.
EOHTML;
        } else {
            $net_donor_text = '';
        }
        
        if ($source_dom) {
            $source_dom_text = '<p>Source for domestic effort: ' . $source_dom . '.</p>';
        } else {
            $source_dom_text = '';
        }
        
$detailed_text = <<<EOHTML
<p>If $country&#8217;s pledge were equal to its fair share, its score would be zero. On the scorebar above,
    $country&#8217;s fair share of $by_year global emissions reductions is expressed as the distance from its BAU to the zero point.
    Its pledge is expressed as the distance from its BAU to its score.</p>

<p>$country&#8217;s obligation is a share of a common global effort and a function of its $link_lower[gloss_capacity]
    and its $link_lower[gloss_responsibility]. $country is projected in $by_year to have
    $cap% of global capacity and $resp% of global responsibility.$net_donor_text</p>

<div id="details">
<h2>Details about this pledge</h2>
<p>$effort_array[pledge_description]</p>
$source_dom_text
<p><strong>Warning: the scores here are only meaningful if the underlying national pledges are in good faith.</strong></p>
</div><!-- end #details-->
EOHTML;
    }
    $retval .= $simple_text;
    $retval .= $annex1_text;
    $retval .= $detailed_text;
    
//    if ($score < 0) {
//$action_string = <<<FALLSSHORTTEXT
//falls short of its $link_lower[gloss_fair] by $pledge_gap_MtCO2 million tonnes. To close that $link_lower[gloss_gap], $country should raise its pledge by an additional $pledge_gap_perc_bau% of its $link_lower[gloss_bau] emissions.
//FALLSSHORTTEXT;
//    } else {
//$action_string = <<<MEETSTEXT
//meets its $link_lower[gloss_fair].
//MEETSTEXT;
//    }
//    
//    if ($pledge_is_negative) {
//        $pledged_string = _('pledged emissions is higher than its ') . $glossary->getLink('gloss_bau', true) . ' emissions by ' . $pledged_reduct_MtCO2 . ' million tonnes. This level of emissions';
//    } else {
//        $pledged_string = _('pledge to mitigate ') . $pledged_reduct_MtCO2 . ' million tonnes';
//    }
//    
//
//    if ($scoreview == 'scoreadv') {
//        $retval .= '<p><span class="score';
//        if ($score < 0) {
//            $retval .= ' negative';
//        }
//        $retval .= <<<LONGTEXT
//        ">$country</span>'s $link_lower[gloss_fair] of the global mitigation burden associated with the $ambition $marker_pathway is $fair_share_perc%. This fair share is calculated as the simple average of its share of global $link_lower[gloss_capacity] and global $link_lower[gloss_responsibility]. ($country is projected in $by_year to have $cap% of global capacity and $resp% of global responsibility.)</p>
//
//        <p>$country has pledged to do $pledged_reduct_perc% of the mitigation that would be needed, globally, to reach the $ambition marker pathway.</p>
//
//        <p>Given a $ambition target, $country&#8217;s $by_year $condition_string $pledged_string $action_string</p>
//LONGTEXT;
//    
//    $retval .= '<div id="details">';
//    $retval .= '<h2>Details about this pledge</h2>';
//    $retval .= '<p>' . $effort_array['pledge_description'];
//    
//    if ($source_dom) {
//        $retval .= '<p>Source for domestic effort: ' . $source_dom . '.</p>';            
//    }
//    if ($source_intl) {
//        $retval .= '<p class="source">Source for international support: ' . $source_intl . '.</p>';            
//    }
//    $retval .= '<p><strong>Warning: the scores here are only meaningful if the underlying national pledges are in good faith.</strong></p>';
//    $retval .= '</div><!-- end #details-->';
//        
//    } elseif ($scoreview == 'scorebasic') {
//        $retval .= '<p>Given a ' .  $ambition . ' target, <span class="score';
//        if ($score < 0) {
//            $retval .= ' negative';
//        }
//
//        $retval .= '">' . $country . '</span>&#8217;s ' . $by_year . ' ' . $condition_string;
//        $retval .= ' ' . $pledged_string . ' ' . $action_string . '</p>';
//    } else {
//        // TODO make sure nothing else needs to go here
//    }

    $retval .= '<div class="results_links"><a href="http://gdrights.org/scorecard-info/interpret-scorecard/" target="_blank">How do I interpret these scores?</a> &nbsp;|&nbsp; ';
    
    $calc_url = getCalcUrl($iso3, $by_year, $pathway_id);
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
