<?php
function get_glossary_array() {
    $retval = array();
    $id = 'gloss_more_info';
    $retval[$id]['label'] = 'More information about Climate Equity Scorecards';
    $retval[$id]['text'] = <<<EOHTML
            <p>Each of these &#8220;Equity Scorecards&#8221; offers a quick and easy look at one country's formal international pledge to act to stabilize the climate. This pledge is expressed as a percentage of its &#8220;fair share&#8221; of the international effort that will be needed, as calculated within the Greenhouse Development Rights effort-sharing framework. The GDRs framework takes account of the mitigation gap between the current global emissions pathway and the emergency global emissions pathway that will be needed, as well as the responsibility and capacity of all countries to contribute the finance, technology, and general effort needed to close that gap.</p>
EOHTML;
    $id = 'gloss_path';
    $retval[$id]['label'] = 'Pathway';
    $retval[$id]['text'] = <<<EOHTML
            <p>The Equity Scorecards system evaluates national pledges relative to the global effort that will be needed to achieve a target global emissions pathway. There are many such possible pathways, but for the purposes of simplicity, three are highlighted here.</p>
            <dl>
                <dt>G8</dt> <dd>Pathway based upon official G-8 statements of its common position. This pathway is insufficient to limit planetary warming to 2°C.</dd>
                <dt>IPCC Likely</dt> <dd>Pathway judged by the IPCC to be &#8220;likely&#8221; to hold total planetary warming below 2°C.</dd>
                <dt>AOSIS</dt> <dd>Pathway based upon the AOSIS negotiating position. This pathway...</dd>
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
            <p>The <a href="http://unfccc.int" target="_blank">United Nations Framework Convention on Climate Change</a>, in its Article 3.1, commits its signatories to &#8220;protect the climate system for the benefit of present and future generations of humankind, on the basis of equity and in accordance with their common but differentiated responsibilities and respective capabilities.&#8221; This implies that nations, and by extension their citizens, have obligations to act to protect the climate system. The <a href="http://gdrights.org" target="_blank">Greenhouse Development Rights</a> framework is an attempt to rigorously but transparently model those obligations, and this Equity Scorecards interface to the <a href="http://gdrights.org/calculator" target="_blank">GDRs calculator</a> is simply an interface that highlights the GDRs standard case, which calculates national &#8220;fair shares&#8221; of the necessary global action, and compares a country&#8217;s pledge to its fair share to see how it&#8217;s doing.</p>
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
            <p>Nations can act to protect the climate system in one of two ways. They can act within their own borders, and they can act by provided international support to other countries. There are of course lots of complexities involved in qualifying and quantifying actions, but at the end of the day they have this dual aspect. International support is a kind of effort that a nation makes outside its own borders. It comes in many forms, but can typically be reported either in monetary terms or in terms of tons mitigated. We don’t attempt to estimate national levels of international support, but rather take authoritative estimates of pledged support &#8220;off the shelf&#8221; as they become available.</p>
EOHTML;
    $id = 'gloss_gap';
    $retval[$id]['label'] = 'Gap';
    $retval[$id]['text'] = <<<EOHTML
            <p>The gap shown in these Equity Scorecards represents the difference between a country&#8217;s fair share of the global effort (as implied by the specified level of ambition) and its total pledge. This total pledge is the sum of its pledged domestic effort and its pledged international support. It is a national pledge gap.</p>
EOHTML;
    $id = 'gloss_bau';
    $retval[$id]['label'] = 'Business as Usual';
    $retval[$id]['text'] = <<<EOHTML
            <p>National greenhouse-gas emissions baselines (&#8220;BAU baselines&#8221;) are here based on projected emissions growth rates from McKinsey and Co&#8217;s projections (Version 2.1) applied to the most current available annual emissions data (CO2 from fossil fuels from CDIAC&#8217;s 2010 estimates). CO2 from land use is projected to remain constant at 2005 levels. Non-CO2 greenhouse gases are taken to remain in the same proportion to fossil CO2 emissions that they were in 2005.</p>
EOHTML;
    $id = 'gloss_ei';
    $retval[$id]['label'] = 'Emissions Intensity';
    $retval[$id]['text'] = <<<EOHTML
            <p>[ratio of CO2 emissions to GDP]</p>
EOHTML;
    
    return $retval;
}

if (isset($_GET['id'])) {
    $array = get_glossary_array();
    echo '<h2>' . $array[$_GET['id']]['label'] . '</h2>' . "\n";
    echo $array[$_GET['id']]['text'];
}

