<?php
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

  <title>Warning about Pledges | Climate Equity Scorecard</title>
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
        <h1>Warning: We&#8217;re not scoring national actions, but only national &#8220;pledges&#8221; to act. Call them promises.</h1>
    </header>
    <div id="main" role="main" class="group">
        
    <section>

        <p>The Scorecards system does not attempt to evaluate national actions. How could it, when the actions under scrutiny here are actions to be made in the future? The scores here apply to the &#8220;pledges&#8221; of action that nations have made in the international climate negotiations. Think of them as public promises.</p>
        
        <h2>There are lots of different cases here</h2>
        
        <p>Some countries (like China) have made extremely ambitious pledges, and by all account intend to do their best to fulfill them. Other countries (like the US) are making pledges that seem adequate, though only barely, and only when evaluated against dangerously weak global emissions pathways. (The Scorecards system does not itself support such weak pathways, but if you <a href="http://gdrights.org/calculator_dev/">click through to the underlying Calculator</a>, you can easily explore their implications).</p>
        
        <p>Also, a country may be doing, or planning to do, more that it&#8217;s willing to pledge. This is an important case because it appears to describe India, which from the perspective of domestic policy and even NAMAs, appears to be planning actions that are more ambitious than it is willing to pledge.</p>
        
        <h2>Countries have only made pledges for 2020</h2>
        
        <p>The scores here are for 2020. At the time of this writing, this is only eight years out, and the level of ambition that a country (or its elites) are willing to pledge in 2020 does not necessarily represent its plans &#8211; or hopes &#8211; for the future. Nor does it necessarily represent its obligation to act &#8211; its &#8220;fair share&#8221; &#8211; in the longer term. You can see this if you ask the Scorecard for [More Detail], which will supplement the 2020 &#8220;score bar&#8221; with a country-specific &#8220;time series&#8221; graph that goes out to 2030. In any case, we can only show 2020 pledges because they&#8217;re the only pledges that exist.</p>
        
        <h2>We say nothing about the fraction of a country&#8217;s action that&#8217;s &#8220;at home&#8221;</h2>
        
        <p>The Greenhouse Development Rights framework does not have anything to say about how much mitigation a country (or region) does within its own borders. It rather assigns each country a share of the <em>global</em> mitigation burden &#8211; the amount of mitigation that must be done if the target global pathway is to be achieved. In the GDRs world, a country is doing its &#8220;fair share&#8221; if its domestic and international efforts sum to at least equal its national mitigation obligation. From a global efficiency perspective, this works because countries are free to discharge their obligations wherever they can. The assumption is that this happens in a transparent and honest manner, which is a challenge, but one we&#8217;ll have to face in any case.</p>
        
        <h2>International pledges (such as they are) are not yet considered in these scores</h2>
        
        <p>International effort will be essential in any successful climate regime, but to our knowledge, no nations have yet made international pledges that are precise enough to score. This is because the scores here reflect effort that is pledged <em>for the year 2020</em>, not (cumulative) effort that is pledged <em>before 2020</em>. The situation on this front will evolve in the years ahead. If and when it clarifies to the necessary point, international efforts will be added to these scores.</p>
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