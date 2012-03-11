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

  <title>Glossary | Climate Equity Scorecard</title>
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
  <body class="glossary group">
    <div id="container" class="group">
    <header>
        <h1><a href="http://gdrights.org/scorecard/">Climate Equity Scorecard</a> Glossary</h1>
        <!--<p id="more_info"><a href="#">more information about climate equity scorecards</a> &#187;</p>-->
    </header>
    <div id="main" role="main" class="group">
        <!-- TODO generate this list of glossary terms from XML file -->
        <nav>
            <ul>
                <li><a href="#gloss_more_info">More information about Climate Equity Scorecards</a></li>
                <li><a href="#gloss_path">Level of Global Ambition &#8211; Global Mitigation Pathways</a></li>
                <li><a href="#gloss_pledge">Type of Pledge</a></li>
                <li><a href="#gloss_fair">Fair Share</a></li>
                <li><a href="#gloss_dom">Pledged Effort</a></li>
                <!--<li><a href="#gloss_intl">Pledged International Support</a></li>-->
                <li><a href="#gloss_gap">Pledge Gap</a></li>
                <li><a href="#gloss_bau">Business as Usual</a></li>
                <li><a href="#gloss_ei">Emissions Intensity</a></li>
                <li><a href="#gloss_more_results">More Results for This Country</a></li>
                <li><a href="#gloss_responsibility">Responsibility</a></li>
                <li><a href="#gloss_capacity">Capacity</a></li>
                <li><a href="#gloss_rci">Responsibility and Capacity Index (RCI)</a></li>
                <li><a href="#gloss_dev_threshold">Development Threshold</a></li>
            </ul>
        </nav>
        <?php
            $glossary = new HWTHelp('glossary.xml', 'def_link', 'glossary.php');
            echo $glossary->getHelpPage();
        ?>

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