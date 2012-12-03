<?php
/**
 * index.php
 * 
 * PHP Version 5
 *
 * @package GDRsScorecard
 * @copyright 2011-2012 EcoEquity and Stockholm Environment Institute
 * @license All rights reserved
 * @link http://www.gdrights.org/
 */

if (isset($_GET['debug']) && $_GET['debug'] == 'yes') {
    ini_set('display_errors',1); 
    error_reporting(E_ALL);
}
require_once "functions.php";
require_once "scorecard_results.php";
require_once "class/HWTHelp/HWTHelp.php";

$glossary = new HWTHelp('def_link', 'glossary.php', 'sc_gloss');

$api = GDRsAPI::connection();

if ($_POST && ($_POST['country']!=='none')) {    
    $html = getResults();
    //$html = resultsTest();
} else {
    $html = $resultsDefault;
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
  <script src="js/libs/modernizr-2.6.2.min.js"></script>
  </head>  
  <body class="group">
    <div id="loading"></div>
    <div id="container" class="group">
        <header>
            <h1><a class="title" href="index.php">Climate Equity Scorecard</a></h1>
<!--            <h2><span>DO NOT CITE OR PUBLICIZE</span></h2>-->

            <?php
            if (isDev()) {
                $gloss_url = "http://gdrights.org/scorecard_dev/glossary.php";
            } else {
                $gloss_url = "http://gdrights.org/scorecard/glossary.php";
            }
            ?>
            <p><?php //echo $glossary->getLink('sc_about',0,'About the Scorecard') ?><a href="http://gdrights.org/scorecard-info/about/">About the Scorecard</a> &nbsp;|&nbsp; <a href="<?php echo $gloss_url;?>">Glossary</a> &nbsp;|&nbsp; <a  href="&#109&#97&#105&#108&#116&#111&#58&#102&#101&#101&#100&#98&#97&#99&#107&#64&#103&#100&#114&#105&#103&#104&#116&#115&#46&#111&#114&#103&#63&#115&#117&#98&#106&#101&#99&#116&#61&#115&#99&#111&#114&#101&#99&#97&#114&#100&#32&#102&#101&#101&#100&#98&#97&#99&#107" title="&#115&#101&#110&#100&#32&#102&#101&#101&#100&#98&#97&#99&#107&#32&#111&#110&#32&#116&#104&#101&#32&#67&#108&#105&#109&#97&#116&#101&#32&#69&#113&#117&#105&#116&#121&#32&#83&#99&#111&#114&#101&#99&#97&#114&#100" >&#83;en&#100; &#70;&#101;&#101;db&#97;ck</a></p>
        </header>
        <div id="main" role="main" class="group">
            <form name="settings" id="settings" method="post" autocomplete="off" >
            <div id="settings_wrapper">

                <ul>
                    <li class="setting">
                        <fieldset>
                            <legend>Country or Region</legend>
                            <select id="country" name="country">
                            <?php
                            if (isset($_POST['country']) && ($_POST['country']!=='none')) {
                                echo '<option value="none">-Select-</option>';
                                // 'country' can be either a region or a country
                                if (isCountry($_POST['country'])) {
                                    echo availRegionsOptions();
                                    echo '<option value="none">--------</option>';
                                    echo availCountriesOptions($_POST['country']);
                                } else {
                                    echo availRegionsOptions($_POST['country']);
                                    echo '<option value="none">--------</option>';
                                    echo availCountriesOptions();
                                }
                            } else {
                                echo '<option value="none" selected="selected">-Select-</option>';
                                echo availRegionsOptions();
                                echo '<option value="none">--------</option>';
                                echo availCountriesOptions();
                            }?>
                            </select>
                        </fieldset>
                    </li>

                    <li class="setting">
                        <fieldset id="pathway">
                            <legend><?php echo $glossary->getLink('gloss_path',0,'Level of Global Ambition');?></legend>
                            <?php
                            $checkedString = array();
                            if (isset($_POST['ambition'])) {
                                foreach ($api->pathwayIds as $pwType => $pwId) {
                                    if ($pwId===$_POST['ambition']) {
                                        $checkedString[$pwType] = 'checked="checked"';
                                    } else {
                                        $checkedString[$pwType] = '';
                                    }
                                }
                            } else {
                                $checkedString['low'] = '';
                                $checkedString['med'] = 'checked="checked"';
                                $checkedString['high'] = '';
                            }
                            ?>
                            <label for="ambition-med"><input type="radio" name="ambition" id="ambition-med" value="<?php echo $api->pathwayIds['med'] ?>" <?php echo $checkedString['med']; ?> /> <?php printf(_("Try to limit warming to %s"), $api->pathwayLabel['med']); ?></label>
                            <label for="ambition-high"><input type="radio" name="ambition" id="ambition-high" value="<?php echo $api->pathwayIds['high'] ?>" <?php echo $checkedString['high']; ?> /> <?php printf(_("Try to limit warming to %s"), $api->pathwayLabel['high']); ?></label>
                            <!-- No more low[est]-ambition pathway, for now
                            TODO: fix 3-pathway workflow to 2-pathway workflow, with labels stored in API and not overridden here -->
                        </fieldset>
                    </li>

                    <li class="setting">
                         <fieldset id="pledge_type">
                            <legend><?php echo $glossary->getLink('gloss_pledge');?></legend>
                            <div id="pledge_controls"><?php include_once 'pledge_control.php'; ?></div>
                         </fieldset>
                    </li>

                </ul>

                <input type="submit" value="get score" id="submit" />
                
                <p id="no-script-warning">Warning: you are viewing this page without JavaScript enabled. If the selected country or group has not made the selected type of pledge, you may see an error message and/or strange results after clicking GET SCORE. In that case, reload the page.</p>

            </div><!-- end of #settings_wrapper -->

            <div id="results" class="group">
                <?php echo $html; ?>
            </div> <!--! end of #results -->
            </form>

            <div id="popup"></div>

        </div> <!--! end of #main -->

        <footer>
            <p class="first">Thanks to the <a href="http://www.minor-foundation.no/" target="_blank">Minor Foundation for Major Challenges</a>, and to the <a href="http://www.rbf.org/">Rockefeller Brothers Fund</a>, <a href="http://www.christianaid.org.uk/">Christian Aid</a>, and the <a href="http://www.sei-international.org/">Stockholm Environment Institute</a> for supporting this effort.</p>
            <p><?php echo $glossary->getLink('sc_credits') ?></p>
            <p>Please send feedback on the Climate Equity Scorecard to <a href='m&#97;il&#116;o&#58;f%65&#37;65db%61ck&#64;gdri&#103;&#104;ts%2E%6F&#114;&#103;'>feed&#98;ack&#64;gdrig&#104;&#116;s&#46;org</a></p>
        </footer>
    </div> <!--! end of #container -->

    
  <!-- JavaScript at the bottom for fast page loading -->

  <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="js/libs/jquery-1.6.2.min.js"><\/script>')</script>

  <!-- Grab dialog-optimized jQueryUI locally if possible, fall back to Google CDN's complete jQueryUI, with a protocol relative URL -->
  <script src="js/libs/jquery-ui-1.8.16.custom.min.js"></script>
  <script>window.ui || document.write('<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"><\/script>')</script>

  <!-- scripts concatenated and minified via ant build script-->
  <script defer src="js/plugins.js"></script>
  <script defer src="js/script.js"></script>
  <script src="js/scorecard.js"></script>
  <!--<script src="js/libs/jquery.pageslide.min.js"></script>-->
<!--  <script src ="js/libs/jquery.ba-outside-events.min.js"></script>-->
      <script>
              $('body')
                      .bind(
                       'click',
                       function(e){
                        if(
                         $('#popup').dialog('isOpen')
                         && !$(e.target).is('.ui-dialog, a')
                         && !$(e.target).closest('.ui-dialog').length
                        ){
                         $('#popup').dialog('close');
                        }
                       }
                      );
    </script>

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