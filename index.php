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

$glossary = new HWTHelp('glossary.xml', 'def_link', 'glossary.php');

$api = GDRsAPI::connection();

if ($_POST && ($_POST['country']!=='none')) {    
    $html = getResults();
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

  <title>Climate Equity Scorecard - BETA</title>
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
        <h1>Climate Equity Scorecard <span>BETA</span></h1>
        <h2><span>DO NOT CITE OR PUBLICIZE</span></h2>
        <p>Please send feedback on the Climate Equity Scorecard to <a href='m&#97;il&#116;o&#58;f%65&#37;65db%61ck&#64;gdri&#103;&#104;ts%2E%6F&#114;&#103;' class="ext">feed&#98;ack&#64;gdrig&#104;&#116;s&#46;org</a></p>      
        <p id="more_info"><?php echo $glossary->getLink('gloss_more_info') ?></p>
    </header>
    <div id="main" role="main" class="group">
        <form name="settings" id="settings" method="post" autocomplete="off" >
        <div id="settings_wrapper">
            
            <ul>
                <li class="setting">
                    <fieldset>
                        <legend>Country</legend>
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
                        <legend><?php echo $glossary->getLink('gloss_path');?></legend>
                        <?php
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
                            $checkedString['med'] = '';
                            $checkedString['high'] = 'checked="checked"';
                        }
                        ?>
                        <label for="ambition-high"><input type="radio" name="ambition" id="ambition-high" value="<?php echo $api->pathwayIds['high'] ?>" <?php echo $checkedString['high']; ?> /> <?php echo $api->pathwayLabel['high'] ?></label>
                        <label for="ambition-med"><input type="radio" name="ambition" id="ambition-med" value="<?php echo $api->pathwayIds['med'] ?>" <?php echo $checkedString['med']; ?> /> <?php echo $api->pathwayLabel['med'] ?></label>
                        <!-- No more low[est]-ambition pathway, for now
                        TODO: fix 3-pathway workflow to 2-pathway workflow, with labels stored in API and not overridden here -->
                    </fieldset>
                </li>
                <li class="setting">
                     <fieldset id="pledge_type">
                         <legend><?php echo $glossary->getLink('gloss_pledge');?></legend>
                        <?php 
                        //$pledge_cond = false; // temporarily hard-coded
                        //$pledge_uncond = true; // temporarily hard-coded
                        $pledge_cond = getPledgeInformation($_POST['country'], 1, $params['min_target_year']);
                        $pledge_uncond = getPledgeInformation($_POST['country'], 0, $params['min_target_year']);
                        
                        if ($pledge_cond and !$pledge_uncond) {
                            $checkedString['yes'] = 'checked="checked"';
                            $disabledString['yes'] = '';
                            $checkedString['no'] = '';
                            $disabledString['no'] = 'disabled="disabled"';
                        } else if (!$pledge_cond and $pledge_uncond) {
                            $checkedString['yes'] = '';
                            $disabledString['yes'] = 'disabled="disabled"';
                            $checkedString['no'] = 'checked="checked"';
                            $disabledString['no'] = '';
                        } else if ($pledge_cond and $pledge_uncond) {
                            if (isset($_POST['conditional'])) {
                                if ($_POST['conditional']) {
                                    $checkedString['yes'] = 'checked="checked"';
                                    $disabledString['yes'] = '';
                                    $checkedString['no'] = '';
                                    $disabledString['no'] = '';
                                } else {
                                    $checkedString['yes'] = '';
                                    $disabledString['yes'] = '';
                                    $checkedString['no'] = 'checked="checked"';
                                    $disabledString['no'] = '';
                                }
                            } else {
                                $checkedString['yes'] = '';
                                $disabledString['yes'] = '';
                                $checkedString['no'] = 'checked="checked"';
                                $disabledString['no'] = '';
                            }
                        }
                      // TODO: Disable choice if unavailable
                      // if selected country/group does NOT have a [conditional/unconditional] pledge, 
                      // add disabled="disabled" to the unavailable option and check the one that is available
                        
                        ?>                         
                         <label for="conditional-no"><input type="radio" name="conditional" id="conditional-no" value="0" 
                        <?php echo $checkedString['no']; ?> <?php echo $disabledString['no']; ?> /> Unconditional</label>
                         
                         <label for="conditional-yes"><input type="radio" name="conditional" id="conditional-yes" value="1" 
                        <?php echo $checkedString['yes']; ?> <?php echo $disabledString['yes']; ?>/> Conditional</label>
                     </fieldset>
                </li>
            </ul>

            <input type="submit" value="get score" id="submit" />

        </div><!-- end of #settings -->
        
        <div id="results" class="group">
            <?php echo $html; ?>
        </div> <!--! end of #results -->
        </form>
        
        <div id="popup"></div>
        
    </div> <!--! end of #main -->
        <?php include_once ("footer.php"); ?>
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
  <script src="js/libs/jquery.pageslide.min.js"></script>
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