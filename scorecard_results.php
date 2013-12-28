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
//TODO be able to return to default state
$resultsDefault = $glossary->getHelpEntry('sc_intro');

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
    
//    if ($scoreview === 'scorebasic') {
//        $display = 'basic';
//    } else {
//        $display = 'brackets';
//    }
    
    $display = 'brackets';

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
    $source_dom = cleanText($pledge_info['source']);
    $caveat_dom = cleanText($pledge_info['caveat']);
    
    $effort_array = getGdrsInformation($pledge_info, $pathway_id);
    $score = number_format($effort_array['score']); // No decimals
    $cap = niceNumber($effort_array['cap']);
    $resp = niceNumber($effort_array['resp']);
    $fair_share_perc = niceNumber($effort_array['fair_share_perc']);
    $fair_share_MtCO2 = niceNumber($effort_array['fair_share_MtCO2']);;
    $glob_mit_req_GtCO2 = number_format($effort_array['glob_mit_req_MtCO2']/1000, 1);
    $pledge_gap_MtCO2 = niceNumber(abs($effort_array['pledge_gap_MtCO2']));
    $pledge_gap_as_score = number_format(abs($effort_array['score']));
    $gdrs_perc_1990 = niceNumber($effort_array['gdrs_perc_1990']);
    $gdrs_perc_1990_abs = abs($gdrs_perc_1990);
    $fair_share_perc_below_bau = niceNumber($effort_array['fair_share_perc_below_bau']);
    $fair_share_perc_below_1990 = niceNumber($effort_array['fair_share_perc_below_1990']);
    
    if ($fair_share_perc_below_1990 >= 0) {
        $perc_below_1990_text = "as " . $fair_share_perc_below_1990. "% reduction below national 1990 emissions";
    } else {
        $perc_below_1990_text = "as a " . -$fair_share_perc_below_1990. "% increase above national 1990 emissions";
    }
    
    // $pledge_perc_1990 = niceNumber($effort_array['pledge_perc_1990']);

    $iso3 = $_POST['country'];

    $condition_string = $_POST['conditional'] ? 'conditional' : 'unconditional';
    if ($score < 0) {
        $score_class = 'negative';
    } else {
        $score_class = 'positive';
    }
    
    if (($effort_array['score'] < -150) or ($effort_array['bau_score'] < -150)) {
        $scale_min = round(min($effort_array['score'],$effort_array['bau_score']), -2) - 50;
    } else {
        $scale_min = -150;
    }
    if ($effort_array['score'] > 100) {
        $scale_max = round($effort_array['score'], -2) + 50;
    } else {
        $scale_max = 100;
    }
    $scale_array = array('min'=>$scale_min,'max'=>$scale_max);
    foreach ($glossary->getIds() as $gloss_id) {
        $link_lower[$gloss_id] = $glossary->getLink($gloss_id, true, 0);
    }
    
    $retval = '';
    $retval .= '<p><span class="score ' . $score_class . '">';
    $retval .= 'Score: ' . $score . '</span></p>';
$score_intro_text = <<<EOHTML
    <p class="small_intro">A country&#8217;s pledge is scored against its fair share.  
        The $link_lower[gloss_score] is given in tons per capita. 
        If a country&#8217;s pledge (domestic mitigation plus international finance) equals its fair share, its score is zero. 
        If its pledge falls short of its fair share, its score is negative. 
        If its pledge surpasses its fair share, its score is positive.</p>
EOHTML;
    $retval .= $score_intro_text;
    $retval .= drawScoreBar($effort_array['score'], $effort_array['bau_score'], $display, $scale_array); 
//    $retval .= drawScoreBar($effort_array['score'], $effort_array['bau_score'], $display); 
    
    $retval .= '<input type="hidden" value=' . $scoreview . ' name="scoreview" id="scoreview" />';
    if ($scoreview == 'scorebasic') {
        $switchview = 'Show more';
        $linkview = '<p><span>Basic view</span><a href="#">Detailed view</a></p>';
    } else {
        $switchview = 'Show less';
        $linkview = '<p><a href="#">Basic view</a><span>Detailed view</span></p>';
    }
    $retval .= '<input type="submit" value="' . $switchview . '" name="switch_view" id="switch_view" />';
    $retval .= '<div id="switch_links" class="group">' . $linkview . '</div>';
    
    $marker_pathway = $glossary->getLink('gloss_path', false, $ambition);
    
    // case 1 means score is negative, but pledge is between BAU and fair share (zero score)
    // case 2 means score is positive
    // case 3 means score is negative, and plege is worse than BAU, to the left of BAU on the graph
    switch ($effort_array['case']) {
        case 2:
$simple_text = <<<EOHTML
           <p>Given a $marker_pathway target, the $link_lower[gloss_mitreq] in $by_year is $glob_mit_req_GtCO2 gigatonnes.</p>

            <p><span class="score $score_class">$country</span>&#8217;s $link_lower[gloss_fair] 
            of this $by_year global mitigation requirement is $fair_share_perc%, which is $fair_share_MtCO2 
            million tonnes. $country&#8217;s $by_year $condition_string mitigation pledge 
            exceeds its fair share of the global effort by $pledge_gap_MtCO2 million tonnes. 
                
            <p>In per-capita terms, $country&#8217;s $by_year fair share comes to YYY tonnes. Its pledge, however, is YYY 
            tonnes per person, which exceeds that fair share by YYY tonnes per person.  Its score is therefore YYY.</p>
            
EOHTML;
            break;
        case 1:
        case 3:
$simple_text = <<<EOHTML
            <p>Given a $marker_pathway target, the $link_lower[gloss_mitreq] in $by_year is $glob_mit_req_GtCO2 gigatonnes.</p>
            
            <p><span class="score $score_class">$country</span>&#8217;s $link_lower[gloss_fair] 
            of this $by_year global mitigation requirement is $fair_share_perc%, which is $fair_share_MtCO2 
            million tonnes. $country&#8217;s $by_year $condition_string mitigation pledge 
            falls short of its fair share of the global effort by $pledge_gap_MtCO2 million tonnes. 
            
            <p>In per-capita terms, $country&#8217;s $by_year fair share comes to XXX tonnes. Its pledge, however, is only                 
            XXX tonnes per person, which falls short of its fair share by XXX tones per person. Its score is therefore XXX.</p>
            
EOHTML;
            break;
        default:
            throw new Exception('Invalid case id: ' . $effort_array['case']);
    }
    
    // same cases as above, for 1990 terms
    
//    if ($is_annex_1) {
//        switch ($effort_array['case']) {
//            case 2:
//$annex1_text = <<<EOHTML
//<p>For $country to meet its $link_lower[gloss_mitob] with domestic mitigation alone, it would have to limit its $by_year emissions to $gdrs_perc_1990% of its 1990 emissions. Its current pledge implies $by_year emissions of $pledge_perc_1990% of 1990 emissions.</p>
//EOHTML;
//                break;
//            case 1:
//            case 3:
//$annex1_text = <<<EOHTML
//<p>For $country to meet its $link_lower[gloss_mitob] with domestic mitigation alone, its net $by_year emissions would have to be negative by an amount equal to $gdrs_perc_1990_abs% of its 1990 emissions. Its current pledge implies $by_year emissions of $pledge_perc_1990% of 1990 emissions.</p>
//EOHTML;
//                break;
//            default:
//                throw new Exception('Invalid case id: ' . $effort_array['case']);
//        }
//    } else {
//        $annex1_text = ''; // NOT ANNEX 1
//    }
    
    if ($scoreview === 'scoreadv') {
//        if ($effort_array['fund_others']) {
//$net_donor_text = <<<EOHTML
// To meet its mitigation obligation, $country could either limit its own emissions, or contribute to reducing global emissions,
//or act both domestically and internationally.
//EOHTML;
//        } else {
//            $net_donor_text = '';
//        }
        
        if ($source_dom) {
            $source_dom_text = '<p>Source for pledge details: ' . $source_dom . '.</p>';
        } else {
            $source_dom_text = '';
        }
        
        if ($caveat_dom) {
            $caveat_dom_text = '<p>Please note: ' . $caveat_dom . '.</p>';
        } else {
            $caveat_dom_text = '';
        }
        
$detailed_text = <<<EOHTML
        <p>Note that a country&#8217;s fair share can be expressed in various ways: as millions of tonnes, as tonnes per capita, 
        as a percent below BAU emissions, as a percent below 1990 emissions, and so on.  For example, $country&#8217;s fair share can 
        be expressed as $fair_share_MtCO2 million tonnes, as $fair_share_perc_below_bau% reduction below national BAU 
        emissions, or $perc_below_1990_text.</p>
                
        <p>In any case, a country’s fair share is relative to a common global effort, and explicitly includes
        both reductions undertaken domestically and support for reductions undertaken internationally.
        (This is how pledges are generally presented, for example including both reductions supported abroad through the Clean Development Mechanism
        as well as those undertaken at home.)</p>
        
        <p>A country&#8217;s fair share is a function of both its $link_lower[gloss_capacity] and its $link_lower[gloss_responsibility].
        $country is projected in $by_year to have $cap% of global capacity and $resp% of global responsibility. 
        To meet its $link_lower[gloss_mitob], $country could either act nationally to limit its own emissions, 
        or provide support for reducing emissions internationally, or act both nationally and internationally.</p>
        
        <p>The fair share calculated for countries with high capability and responsibility can be 
        quite large compared to national emissions, generally implying greater reductions than can be 
        cost-effectively undertaken domestically.  This reflects the practical (and ethical) imperative of 
        having high capacity and responsibility countries (like $country) support mitigation in countries with lower capability and responsibility.</p>

<div id="details">
<h2>Details about this pledge</h2>
<p>$effort_array[pledge_description]</p>
$source_dom_text
$caveat_dom_text
<div id="warnings">
<h2>Warnings</h2>
<p><strong>&#8226; These scores only reflect domestic emissions reduction pledges, for only domestic emissions reduction pledges are quantified and 
available.  Given this, most developed countries score very badly indeed.  When and if these countries make accountable 
financial pledges, their scores will improve.  For more on this, see 
<a href="http://gdrights.org/gdrs-scorecard-calculator-information/gdrs-obligations/" target="_blank">On domestic action in a global crisis</a>
</strong></p>
<p></p> 
<p><strong>&#8226; These scores are only meaningful if the underlying national pledges are in good faith, and if the policies 
needed to realize these pledges are fully implemented. This implies that all "loopholes" must be closed before these scores are meaningful.  For example, 
inflated land-use baselines (projected reference emissions levels) must not be exploited by those countries to which they are available.</strong></p>
</div><!-- end #details-->
EOHTML;
    } else {
        $detailed_text = '';
    }
    
    $retval .= $simple_text;
//    $retval .= $annex1_text;
    $retval .= $detailed_text;
    
    $retval .= '<div class="results_links"><a href="http://gdrights.org/scorecard-info/interpret-scorecard/" target="_blank">Guide to scores</a> &nbsp;|&nbsp; ';
    
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
