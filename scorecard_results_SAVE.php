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
    $pathwayIds = GDRsAPI::connection()->pathwayIds;
    $pathwayLabel = GDRsAPI::connection()->pathwayLabel;
    $params = array();

    $params['min_target_year'] = getMinTargetYear($_POST['country'], $_POST['conditional']);
    $by_year = $params['min_target_year'];
    $params['country_name'] = getCountryRegionName($_POST['country']);
    $pathway_id = $_POST['ambition'];
    $params['ambition'] = $pathwayLabel[array_search($pathway_id, $pathwayIds)];
    $ambition = $params['ambition'];

    $pledge_info = getPledgeInformation($_POST['country'], $_POST['conditional'], $params['min_target_year']);
    if (!$pledge_info) {
        return '<div id="summary"><p class="first"><span id="country_name">' . $params['country_name'] . '</span> has not made ' . ($_POST['conditional'] ? 'a conditional' : 'an unconditional') . ' pledge.</p></div>';
    }
    // Remove surrounding spaces and any ending punctuation: we have no control over this text, so clean it up a bit
    $details = cleanText($pledge_info['details']);
    $source_dom = cleanText($pledge_info['source']);
    $source_intl = cleanText($pledge_info['intl_source']);

    $effort_array = getGdrsInformation($pledge_info, $_POST['ambition']);
    $effort_val = $effort_array['dom_pledge'] + $effort_array['intl_pledge'];
    $effort = number_format($effort_val);

    $intl = niceNumber($effort_array['intl_pledge']);
    $dom = niceNumber($effort_array['dom_pledge']);
    $gap = niceNumber($effort_array['gap']);
    $pledge_over_bau = niceNumber($effort_array['pledge_over_bau']);
    $cap = niceNumber($effort_array['cap']);
    $resp = niceNumber($effort_array['resp']);
    $dom_rel_global = niceNumber($effort_array['dom_rel_global']);

    $iso3 = $_POST['country'];

    $condition_string = $_POST['conditional'] ? 'conditionally' : 'unconditionally';
    $retval = '<div id="summary">';
    $retval .= '<p class="first"><span id="country_name">' . $params['country_name'] . '</span> ';
    $retval .= 'is projected in ' . $by_year . ' to have ' . $cap . '% of global ';
    $retval .= $glossary->getLink('gloss_capacity', true) . ' and ';
    $retval .= $resp . '% of global ' . $glossary->getLink('gloss_responsibility', true) . '. ';
    if (!$effort_array['neg_pledge']) {
        $retval .= 'It has pledged ' . $condition_string . ' to do ';
        $retval .= $dom_rel_global . '% of the mitigation that would be needed, globally, ';
        $retval .= 'to reach the ' . $ambition . ' pathway. ';
        $retval .= $params['country_name'] . ', that is, ';
    } else {
        $retval .= $params['country_name'] . ' ';
    }
    $retval .= 'has pledged ' . $condition_string . ' to do ';
    if ($effort_array['neg_pledge']) {
        $retval .= 'NONE ';
    } else {
        $retval .= '<span id="commitment">' . $effort . '%</span> ';
    }
    $retval .= 'of its ' . $glossary->getLink('gloss_fair', true);
    $retval .= ' in ' . $params['min_target_year'] . ', '; 
    $retval .= 'assuming the ' . $ambition . ' pathway.';
    if ($effort_array['neg_pledge']) {
        switch ($pledge_info['quantity']) {
            case 'absolute': $quantity_text = 'total emissions'; break;
            case 'intensity': $quantity_text = 'emissions intensity'; break;
            default: $quantity_text = ''; // Shouldn't reach here
        }
        switch ($pledge_info['year_or_bau']) {
            case 'year': $yearbau_text = strval($pledge_info['rel_to_year']); break; // Actually, this is the only option that makes sense, so hope we get here!
            case 'bau': $yearbau_text = $glossary->getLink('gloss_bau', true); break;
            default: $yearbau_text = '';
        }
        $retval .= ' (It has pledged ' . $condition_string . ' to reduce ';
        $retval .= $quantity_text;
        $retval .= ' by ' . number_format($pledge_info['reduction_percent']) . '% ';
        $retval .= 'compared to ' . $yearbau_text . '. ';
        $retval .= 'However, our projections indicate that its ';
        $retval .= $glossary->getLink('gloss_bau', true);
        $retval .= ' efficiency increases would exceed this amount.)';
    }
    $retval .= '</p></div>';
    $retval .= '<div id="graph" class="group">';
    $retval .= '<img src="img/grid.gif" alt=" " />';
    $retval .= '<p id="fair-share">100% of fair share</p>';
    $retval .= drawGraph($effort_array['intl_pledge'], 'intl', $effort_array['dom_pledge'], 'dom', false);
    $retval .= '</div><!-- end #graph -->';

    $retval .= '<div id="key" class="group">';
    // $retval .= '<p><span class="intl"></span> ' . $intl . '% ' . $glossary->getLink('gloss_intl', true) . '</p>';
    $retval .= '<p><span class="dom"></span> ' . $dom . '% ' . $glossary->getLink('gloss_dom', true) . '</p>';
    if ($effort_val < 100) {
        $retval .= '<p><span class="gap"></span> ' . $gap . '% ' . $glossary->getLink('gloss_gap', true) . '</p></div><!-- end #key -->';
    }

//    $retval .= '<p class="right source"><a href="#details" class="def_link">Details about this pledge</a></p>';
    
//$retval .=    
//<<<EOT
//    <div id="details">
//        <h2>Details about this pledge</h2>
//        <p>$effort_array['pledge_description']</p>
//        <p>Source for domestic effort: $source_dom.</p>
//        <p>Warning: the scores here are only meaningful if the underlying national pledges are in good faith.</p>
//    </div>
//EOT;

//    $retval .= '<script>$("#details").dialog({ autoOpen: false })</script>';
//    $retval .= '<script>$("#details").dialog("open")</script>';
    
    $retval .= '<div id="details">';
    $retval .= '<h2>Details about this pledge</h2>';
    $retval .= '<p>' . $effort_array['pledge_description'];

    // Not reporting these details
    /*if ($details) {
        $retval .= ' This pledge assumes: ' . $details . '.</p>';
    }*/
    
    if ($source_dom) {
        $retval .= '<p>Source for domestic effort: ' . $source_dom . '.</p>';            
    }
    if ($source_intl) {
        $retval .= '<p class="source">Source for international support: ' . $source_intl . '.</p>';            
    }
    $retval .= '<p><strong>Warning: the scores here are only meaningful if the underlying national pledges are in good faith.</strong></p>';
    $retval .= '</div>';

    $retval .= '<h2><a href="what.php" target="_blank">How do I interpret these scores?</a></h2>';

    $calc_url = getCalcUrl($iso3, $pathway_id);
    $retval .= '<h2 id="more_options"><a href="' . $calc_url . '" target="_blank">';
    $retval .= 'more detailed results &#187;</a></h2>';

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