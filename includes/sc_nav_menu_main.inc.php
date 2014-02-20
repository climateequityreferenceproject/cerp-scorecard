<?php
if (isDev()) {
    $gloss_url = "http://gdrights.org/scorecard_dev/glossary.php";
} else {
    $gloss_url = "http://gdrights.org/scorecard/glossary.php";
}
if (isDev()) {
    $calc_home_url = "http://gdrights.org/calculator_dev/";
} else {
    $calc_home_url = "http://gdrights.org/calculator/.php";
}
?>
<div id="nav">
    <ul class="group">
        <li><a href="http://gdrights.org/scorecard-info/about">About the Scorecard</a></li> 
        <li><a href="<?php echo $calc_home_url;?>">Climate Equity Reference Calculator</a></li>
        <li><a href="<?php echo $gloss_url;?>">Glossary</a></li>
        <li><a href="http://gdrights.org/about">About Greenhouse Development Rights</a></li>
        <li><a href="http://www.ecoequity.org">EcoEquity </a></li>
        <li><a href="http://www.sei-international.org">SEI</a></li>
<!-- link to send feedback: -->
        <li class="last"><a  href="&#109&#97&#105&#108&#116&#111&#58&#102&#101&#101&#100&#98&#97&#99&#107&#64&#103&#100&#114&#105&#103&#104&#116&#115&#46&#111&#114&#103&#63&#115&#117&#98&#106&#101&#99&#116&#61&#115&#99&#111&#114&#101&#99&#97&#114&#100&#32&#102&#101&#101&#100&#98&#97&#99&#107" title="&#115&#101&#110&#100&#32&#102&#101&#101&#100&#98&#97&#99&#107&#32&#111&#110&#32&#116&#104&#101&#32&#67&#108&#105&#109&#97&#116&#101&#32&#69&#113&#117&#105&#116&#121&#32&#83&#99&#111&#114&#101&#99&#97&#114&#100" >&#83;en&#100; &#70;&#101;&#101;db&#97;ck</a></li> 
    </ul>
</div><!-- end #nav -->