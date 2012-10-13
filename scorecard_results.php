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
$resultsDefault = '<p>How do countries&#8217; emission-reduction pledges &#8211; their international promises &#8211; compare to the efforts they should be making, their ' . $glossary->getLink('gloss_fair', true) . ' of the global effort needed to limit dangerous and avoidable climate change? <strong>This is the basic question that this Scorecard tries to answer.</strong></p>
        
        <p>The Climate Equity Scorecard aims to express the principle of &#8220;Common but differentiated responsibilities and respective capabilities&#8221; &#8211; a keystone of global climate diplomacy &#8211; in terms of a simple but meaningful analysis of national pledges.</p>
        <p>The Scorecard represents a country&#8217;s (or group of countries&#8217;) pledge to act, relative to its fair share of the international effort that would be needed to reach an ambitious temperate-stabilization target.</p>
        <p>This calculation is based on the Greenhouse Development Rights (GDRs) effort-sharing framework. The underlying <a href="http://gdrights.org/calculator/" target="_blank">GDRs calculator</a> offers much more detail, and many more options for exploring national fair shares.';

/**
 * Generate HTML to diplay bar chart and text information about pledge
 * 
 * @return string: Nicely-formatted HTML for displaying information about the pledge
 */

// echo 'USA:' . hasUnconditionalPledge('USA');
// echo '<br /><br />';
// echo 'Brazil:' . hasUnconditionalPledge('BRA');
// echo '<br /><br />';
// echo 'China:' . hasUnconditionalPledge('CHK');
// echo '<br /><br />';
// echo 'Canada:' . hasUnconditionalPledge('CAN');
// echo '<br /><br />';

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
