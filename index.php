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
    $effort = $effort_array['dom_pledge'];
} else {
    $params['min_target_year'] = NULL;
    $params['country_name'] = NULL;
    $params['ambition'] = NULL;
    
    $pledge_info = NULL;
    $effort_array = NULL;
    $effort = NULL;
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
            
            <ul>
                
                <li id="country">
                    <select id="country" name="country">
                        <?php echo avail_countries_options() ?>
                    </select>
                </li>
                <li id="ambition">
                    <fieldset>
                        <legend><a class="definition" href="#">Level of Global Ambition</a></legend>

                        <li><label for="ambition"><input type="radio" name="ambition" id="ambition-low" value="<?php echo $pathway_id['low'] ?>"/> <?php echo $pathway_label['low'] ?></label></li>
                        <li><label><input type="radio" name="ambition" id="ambition-med" value="<?php echo $pathway_id['med'] ?>" checked="checked" /> <?php echo $pathway_label['med'] ?></label></li>
                        <li><label><input type="radio" name="ambition" id="ambition-high" value="<?php echo $pathway_id['high'] ?>"/> <?php echo $pathway_label['high'] ?></label></li>
                    </fieldset>
                </li>
                <li id="pledge_type">
                    <fieldset>
                        <legend><a class="definition" href="#">Type of Pledge</a></legend>
                    
                        <li><label><input type="radio" name="conditional" value="0" checked="checked" /> Unconditional</label></li>
                        <li><label><input type="radio" name="conditional" value="1"/> Conditional</label></li>
                    </fieldset>
                </li>
            </ul>
            <input type="submit" value="run" />
        </form>
        
        <div id="summary">
            <div id="country_name"><?php echo $params['country_name'] ?></div>
            <p>has pledged to do</p>
            <div id="commitment"><?php echo number_format($effort) ?>%</div>
            <p>of its fair share in <?php echo $params['min_target_year'] ?>, assuming <?php echo strtolower($params['ambition']) ?> global ambition.</p>
            <p id="more_options"><a href="#">I want more options for this calculation</a></p>
        </div>

    </body>
</html>
