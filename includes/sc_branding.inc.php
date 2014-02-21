<?php
if (isDev()) {
    $scorecard_home_url = "http://gdrights.org/scorecard_dev/";
} else {
    $scorecard_home_url = "http://gdrights.org/scorecard/";
}
?>
            <div id="header" class="group">
                <h1><a href="<?php echo $scorecard_home_url;?>">Climate Equity Pledge Scorecard</a> <span>beta</span></h1>
                <div id="partners">
                    <ul>
                        <li><a id="ecoequity" href="http://www.ecoequity.org">EcoEquity</a></li>
                        <li><a id="seius" href="http://www.sei-international.org">SEI-US</a></li>
                    </ul>
                </div><!-- end #partners -->
            </div><!-- end #header -->