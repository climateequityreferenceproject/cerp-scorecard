<?php
if (isDev()) {
    $gloss_url = "http://gdrights.org/scorecard_dev/glossary.php";
} else {
    $gloss_url = "http://gdrights.org/scorecard/glossary.php";
}
if (isDev()) {
    $calc_home_url = "http://gdrights.org/calculator_dev/";
} else {
    $calc_home_url = "http://gdrights.org/calculator/";
}
?>
<div id="nav">
    <ul class="group">
        <li><a href="http://gdrights.org/scorecard-info/about" target="_blank">About the Scorecard</a></li> 
        <li><a href="<?php echo $calc_home_url;?>" target="_blank">Climate Equity Reference Calculator</a></li>
        <li class="last"><a href="<?php echo $gloss_url;?>" target="_blank">Glossary</a></li>
<!--        <li class="last"><a href="http://gdrights.org/about" target="_blank">About Greenhouse Development Rights</a></li>-->
    </ul>
</div><!-- end #nav -->