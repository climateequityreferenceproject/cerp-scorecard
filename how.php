<?php
/**
 * glossary.php
 * 
 * PHP Version 5
 *
 * @package GDRsScorecard
 * @copyright 2011-2012 EcoEquity and Stockholm Environment Institute
 * @license All rights reserved
 * @link http://www.gdrights.org/
 */

require_once "functions.php";
require_once "class/HWTHelp/HWTHelp.php";
$glossary = new HWTHelp('glossary.xml', 'def_link', 'glossary.php');

?>
<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!-- Consider adding an manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <!-- Use the .htaccess and remove these lines to avoid edge case issues.
       More info: h5bp.com/b/378 -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title>What is this? And how do I interpret these scores? | Climate Equity Scorecard</title>
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

  <!-- CSS: implied media=all -->
  <!-- CSS concatenated and minified via ant build script-->
  <link rel="stylesheet" href="css/style.css">
  <!-- end CSS-->

  <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

  <!-- All JavaScript at the bottom, except for Modernizr / Respond.
       Modernizr enables HTML5 elements & feature detects; Respond is a polyfill for min/max-width CSS3 Media Queries
       For optimal performance, use a custom Modernizr build: www.modernizr.com/download/ -->
  <script src="js/libs/modernizr-2.0.6.min.js"></script>
  </head>  
  <body class="group docs">
    <div id="container" class="group">
    <header>
        <h1>What is the <a href="http://gdrights.org/scorecard/">Climate Equity Scorecard</a>?</h1>
    </header>
    <div id="main" role="main" class="group">
        
    <section>
        <h2>And how do I interpret these scores?</h2>
        <p>These Equity Scorecards are designed to clarify the equity debate by estimating national &#8220;fair shares&#8221; of the global effort that would be needed to meet an ambitious mitigation goal. They are based on the <a href="http://gdrights.org">Greenhouse Development Rights</a> framework, which straightforwardly models a global system of common but differentiated responsibilities and capabilities.</p>
        <p>The calculations behind these scores reflect <a href="http://gdrights.org/scorecard/glossary.php#gloss_bau">business-as-usual pathways</a> and <a href="http://gdrights.org/scorecard/glossary.php#gloss_paths">mitigation pathways</a> that are as well-vetted and &#8220;off the shelf&#8221; as possible. Note also that this system does not attempt to score national <em>actions</em>, but concerns itself only with national <em>pledges</em>. This is why it reports scores for 2020 alone &#8211; this is the only year for which pledges exist.</p>
        <p>If a country is doing a great deal, but chooses not to express that action as a 2020 pledge, its actions will not be reflected in its score. Similarly, if a country makes a pledge that it does not actually attempt to meet &#8211; a bad faith pledge &#8211; it will, at least for the moment, be scored as if it were in good faith.</p>

        <h3>Greenhouse Development Rights is a reference framework</h3>
        <p>The Equity Scorecards system aims to provide an easily-understood but nevertheless robust reference by which negotiated pledges can be evaluated, relative to a given level of global ambition and a fair distribution of the effort needed to realize it. We do not imagine that the &#8220;formulas&#8221; that drive our calculations will be soon accepted as the basis of a global accord. However, we do believe that our approach is about as fair as any could possibly be. (For more on this, see for example the <a href="http://www.climatenetwork.org/publication/can-fair-effort-sharing-discussion-paper">Climate Action Network&#8217;s Fair Effort Sharing Discussion paper</a>, or BASIC&#8217;s &#8220;experts paper&#8221; on <a href="http://gdrights.org/wp-content/uploads/2011/12/EASD-final.pdf">Equitable access to sustainable development</a>.) Also, Greenhouse Development Rights is designed to be extremely transparent and makes almost no limiting assumptions about institutions and mechanisms. Moreover, most all the parameterizations that define the GDRs &#8220;standard case&#8221; can be easily changed in the underlying <a href="http://gdrights.org/calculator_dev/">calculator</a> and database; sensitivity analyses are easy to do. For these reasons and others, we believe that the scores here are quite meaningful.</p>

        <h3>Why equity, and why now?</h3>
        <p>After Durban, the equity debate is more important than ever. Moreover, as the Durban Platform negotiations evolve, this is likely to be widely recognized. Despite the continued salience of the old Annexes, the global differentiation debate is long overdue, and it will inevitably implicate <a href="http://gdrights.org/scorecard/glossary.php#gloss_capacity">capacity</a> as well as <a href="http://gdrights.org/scorecard/glossary.php#gloss_responsibility">responsibility</a>. There must also be a serious engagement with the fundamental problem of &#8220;equal access to sustainable development.&#8221; Without real steps towards such equal access, we are extremely unlikely to see the ambitious multilateral mobilizations that the climate crisis demands. Equity, it turns out, is a precondition of short-term action as well as long-term ambition.</p>

        <h3>There are many equity indicators, but only a few are critical</h3>
        <p>Short para explaining that, while there are many views of equity and equity indicators, the core of the relevant debate, from the perspective of the negotiations, is defined by the CPC and RCI approaches. (PC is the limiting case &#8211; nothing that is not at least as fair as PC can reasonably be claimed as fair at all &#8211; this follows from the nature of the commons itself.) These are closely related in that XXX, but also very different in that YYY. (Question: Do any of the existing CPC variants make provision for capacity modifiers?)</p>

        <h3>When it comes to &#8220;realism,&#8221; Stringency means a great deal</h3>
        <p>Equity-based approaches to the climate transition are routinely judged to be impractical and unrealistic. It&#8217;s easy, when reviewing the allocations that GDRs and similar systems generate, to dismiss them for just these reasons. Look for example, at the United States Scorecard, then click through to the underlying <a href="http://gdrights.org/calculator_dev/">calculator</a> and examine the related Country Reports. These project GDRs allocations out to 2030. There are no 2030 pledges to evaluate, but if you&#8217;ve selected a stringent global mitigation pathway (e.g. the <em>AOSIS pathway</em>) you can see that the US allocation actually goes <em>negative</em> before 2030 is even reached. The US, in other words, is judged to be responsible for mitigating more tons of greenhouse gas than are emitted within its borders.</p>
        <p>But before you conclude that this judgment is irrelevant, do an experiment in which you reduce the ambition of the mitigation pathway. Take a look at the results you get under the far less stringent (and far more dangerous) <em>G8 pathway</em>, which reflects the stated policy of the most powerful industrialized countries. Under this pathway, the demands that GDRs makes on the US are still stringent, but not in the manner of the <em>AOSIS pathway</em>. They are only stringent when judged by the norms of the business-as-usual world, which we remain embedded in. In other word, the allocations that GDRs generates are stringent under the G8 pathway, but they are also &#8220;reasonable,&#8221; or almost reasonable, even from the perspective of the business-as-usual world.</p>
        <p>The point here is a general one. <em>All possible climate mitigation strategies become extremely challenging at high levels of ambition.</em> Thus, GDRs, considered at a high-level of ambition &#8211; our preference &#8211; is extremely challenging. So is any possible alternative. Moreover, it remains the case that equity is a precondition for engaged, sustained, global cooperation, and that without such cooperation the necessary stringency will remain unachievable. Something like GDRs, by virtue of its commitment to sustainable development rights, has at least a chance of delivering on the promise of a high-ambition climate transition.</p>

        <h3>How we assess national pledges</h3>
        <h4>Our scores are based on the Copenhagen / Cancun pledges</h4>
        <p>We do not attempt to estimate levels of domestic effort, but rather take authoritative estimates of pledged effort &#8220;off the shelf&#8221; as they become available. This raises an important caveat &#8211; the scores here are only meaningful to the extent that national pledges are made in good faith. On this matter, we attempt no judgments. Also note that in some cases a country is judged to be pledging NO effort. This is typically because it has pledged an emission intensity target that is smaller than its projected <a href="http://gdrights.org/scorecard/glossary.php#gloss_bau">business-as-usual</a> rate of intensity improvement.</p>
        <h4>Conditional and Unconditional pledges are treated separately</h4>
        <p>Many countries have expressed their national emissions mitigation pledges in the form of an <em>unconditional</em> pledge and a <em>conditional</em> pledge. The unconditional pledge is of lower ambition, but will be met unilaterally. The conditional pledge is higher ambition, but is conditional on other countries satisfying certain requirements relating to increasing their own ambition with respect to mitigation pledges and/or the provision of finance and technology.</p>
        <h4>International pledges (such as they are) are not considered in this analysis</h4>
        <p>International effort will be essential in any successful climate regime, but to our knowledge, no nations have yet made international pledges that are precise enough to score. This is because the scores here reflect effort that is pledged for the year 2020, not (cumulative) effort that is pledged before 2020. The situation on this front will evolve in the years ahead. If and when it clarifies to the necessary point, international efforts will be added to these scores.</p>
        <h4>We judge some countries to be pledging no action at all</h4>
        <p>For example, we judge India to be making essentially no pledge at all. To be sure, India may well be planning to do more, from the perspective of domestic policy and even NAMAs, than it is actually pledging to do. The issue here is that, for whatever reasons, the Indians have not chosen to make higher pledges. No such reasons are considered by the Equity Scorecards system. It simply evaluates the national pledges of record.</p>

        <h2>Walk-through of specific cases</h2>
        <p>To really see what&#8217;s at stake in the scores that we are assigning, it&#8217;s best to spend a few minutes experimenting with the overall Equity Scorecards system, and to take a good look at the underlying Greenhouse Development Rights calculator.</p>
        <p>Keep in mind the overall logic of the GDRs approach, which is designed to calculate national fair shares of a global effort. Here, the effort in question is the <a href="#">global mitigation requirement</a>, though it could as well be the global adaptation requirement. A nation&#8217;s &#8220;fair share&#8221; of this global effort based on its <a href="http://gdrights.org/scorecard/glossary.php#gloss_rci">Responsibility and Capacity Index</a> or RCI &#8211; the more <a href="http://gdrights.org/scorecard/glossary.php#gloss_responsibility">responsibility</a> a country has (relative to the global total), or the more <a href="http://gdrights.org/scorecard/glossary.php#gloss_capacity">capacity</a>, the greater its share on the global RCI.</p>
        <p>The RCI is thus disjoint from <a href="#">domestic emissions</a>. Typically, when the GDRs model is run relative to a stringent global mitigation pathway, wealthy countries are found to have mitigation obligations that are greater than their physical domestic emissions. Typically, developing countries have more mitigation potential then they have obligation to mitigate.</p>
        
        <h3>Developed country examples</h3>
        <ul>
            <li>A representative developed country that is under-pledged. E.g. the US. Point out that a developed country&#8217;s obligation may be greater than its domestic emissions</li>
            <li>A representative developed country that is over-pledged. E.g. Iceland / XXX. Point out that a developed country&#8217;s obligation may be greater than its domestic emissions.</li>
        </ul>
        <h3>Developing country examples</h3>
        <ul>
            <li>First, show a country that has made essentially pledged no action. E.g. India. Explain how this happens, with an example that compares India&#8217;s pledge to its BAU.</li>
            <li>Second, show a country that has pledged to meet part of its obligation. E.g. Mexico / AOSIS.</li>
            <li>Third, show how that same country&#8217;s pledge can more than exceed its obligation at a lower level of stringency. E.g. Mexico / G8.</li>
            <li>Fourth, show a Costa Rica, a country that is pledging more than its obligation even at the AOSIS level, and far, far more at the less-stringent G8 level.</li>
            <li>Finally, show China.</li>
        </ul>
        <h2>Authors and programmers. And funders? And what about Kirk?</h2>
        <p>This doc should identify the authors. Eric &amp; Tyler need special credit for programming and design.</p>
    </section>
        
    </div> <!--! end of #main -->
    <?php include_once ("footer.php"); ?>
  </div> <!--! end of #container -->


  <!-- JavaScript at the bottom for fast page loading -->

  <!-- scripts concatenated and minified via ant build script-->
  <!-- end scripts-->

	
  <!-- Change UA-XXXXX-X to be your site's ID -->
  <script>
    window._gaq = [['_setAccount','UAXXXXXXXX1'],['_trackPageview'],['_trackPageLoadTime']];
    Modernizr.load({
      load: ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js'
    });
  </script>


  <!-- Prompt IE 6 users to install Chrome Frame. Remove this if you want to support IE 6.
       chromium.org/developers/how-tos/chrome-frame-getting-started -->
  <!--[if lt IE 7 ]>
    <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
    <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
  <![endif]-->
  </body>

</html>