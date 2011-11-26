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

  <title>Climate Equity Scorecard</title>
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

  <!-- CSS: implied media=all -->
  <!-- CSS concatenated and minified via ant build script-->
  <link rel="stylesheet" href="style.css">
  <!-- end CSS-->

  <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

  <!-- All JavaScript at the bottom, except for Modernizr / Respond.
       Modernizr enables HTML5 elements & feature detects; Respond is a polyfill for min/max-width CSS3 Media Queries
       For optimal performance, use a custom Modernizr build: www.modernizr.com/download/ -->
  <script src="js/libs/modernizr-2.0.6.min.js"></script>
  </head>  
  <body class="group">
    <div id="loading"></div>
    <div id="container" class="group">
    <header>
        <h1>Climate Equity Scorecard</h1>
        <p id="more_info"><a href="#">more information about climate equity scorecards &#187;</a></p>
    </header>
    <div id="main" role="main" class="group">
        <form name="settings" id="settings" method="post" autocomplete="off" >
            
            <ul>
                <li class="setting">
                    <fieldset>
                        <legend>Country or Group</legend>
                        <select id="country" name="country">
                        <option value=AUS>Australia</option><option value=BLR>Belarus</option><option value=BRA>Brazil</option><option value=CAN>Canada</option><option value=CHK selected="selected">China</option><option value=CRI>Costa Rica</option><option value=HRV>Croatia</option><option value=ISL>Iceland</option><option value=IND>India</option><option value=IDN>Indonesia</option><option value=ISR>Israel</option><option value=JPN>Japan</option><option value=KAZ>Kazakhstan</option><option value=KOR>Korea, Rep.</option><option value=LIE>Liechtenstein</option><option value=MDV>Maldives</option><option value=MHL>Marshall Islands</option><option value=MEX>Mexico</option><option value=MDA>Moldova</option><option value=MCO>Monaco</option><option value=NZL>New Zealand</option><option value=NOR>Norway</option><option value=PNG>Papua New Guinea</option><option value=RUS>Russia</option><option value=SGP>Singapore</option><option value=ZAF>South Africa</option><option value=CHE>Switzerland</option><option value=UKR>Ukraine</option><option value=GBR>United Kingdom</option><option value=USA>United States</option>                        </select>
                    </fieldset>
                </li>
                <li class="setting">
                    <fieldset id="ambition">
                        <legend><a class="definition" href="#"><span>Level of Global Ambition</span></a></legend>
                                                                        <label for="ambition-low"><input type="radio" name="ambition" id="ambition-low" value="8"  /> Low</label>
                        <label for="ambition-med"><input type="radio" name="ambition" id="ambition-med" value="7" checked="checked" /> Moderate</label>
                        <label for="ambition-high"><input type="radio" name="ambition" id="ambition-high" value="4"  /> High</label>
                    </fieldset>
                </li>
                <li class="setting">
                     <fieldset id="pledge_type">
                         <legend><a class="definition" href="#">Type of Pledge</a></legend>
                                                 
                         <label for="conditional-no"><input type="radio" name="conditional" id="conditional-no" value="0" checked="checked" /> Unconditional</label>
                         <label for="conditional-yes"><input type="radio" name="conditional" id="conditional-yes" value="1"  /> Conditional</label>
                     </fieldset>
                </li>
            </ul>
            <input type="submit" value="get score" id="submit" />
        </form>
        
        <div id="results" class="group">
            <div id="summary"><p class="first"><span id="country_name">China</span> has pledged to do <span id="commitment">269%</span> of its <a class="definition" href="#">fair share</a> in 2020, assuming moderate global ambition.</p>            </div>
            <div id="graph" class="group">
                <div id="international" class="international" style="width:0%"></div> <div id="domestic" class="domestic" style="width:269%"</div> <div id="gap" class="gap" style="width:-169%"</div> </div> <!-- end #graph --><div id="key" class="group"><p><span class="international"></span> 0% <a class="definition" href="#">pledged international support</a></p><p><span class="domestic"></span> 269% <a class="definition" href="#">pledged domestic effort</a></p><p><span class="gap"></span> -169% <a class="definition" href="#">gap</a></p>        </div>
            <p id="more_options"><a href="http://gdrights.org/calculator_dev/?iso3=CHK">more results for this country &#187;</a></p>
            <div id="details">
                <h2>Details about this pledge</h2>
                <p class="first">[details text from pledge database: This result reflects... Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore...]</p>
            </div>
        </div> <!--! end of #results -->
    </div> <!--! end of #main -->
    <footer>

    </footer>
  </div> <!--! end of #container -->


  <!-- JavaScript at the bottom for fast page loading -->

  <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="js/libs/jquery-1.6.2.min.js"><\/script>')</script>


  <!-- scripts concatenated and minified via ant build script-->
  <script defer src="js/plugins.js"></script>
  <script defer src="js/script.js"></script>
  <!--<script src="js/scorecard.js"></script>-->
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