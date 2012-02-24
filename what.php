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

require_once "class/HWTHelp/HWTHelp.php";

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
        <h1>What is the Climate Equity Scorecard?</h1>
        <h2>And how do I interpret these scores?</h2>
    </header>
    <div id="main" role="main" class="group">
        
        <section>

        <h2>Big picture</h2>

        <h3>Why Equity, and why now?</h3>
        <p>Short para explaining why equity is particularly important now, in the post-Durban period. This para should talk about equity and early action as well as equity as a precondition for high ambition in the long term.</p>

        <h3>Actually, there are few relevant equity indicators</h3>
        <p>Short para explaining that, while there are many views of equity and equity indicators, the core of the relevant debate, from the perspective of the negotiations, is defined by the CPC and RCI approaches. These are closely related in that XXX, but also very different in that YYY. (Question: Do any of the existing CPC variants make provision for capacity modifiers?)</p>

        <h3>Stringency means a lot</h3>
        <p>The scores reported here will strike some as unrealistic. Do note that much of their severity derives from the stringency of target pathways. Compare the results for the AOSIS and G8 pathways, and see for yourself.</p>
        <p>We strongly believe that the risk of climate catastrophe should be minimized, and that the AOSIS pathway is the best of the options here.  Note also that the underlying system models still more stringent options.</p>

        <h3>We judge some countries to pledging no action at all</h3>
        <p>For example, India, which we judge to be making essentially no pledge at all. To be sure, it may well be planning to do more, from the perspective of domestic policy and even NAMAs, than it is pledging to do. The issue here is that negotiating dynamics are seen by the Indians as a disincentive to higher pledges. We have ideas about that this implies, but really this problem is above our pay grades. We’re just comparing pledges.</p>

        <h3>This is just a reference framework</h3>
        <p>The goal here is to provide a reference by which negotiated pledges can be evaluated, relative to a given level of ambition and a fair global distribution of the effort needed to realize that ambition.  We do not imagine that the “formulas” that drive our calculations will be soon accepted as the basis of a global accord.  However, we believe that the approach that underlies these scorecards is about as fair as any could possibly be. Also, it is extremely transparent and makes almost no limiting assumptions about institutions and mechanisms. (Click though to the underlying Calculator to see for yourself). For these reasons and others, the scores here are quite meaningful.</p>
        </section>
        
        <section>

        <h2>Walk-through of specific cases</h2>
        <ul>
            <li>We’d very much like to be able to include “Pledged international support” in this scorecard, but no country, to our knowledge, has made a meaningful international pledge. Explain why this is true.</li>
            <li>Remember, a country’s obligation corresponds to its share of the global RCI – it is a share of the total global mitigation obligation. This number is quite disjoint from its domestic emissions. Remember, in the GDRs system a country also has a share of the global adaptation obligation.</li>
            <li>Typically, relative to stringent global mitigation pathways, development countries have mitigation obligations greater than their domestic emissions.  Typically, developing countries have more mitigation potential then they have obligation to mitigate.</li>
        </ul>

        <h3>Developed country examples</h3>
        <ul>
            <li>A representative developed country that is under-pledged. E.g. the US. Point out that a developed country’s obligation may be greater than its domestic emissions</li>
            <li>A representative developed country that is over-pledged. E.g. Iceland / G8. Point out that a developed country’s obligation may be greater than its domestic emissions.</li>
        </ul>

        <h3>Developing country examples</h3>
        <ul>
            <li>First, show a country that has made essentially pledged no action. E.g. India. Explain how this happens, with an example that compares India’s pledge to its BAU.</li>
            <li>Second, show a country that has pledged to meet part of its obligation. E.g. Mexico / AOSIS.</li>
            <li>Third, show how that same country’s pledge can more than exceed its obligation at a lower level of stringency. E.g. Mexico / G8.</li>
            <li>Fourth, show a Costa Rica, a country that is pledging more than its obligation even at the AOSIS level, and far, far more at the less-stringent G8 level.</li>
            <li>Finally, show China.</li>
        </ul>        
    </div> <!--! end of #main -->
    <footer>

    </footer>
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