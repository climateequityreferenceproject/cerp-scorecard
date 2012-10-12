<?php

function getResults() {
    $retval = print_r($_POST, true);

    if (!isset($_POST['scoreview'])) {
        $scoreview = 'scorebasic';
    }
    else {
        if (!isset($_POST['switch_view'])) {
            $scoreview = $_POST['scoreview'];
        }
    }
    
    if (isset($_POST['switch_view'])) {
        if (isset($_POST['scoreview']) && ($_POST['scoreview'] == 'scorebasic')) {
            $scoreview = 'scoreadv';
        }
        else {
            $scoreview = 'scorebasic';
        }
    }
    $retval .= '<input type="hidden" value=' . $scoreview . ' name="scoreview" id="scoreview" />';
    $retval .= '<input type="submit" value="switch view" name="switch_view" id="switch_view" />';
    $retval .= '<br />' . $scoreview;
    return $retval;
}
?>
