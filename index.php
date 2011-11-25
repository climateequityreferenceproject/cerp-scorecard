<?php
include("functions.php");

$pathway_id = get_pathways(array('low'=>'IPCC_likely', 'med'=>'AOSIS', 'high'=>'Hansen'));
$pathway_label = array(
    'low' => 'Low',
    'med' => 'Moderate',
    'high' => 'High'
);

$params = array();
if ($_POST) {
    $params['min_target_year'] = get_min_target_year($_POST['country'], $_POST['conditional']);
    $params['country_name'] = get_country_name($_POST['country']);
    $params['ambition'] = $pathway_label[array_search($_POST['ambition'], $pathway_id)];
    
    $pledge_info = get_pledge_information($_POST['country'], $_POST['conditional'], $params['min_target_year']);
    $effort_array = get_gdrs_information($pledge_info, $_POST['ambition']);
    $effort = $effort_array['dom_pledge'] + $effor_array['intl_pledge'];
} else {
    $params['min_target_year'] = NULL;
    $params['country_name'] = NULL;
    $params['ambition'] = NULL;
    
    $pledge_info = NULL;
    $effort_array = NULL;
    $effort = NULL;
}
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
    <div id="container" class="group">
    <header>
        <h1>Climate Equity Scorecard</h1>
        <p id="more_info"><a href="#">more information about climate equity scorecards &#187;</a></p>
    </header>
    <div id="main" role="main" class="group">
        <form name="settings" id="settings" method="post">
            Select a country or group below to see how its pledge measures up to its <a class="definition" href="#">fair share</a> of the global cost of mitigating climate change.
            <ul>
                <li class="setting">
                    <fieldset>
                        <legend>Country or Group</legend>
                        <select id="country" name="country">
                        <?php echo avail_countries_options() ?>
                        </select>
                    </fieldset>
                </li>
                <li class="setting">
                    <fieldset id="ambition">
                        <legend><a class="definition" href="#"><span>Level of Global Ambition</span></a></legend>
                        <label for="ambition-low"><input type="radio" name="ambition" id="ambition-low" value="<?php echo $pathway_id['low'] ?>"/> <?php echo $pathway_label['low'] ?></label>
                        <label for="ambition-med"><input type="radio" name="ambition" id="ambition-med" value="<?php echo $pathway_id['med'] ?>" checked="checked" /> <?php echo $pathway_label['med'] ?></label>
                        <label for="ambition-high"><input type="radio" name="ambition" id="ambition-high" value="<?php echo $pathway_id['high'] ?>"/> <?php echo $pathway_label['high'] ?></label>
                    </fieldset>
                </li>
                <li class="setting">
                     <fieldset id="pledge_type">
                         <legend><a class="definition" href="#">Type of Pledge</a></legend>
                         <label for="conditional-no"><input type="radio" name="conditional" id="conditional-no" value="0" checked="checked" /> Unconditional</label>
                         <label for="conditional-yes"><input type="radio" name="conditional" id="conditional-yes" value="1"/> Conditional</label>
                     </fieldset>
                </li>
            </ul>
            <input type="submit" value="get score" id="submit" />
        </form>
        
        <div id="results" class="group">
            <div id="summary">
                <p class="first"><span id="country_name"><?php echo $params['country_name'] ?></span> 
                has pledged to do 
                <span id="commitment"><?php echo number_format($effort) ?>%</span> 
                of its <a class="definition" href="#">fair share</a> in <?php echo $params['min_target_year'] ?>, 
                assuming <?php echo strtolower($params['ambition']) ?> global ambition.</p>
            </div>
            <div id="graph" class="group">
                <div id="international" class="international"></div>
                <div id="domestic" class="domestic"></div>
                <div id="gap" class="gap"></div>
           </div>
            <div id="key" class="group">
                <p><span class="international"></span><?php echo number_format($effort_array['intl_pledge']); ?>% <a class="definition" href="#">pledged international support</a></p>
                <p><span class="domestic"></span><?php echo number_format($effort_array['dom_pledge']); ?>% <a class="definition" href="#">pledged domestic effort</a></p>
                <p><span class="gap"></span><?php echo number_format($effort_array['gap']); ?>% <a class="definition" href="#">gap</a></p>
            </div>
            <p id="more_options"><a href="#">more options for this calculation &#187;</a></p>
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