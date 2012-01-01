<?php
/**
 * index.php
 * 
 * PHP Version 5
 *
 * @copyright 2011-2012 EcoEquity and Stockholm Environment Institute
 * @license All rights reserved
 * @link http://www.gdrights.org/
 */

/**
 * Get an array of glossary entries
 * 
 * @return array An array of glossary entries
 * 
 * @todo Replace this with a class and appropriate methods
 * @todo Read glossary from a file (e.g., XML) or database
 */
function getGlossaryArray()
{
    $retval = array();
    $id = 'gloss_more_info';
    $retval[$id]['label'] = 'More information about Climate Equity Scorecards';
    $retval[$id]['text'] = <<<EOHTML
            <p>Each &#8220;Equity Scorecard&#8221; offers a quick look at one country&#8217;s formal international pledge to act to stabilize the global climate. This pledge is compared to the country&#8217;s &#8220;fair share&#8221; of the international effort that will be needed to close the <em>mitigation gap</em> between the current global emissions pathway and a <em>global mitigation pathway</em>. The country&#8217;s fair share is calculated within the <a href="http://gdrights.org" target="_blank">Greenhouse Development Rights (GDRs)</a> effort-sharing framework, as based on the <em>responsibility</em> and <em>capacity</em> of all countries to contribute the finance, technology, and general effort to the common global effort to stabilize the climate. At any point, the user may click though to the underlying calculator to examine and adjust a number of parameters.</p>
            <p><strong>This system is still under development.</strong></p>
EOHTML;
    $id = 'gloss_path';
    $retval[$id]['label'] = 'Level of Global Ambition';
    $retval[$id]['text'] = <<<EOHTML
            <p>The Equity Scorecards system evaluates national pledges relative to the global effort that will be needed to achieve a target global emissions pathway. There are many such possible pathways, but for the purposes of simplicity, three are highlighted here.</p>
            <dl>
                <dt>AOSIS</dt> <dd>This, our high-ambition reference pathway, is based upon the negotiating position of the Small Island States. Note that, if you click through to the underlying <a href="http://gdrights.org/calculator" target="_blank">Greenhouse Development Rights Calculator</a>, you will find that it supports the even more ambitious &#8220;Hansen-style 350 ppm&#8221; pathway.</dd>
                <dt>BASIC Experts</dt> <dd>This, our moderate-ambition reference pathway, is based upon the carbon budget which the &#8220;BASIC Experts&#8221; group (researchers from Brazil, South Africa, India and China) used in their common report on <a href="http://gdrights.org/wp-content/uploads/2011/12/EASD-final.pdf" target="_blank">Equitable Access to Sustainable Development</a> (released in Durban on Dec 3, 2011).</dd>
                <dt>G8</dt> <dd>This, our low-ambition reference pathway, is based upon official G-8 statements of its common mitigation goals. This pathway is supported by various kinds of self-styled &#8220;realists&#8221; but is insufficient to limit planetary warming to 2°C.</dd>
            </dl>
EOHTML;
    $id = 'gloss_pledge';
    $retval[$id]['label'] = 'Type of Pledge';
    $retval[$id]['text'] = <<<EOHTML
            <p>As countries have tabled their national emissions mitigation pledges with the UNFCCC, a pattern has emerged. In it, countries make both unconditional pledges &#8211; which they commit to meet, come what may &#8211; and unconditional pledges that represent their willingness to act in concert with others.</p>
EOHTML;
    $id = 'gloss_fair';
    $retval[$id]['label'] = 'Fair Share';
    $retval[$id]['text'] = <<<EOHTML
            <p>The <a href="http://unfccc.int" target="_blank">United Nations Framework Convention on Climate Change</a>, in its Article 3.1, commits its signatories to &#8220;protect the climate system for the benefit of present and future generations of humankind, on the basis of <strong>equity</strong> and in accordance with their <strong>common but differentiated responsibilities and respective capabilities</strong>.&#8221; This implies that nations, and by extension their citizens, have obligations to act to protect the climate system. The <a href="http://gdrights.org" target="_blank">Greenhouse Development Rights</a> framework is an attempt to rigorously but transparently model those obligations, and this Equity Scorecards interface to the <a href="http://gdrights.org/calculator" target="_blank">GDRs calculator</a> is simply an interface that highlights the GDRs standard case, which calculates national &#8220;fair shares&#8221; of the necessary global action, and compares a country&#8217;s pledge to its fair share to see how it&#8217;s doing.</p>
            <p>Do note that, from any scorecard, you are able to &#8220;click through&#8221; to the underlying calculator, which allows you to modify most of the parameters in the standard case.</p>
EOHTML;
    $id = 'gloss_dom';
    $retval[$id]['label'] = 'Pledged Domestic Effort';
    $retval[$id]['text'] = <<<EOHTML
            <p>Nations can act to protect the climate system in one of two ways. They can act within their own borders, and they can act by providing international support to other countries. There are of course lots of complexities involved in qualifying and quantifying actions, but at the end of the day they have this dual aspect. Domestic effort is that effort which a nation makes within its own borders. We do not attempt to estimate levels of domestic effort, but rather take authoritative estimates of pledged effort &#8220;off the shelf&#8221; as they become available.</p>
EOHTML;
    $id = 'gloss_intl';
    $retval[$id]['label'] = 'Pledged International Support';
    $retval[$id]['text'] = <<<EOHTML
            <p>Nations can act to protect the climate system in one of two ways. They can act within their own borders, and they can act by providing international support to other countries. There are of course lots of complexities involved in qualifying and quantifying actions, but at the end of the day they have this dual aspect. International support is a kind of effort that a nation makes outside its own borders. It comes in many forms but can typically be reported either in monetary terms or in terms of tons mitigated. We don’t attempt to estimate national levels of international support, but rather take authoritative estimates of pledged support &#8220;off the shelf&#8221; as they become available.</p>
EOHTML;
    $id = 'gloss_gap';
    $retval[$id]['label'] = 'Pledge Gap';
    $retval[$id]['text'] = <<<EOHTML
            <p>The gap shown in these Equity Scorecards represents the difference between a country&#8217;s <em>fair share</em> of the global effort (as implied by the specified level of ambition) and its total pledge. This total pledge is the sum of its <em>pledged domestic effort</em> and its <em>pledged international support</em>. Today, unfortunately, most countries cannot be taken as having pledged any international support. It is, precisely, a national pledge gap.</p>
EOHTML;
    $id = 'gloss_bau';
    $retval[$id]['label'] = 'Business as Usual';
    $retval[$id]['text'] = <<<EOHTML
            <p>National greenhouse-gas emissions baselines (&#8220;BAU baselines&#8221;) are here based on projected emissions growth rates from McKinsey and Co&#8217;s projections (Version 2.1) applied to the most current available annual emissions data (CO2 from fossil fuels from CDIAC&#8217;s 2010 estimates). CO2 from land use is projected to remain constant at 2005 levels. Non-CO2 greenhouse gases are taken to remain in the same proportion to fossil CO2 emissions that they were in 2005.</p>
EOHTML;
    $id = 'gloss_ei';
    $retval[$id]['label'] = 'Emissions Intensity';
    $retval[$id]['text'] = <<<EOHTML
            <p>Emissions Intensity is a measure of the efficiency of a country&#8217;s economic activity, in terms of greenhouse gas emitted per unit of economic output.  It is defined as greenhouse-gas emissions divided by GDP, and can be calculated either in CO2-only terms or in terms of &#8220;all greenhouse gases.&#8221;</p>
EOHTML;
    
    return $retval;
}

if (isset($_GET['id'])) {
    $array = getGlossaryArray();
    
    echo json_encode($array[$_GET['id']]);
}

