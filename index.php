<?php
include_once("functions.php");
include("scorecard_results.php");

$api = GDRsAPI::connection();

if ($_POST && ($_POST['country']!=='none')) {    
    $html = get_results($_POST);
} else {
    $html = $results_default;
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
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/jquery-ui-1.8.16.custom.css">
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
        <p id="more_info"><a class="def_link jqModalTest" href="glossary.php#gloss_more_info" target="_blank">more information about climate equity scorecards</a></p>
    </header>
    <div id="main" role="main" class="group">
        <form name="settings" id="settings" method="post" autocomplete="off" >
            
            <ul>
                <li class="setting">
                    <fieldset>
                        <legend>Country</legend>
                        <select id="country" name="country">
                        <?php
                        if (isset($_POST['country']) && ($_POST['country']!=='none')) {
                            echo '<option value="none">-Select-</option>';
                            echo avail_countries_options($_POST['country']);
                        } else {
                            echo '<option value="none" selected="selected">-Select-</option>';
                            echo avail_countries_options();
                        }?>
                        </select>
                    </fieldset>
                </li>
                <li class="setting">
                    <fieldset id="pathway">
                        <legend><a class="def_link" href="glossary.php#gloss_path" target="_blank"><span>Pathway</span></a></legend>
                        <?php // TODO: this is stupid, why is this repeated? what happened to DRY? ?>
                        <?php 
                        if (isset($_POST['ambition'])) {
                            foreach ($api->pathway_ids as $pw_type => $pw_id) {
                                if ($pw_id===$_POST['ambition']) {
                                    $checked_string[$pw_type] = 'checked="checked"';
                                } else {
                                    $checked_string[$pw_type] = '';
                                }
                            }
                        } else {
                            $checked_string['low'] = '';
                            $checked_string['med'] = '';
                            $checked_string['high'] = 'checked="checked"';
                        }
                        ?>
                        <label for="ambition-high"><input type="radio" name="ambition" id="ambition-high" value="<?php echo $api->pathway_ids['high'] ?>" <?php echo $checked_string['high']; ?> /> <?php echo $api->pathway_label['high'] ?></label>
                        <label for="ambition-med"><input type="radio" name="ambition" id="ambition-med" value="<?php echo $api->pathway_ids['med'] ?>" <?php echo $checked_string['med']; ?> /> <?php echo $api->pathway_label['med'] ?></label>
                        <label for="ambition-low"><input type="radio" name="ambition" id="ambition-low" value="<?php echo $api->pathway_ids['low'] ?>" <?php echo $checked_string['low']; ?> /> <?php echo $api->pathway_label['low'] ?></label>
                    </fieldset>
                </li>
                <li class="setting">
                     <fieldset id="pledge_type">
                         <legend><a class="def_link" href="glossary.php#gloss_pledge" target="_blank">Type of Pledge</a></legend>
                        <?php 
                        if (isset($_POST['conditional'])) {
                            if ($_POST['conditional']) {
                                $checked_string['yes'] = 'checked="checked"';                           
                                $checked_string['no'] = '';
                            } else {
                                $checked_string['no'] = 'checked="checked"';                           
                                $checked_string['yes'] = '';
                            }
                        } else {
                            $checked_string['no'] = 'checked="checked"';                           
                            $checked_string['yes'] = '';                        }
                        ?>                         
                         <label for="conditional-no"><input type="radio" name="conditional" id="conditional-no" value="0" <?php echo $checked_string['no']; ?> /> Unconditional</label>
                         <label for="conditional-yes"><input type="radio" name="conditional" id="conditional-yes" value="1" <?php echo $checked_string['yes']; ?> /> Conditional</label>
                     </fieldset>
                </li>
            </ul>
            <input type="submit" value="get score" id="submit" />
        </form>
        
        <div id="results" class="group">
            <?php echo $html; ?>
        </div> <!--! end of #results -->
        
        <div id="popup"></div>
        
    </div> <!--! end of #main -->
    <footer>

    </footer>
  </div> <!--! end of #container -->


  <!-- JavaScript at the bottom for fast page loading -->

  <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="js/libs/jquery-1.6.2.min.js"><\/script>')</script>

  <!-- Grab dialog-optimmized jQueryUI locally if possible, fall back to Google CDN's complete jQueryUI, with a protocol relative URL -->
  <script src="js/libs/jquery-ui-1.8.16.custom.min.js"></script>
  <script>window.ui || document.write('<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"><\/script>')</script>

  <!-- scripts concatenated and minified via ant build script-->
  <script defer src="js/plugins.js"></script>
  <script defer src="js/script.js"></script>
  <script src="js/scorecard.js"></script>
  <!-- <script src="js/jqModal.js"></script>-->
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