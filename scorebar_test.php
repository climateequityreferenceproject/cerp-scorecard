<!doctype html>
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

  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/jquery-ui-1.8.16.custom.css">
  <style>
.graph {margin-bottom:1em; position:relative; width:100%;}
/* remove the following line when replacing main css */
.graph .bg, .graph .score_neg, .graph .score_pos, .zero_line, .graph.brackets .bg, .brackets .score_neg, .brackets .score_pos {float:none; margin:0;}
.graph .bg, .graph .score_neg, .graph .score_pos {height:30px; position:absolute;}
.graph .bg {background: #eee; width:100%; z-index:10;}
.graph .score_neg, .graph .score_pos {z-index:100;}
.graph .score_neg {background:#dd6e4c; border-right:2px solid #666;}
.graph .score_pos {background:#6cad57; border-left:2px solid #666;}

/* nominally 60%, zero line fudged to avoid gap between line and score bar in Safari */
/*.zero_line {border-right:2px solid #666; left:0; position:absolute; width:59.5%; z-index:500;} */
.zero_line {background:transparent; border:none; left:59.5%; position:absolute; width:2px; z-index:500;}

.axis_labels {font-family: Arial, "Helvetica Neue", Helvetica, sans-serif; font-weight:normal; height:20px; left:0; position:absolute; width:100%;}
.axis_labels span {position:absolute; width:10%;}
.axis_labels span.left  {left:0; text-align:left;}
.axis_labels span.zero  {left:55%; text-align:center;}
.axis_labels span.right  {right:0; text-align:right;}

.graph.basic {height:60px;}
.graph.basic .bg, .graph.basic .score_neg, .graph.basic .score_pos {top:5px;}
/*.graph.basic .zero_line {height:40px; top:0;}*/
.graph.basic .axis_labels {top:35px;}

/*span.label_left, span.label_center, span.label_right {display:block; height:20px; width:6em;}
span.label_left {float:left;}
span.label_center {margin:0 auto; text-align:center;}
span.label_right {position:absolute; right:0; text-align:right; top:0;}*/

/*#key {clear:both; margin-top:0.5em;}*/

      .graph.brackets {height:150px;}
      .graph.brackets .bg, .graph.brackets .score_neg, .graph.brackets .score_pos {top:42px;}
/*      .graph.brackets .zero_line {height:30px; top:42px;} */
      .graph.brackets .bau_line {border-left:2px solid #666; height:50px; position:absolute; top:42px; z-index:500;}
/*      .graph.brackets .bau_line {background:#666; border:none; height:50px; left:30%; position:absolute; top:42px; width:0.5%; z-index:500;}*/
      .graph.brackets .label {
        display:block; 
	font-family: ProximaNova, Arial, "Helvetica Neue", Helvetica, sans-serif; 
        font-size:1em;
	font-weight:bold;
        height:20px; 
        position:absolute; 
        text-align:center;
        z-index:200;
      }
      .graph.brackets .axis_labels {top:72px;}
      .graph.brackets .label.pledge {top:0; width:20%;}
      .graph.brackets .label.bau {top:92px; width:10%;}
      .graph.brackets .label.fair {top:130px; width:20%;}
      .bracket {border:1px solid #999; color:#999; height:12px; position:absolute; z-index:200;}
      .bracket.pledge {border-bottom:none; border-top-width:4px; top:22px;}
      .bracket.fair {border-bottom-width:4px; border-top:none; top:113px;}
  </style>
  <!-- end CSS-->

  <script src="//www.google-analytics.com/ga.js"></script>
  <script src="js/libs/modernizr-2.0.6.min.js"></script>
  </head>  
  <body class="group">
    <div style="display: none;" id="loading"><img src="img/spinner.gif" alt="loading indicator"></div>
    <div id="container" class="group">
        <header>
            <h1>Climate Equity Scorecard <span>BETA</span></h1>
            <h2><span>DO NOT CITE OR PUBLICIZE</span></h2>

            <p><a href="http://gdrights.org/scorecard-info/about/">About the Scorecard</a> &nbsp;|&nbsp; <a href="http://gdrights.org/scorecard_dev/glossary.php">Glossary</a> &nbsp;|&nbsp; <a href="mailto:feedback@gdrights.org?subject=scorecard feedback" title="send feedback on the Climate Equity Scorecard">Feedback</a></p>
        </header>
        <div id="main" role="main" class="group">
            <form name="settings" id="settings" method="post" autocomplete="off">
            <div id="settings_wrapper">

                <ul>
                    <li class="setting">
                        <fieldset>
                            <legend>Country or Region</legend>
                            <select id="country" name="country">
                            <option value="none">-Select-</option><option value="eu27">EU 27</option><option value="none">--------</option><option value="AFG">Afghanistan</option><option value="ATG">Antigua and Barbuda</option><option value="AUS">Australia</option><option value="BLR">Belarus</option><option value="BRA">Brazil</option><option value="CAN">Canada</option><option value="CHK">China</option><option value="CRI">Costa Rica</option><option value="HRV">Croatia</option><option value="ISL">Iceland</option><option value="IND">India</option><option value="IDN">Indonesia</option><option value="ISR">Israel</option><option value="JPN">Japan</option><option value="KAZ">Kazakhstan</option><option value="KOR">Korea, Rep.</option><option value="LIE">Liechtenstein</option><option value="MDV">Maldives</option><option value="MHL">Marshall Islands</option><option value="MEX">Mexico</option><option value="MDA">Moldova</option><option value="MCO">Monaco</option><option value="NZL">New Zealand</option><option value="NOR">Norway</option><option value="PNG">Papua New Guinea</option><option value="RUS">Russia</option><option value="SGP">Singapore</option><option value="ZAF">South Africa</option><option value="CHE">Switzerland</option><option value="UKR">Ukraine</option><option value="GBR">United Kingdom</option><option value="USA" selected="selected">United States</option>                            </select>
                        </fieldset>
                    </li>

                    <li class="setting">
                        <fieldset id="pathway">
                            <legend><a class="def_link" href="glossary.php#gloss_path" target="_blank">Level of Global Ambition</a></legend>
                            <label for="ambition-med"><input name="ambition" id="ambition-med" value="12" checked="checked" type="radio"> 2.0℃ marker pathway (lower ambition)</label>
                            <label for="ambition-high"><input name="ambition" id="ambition-high" value="13" type="radio"> 1.5℃ marker pathway (higher ambition)</label>
                            <!-- No more low[est]-ambition pathway, for now
                            TODO: fix 3-pathway workflow to 2-pathway workflow, with labels stored in API and not overridden here -->
                        </fieldset>
                    </li>

                    <li class="setting">
                         <fieldset id="pledge_type">
                            <legend><a class="def_link" href="glossary.php#gloss_pledge" target="_blank">Type of Pledge</a></legend>
                            <div id="pledge_controls">
                                <label for="conditional-no">
                                    <input name="conditional" id="conditional-no" value="0" checked="checked" type="radio"> Unconditional
                                </label>
                                <label for="conditional-yes" class="disabled">
                                    <input name="conditional" id="conditional-yes" value="1" disabled="disabled" type="radio"> Conditional
                                </label>
                            </div>
                         </fieldset>
                    </li>
                </ul>

                <input style="display: none;" value="get score" id="submit" type="submit">

            </div><!-- end of #settings -->

            <div id="results" class="group">

                <p><span class="score negative">Score: -42</span> (basic)</p>
                <div class="graph basic">
                    <div class="bg"></div> <!-- always 100%, gray bar under everything else -->
                    <div class="axis_labels"><span class="left">-150</span><span class="zero">0</span><span class="right">100</span></div>
                    <div class="score_neg" style="left:43.2%; width:16.8%;"></div> <!-- |score| = 42 x 0.4 = 16.8; 60 - 16.8 = 43.2 -->
                </div> <!-- end .graph -->     
                
                <p><span class="score negative">Score: -42</span> (brackets, case 1, based on USA)</p>
                <div class="graph brackets"> <!-- total width from -150 to +100 points is 100%; zero is nominally at 60%; zero line is at 59.5%; 1 score point width = 0.4% -->
                    <div class="bg"></div>
                    <div class="axis_labels"><span class="left">-150</span><span class="zero">0</span><span class="right">100</span></div>
                    <!-- score bar gets class score_neg or score_pos -->
                    <div class="score_neg" style="left:43.2%; width:16.8%;"></div> <!-- |score| = 42 x 0.4 = 16.8; 60 - 16.8 = 43.2 -->
                    <div class="bau_line" style="left:35.5%;"></div> <!-- |BAU| = 61.26 x 0.4 = 24.5; 60 - 24.5 = 35.5 [left edge of BAU line div] -->
                    <div class="bau label" style="left:30.5%;">BAU</div> <!-- bau_line left - 5 [because BAU label is 10% wide] -->
                    <div class="bracket fair" style="left:35.5%; width:24.5%;"></div> <!-- left = bau_line left; ; width = 60 - left -->
                    <div class="label fair" style="left:37.25%;">fair share</div> <!-- fair bracket left + 1/2 fair bracket width - 10 [because fair label is 20% wide] -->
                    
                    <!-- next line is case 1 specific -->
                    <div class="bracket pledge" style="left:35.5%; width:7.7%;"></div> <!-- left = bau_line left; width = score_neg left - bau_line left -->

                    <div class="label pledge" style="left:29.35%;">pledge</div> <!-- pledge bracket left + 1/2 pledge bracket width - 10 [because pledge label is 20% wide] -->
                </div><!-- end .graph -->
                

                <p><span class="score">Score: 8</span> (basic)</p>
                <div class="graph basic">
                    <div class="bg"></div>
                    <div class="axis_labels"><span class="left">-150</span><span class="zero">0</span><span class="right">100</span></div>
                    <div class="score_pos" style="left:60%; width:3.2%;"></div> <!-- left at zero line; |score| = 8; width = 8 x 0.4 = 3.2 -->
                </div> <!-- end .graph -->     
                
                <!-- case 2 [like Brazil] -->
                <p><span class="score">Score: 8</span> (brackets, case 2, based on Brazil)</p>
                <div class="graph brackets"> <!-- total width from -150 to +100 points is 100%; zero is nominally at 60%; 1 score point width = 0.4% -->
                    <div class="bg"></div>
                    <div class="axis_labels"><span class="left">-150</span><span class="zero">0</span><span class="right">100</span></div>
                    <div class="bau_line" style="left:47.44%;"></div> <!-- |BAU| = 31.40 x 0.4 = 12.56; 60 - 12.56 = 47.44 [left edge of BAU line div] -->
                    <div class="bau label" style="left:42.44%;">BAU</div> <!-- bau_line left - 5 [because BAU label is 10% wide] -->
                    <div class="bracket fair" style="left:47.44%; width:12.56%;"></div> <!-- left = bau_line left; width = 60 - left -->
                    <div class="label fair" style="left:43.72%;">fair share</div> <!-- fair bracket left + 1/2 fair bracket width - 10 [because fair label is 20% wide] -->
                    
                    <!-- next two lines are case 2 specific -->
                    <div class="score_pos" style="left:60%; width:3.2%;"></div> <!-- left at zero line; |score| = 8; width = 8 x 0.4 = 3.2 -->
                    <div class="bracket pledge" style="left:47.44%; width:15.76%;"></div> <!-- left = bau_line left; width = bracket fair width + score_pos width -->
                    
                    <div class="label pledge" style="left:45.32%;">pledge</div> <!-- pledge bracket left + 1/2 pledge bracket width - 10 [because pledge label is 20% wide] -->
                </div><!-- end .graph -->

                
                <!-- case 3 [like India] -->
                <p><span class="score negative">Score: -13</span> (brackets, case 3, based on India)</p>
                <div class="graph brackets">
                    <div class="bg"></div>
                    <div class="axis_labels"><span class="left">-150</span><span class="zero">0</span><span class="right">100</span></div>
                    <div class="score_neg" style="left:54.8%; width:5.2%;"></div> <!-- |score| = 13 x 0.4 = 5.2; 60 - 5.2 = 54.8 -->
                    <div class="bau_line" style="left:58.828%;"></div> <!-- |BAU| = 2.93 x 0.4 = 1.172; 60 - 1.172 = 58.828 [left edge of BAU line div] -->
                    <div class="bau label" style="left:53.828%;">BAU</div> <!-- bau_line left - 5 [because BAU label is 10% wide] -->
                    <div class="bracket fair" style="left:58.828%; width:1.172%;"></div> <!-- left = bau_line left; width = 60 - left -->
                    <div class="label fair" style="left:49.414%;">fair share</div> <!-- fair bracket left + 1/2 fair bracket width - 10 [because fair label is 20% wide] -->
                    
                    <!-- next line is case 3 specific -->
                    <div class="bracket pledge" style="left:54.8%; width:4.028%;"></div> <!-- left = score_neg left; width = score_neg width - bracket fair width -->
                    
                    <div class="label pledge" style="left:46.814%;">pledge</div> <!-- pledge bracket left + 1/2 pledge bracket width - 10 [because pledge label is 20% wide] -->
                </div><!-- end .graph -->

                
                <!-- case 3 [like Ukraine] -->
                <p><span class="score negative">Score: -101</span> (brackets, case 3, based on Ukraine)</p>
                <div class="graph brackets">
                    <div class="bg"></div>
                    <div class="axis_labels"><span class="left">-150</span><span class="zero">0</span><span class="right">100</span></div>
                    <div class="score_neg" style="left:19.6%; width:40.4%;"></div> <!-- |score| = 101 x 0.4 = 40.4; 60 - 40.4 = 19.6 -->
                    <div class="bau_line" style="left:54.848%;"></div> <!-- |BAU| = 12.88 x 0.4 = 5.152; 60 - 5.152 = 54.848 [left edge of BAU line div] -->
                    <div class="bau label" style="left:49.848%;">BAU</div> <!-- bau_line left - 5 [because BAU label is 10% wide] -->
                    <div class="bracket fair" style="left:54.848%; width:5.152%;"></div> <!-- left = bau_line left; width = 60 - left -->
                    <div class="label fair" style="left:47.424%;">fair share</div> <!-- fair bracket left + 1/2 fair bracket width - 10 [because fair label is 20% wide] -->
                    
                    <!-- next line is case 3 specific -->
                    <div class="bracket pledge" style="left:19.6%; width:35.248%;"></div> <!-- left = score_neg left; width = score_neg width - bracket fair width -->
                    
                    <div class="label pledge" style="left:27.224%;">pledge</div> <!-- pledge bracket left + 1/2 pledge bracket width - 10 [because pledge label is 20% wide] -->
                </div><!-- end .graph -->
                



                <p><a href="#">Basic view</a>&nbsp;|&nbsp;Detailed view</p>

                <div class="results_links">
                    <a href="http://gdrights.org/scorecard-info/interpret-scorecard/" target="_blank">How do I interpret these scores?</a> &nbsp;|&nbsp; <a href="http://gdrights.org/calculator_dev/?copydb=yes&amp;iso3=USA&amp;year=2020&amp;db=fw-sql3-V8PjL1" target="_blank">More detailed calculations »</a>
                </div>            
            </div> <!--! end of #results -->
            </form>

            <div style="display: none;" id="popup"></div>

        </div> <!--! end of #main -->

        <footer>
            <p class="first">Thanks to the <a href="http://www.minor-foundation.no/" target="_blank">Minor Foundation for Major Challenges</a>, and to the <a href="http://www.rbf.org/">Rockefeller Brothers Fund</a>, <a href="http://www.christianaid.org.uk/">Christian Aid</a>, and the <a href="http://www.sei-international.org/">Stockholm Environment Institute</a> for supporting this effort.</p>
            <p><a class="def_link" href="glossary.php#sc_credits" target="_blank">Acknowledgements</a></p>
            <p>Please send feedback on the Climate Equity Scorecard to <a href="mailto:f%65%65db%61ck@gdrights%2E%6Frg">feedback@gdrights.org</a></p>
        </footer>
    </div> <!--! end of #container -->


  <!-- JavaScript at the bottom for fast page loading -->

  <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="js/libs/jquery-1.6.2.min.js"><\/script>')</script>

  <!-- Grab dialog-optimized jQueryUI locally if possible, fall back to Google CDN's complete jQueryUI, with a protocol relative URL -->
  <script src="js/libs/jquery-ui-1.8.16.custom.min.js"></script>
  <script>window.ui || document.write('<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"><\/script>')</script><script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>

  <!-- scripts concatenated and minified via ant build script-->
  <script defer="" src="js/plugins.js"></script>
  <script defer="" src="js/script.js"></script>
  <script src="js/scorecard.js"></script>
  <!-- end scripts-->
	
</body></html>