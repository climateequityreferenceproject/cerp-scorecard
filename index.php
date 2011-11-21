<?php
include("functions.php");

$pathway_id = get_pathways(array('low'=>'cum_1000GtCO2', 'med'=>'cum_750GtCO2', 'high'=>'Hansen'));

$min_target_year = NULL;
if ($_POST['conditional'] && $_POST['country']) {
    $min_target_year = get_min_target_year($_POST['country'], $_POST['conditional']); 
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
    <head profile="http://gmpg.org/xfn/11">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Climate Equity Scorecard</title>
    </head>
    <body>
        <h1>Climate Equity Scorecard</h1>
        <div id="more_info"><a href="#">more information about climate equity scorecards</a></div>
        <form name="settings" method="post">
            <input type="hidden" name="settings" />
            <ul id="settings">
                <li id="country">
                    <select id="country" name="country">
                        <?php echo avail_countries_options() ?>
                    </select>
                </li>
                <li id="ambition">
                    <a class="definition" href="#">Level of Global Ambition</a>
                    <ul class="radio">
                        <li><label><input type="radio" name="ambition" value="<?php echo $pathway_id['low'] ?>"/> Low</label></li>
                        <li><label><input type="radio" name="ambition" value="<?php echo $pathway_id['med'] ?>" checked="checked" /> Moderate</label></li>
                        <li><label><input type="radio" name="ambition" value="<?php echo $pathway_id['high'] ?>"/> High</label></li>
                    </ul>
                </li>
                <li id="pledge_type">
                    <a class="definition" href="#">Type of Pledge</a>
                    <ul class="radio">
                        <li><label><input type="radio" name="conditional" value="0" checked="checked" /> Unconditional</label></li>
                        <li><label><input type="radio" name="conditional" value="1"/> Conditional</label></li>
                    </ul>
                </li>
            </ul>
            <input type="submit" value="run" />
        </form>
        
        <div id="summary">
            <div id="country_name">COUNTRY</div>
            <p>has pledged to do</p>
            <div id="commitment">XX%</div>
            <p>of its fair share in <?php echo $min_target_year ?>, assuming TYPE global ambition.</p>
            <p id="more_options"><a href="#">I want more options for this calculation</a></p>
        </div>
        <?php


        $req =& new HTTP_Request("http://gdrights.org/calculator_dev/api/");
        $req->setMethod(HTTP_REQUEST_METHOD_POST);
        $req->addPostData("years", "2020");
        $req->addPostData("countries", "USA");
        if (!PEAR::isError($req->sendRequest())) {
             $response = json_decode($req->getResponseBody());
        } else {
            $response = "";
        }

        print_r($response);
        ?>
        
    </body>
</html>
