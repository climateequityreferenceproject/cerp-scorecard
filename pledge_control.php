<?php

require_once "functions.php";

function pledge_control(&$disabledString, &$checkedString) {
    $pledge_uncond = hasUnconditionalPledge($_POST['country']);
    $pledge_cond = hasConditionalPledge($_POST['country']);
//    echo $_POST['country'] . '<br />uncond: ';
//    echo $pledge_uncond ? 'true' : 'false';
//    echo '<br />cond: ';
//    echo $pledge_cond ? 'true' : 'false'; 

    if ($pledge_cond and !$pledge_uncond) { // conditional pledge only
        $checkedString['yes'] = 'checked="checked"';
        $disabledString['yes'] = '';
        $checkedString['no'] = '';  
        $disabledString['no'] = 'disabled="disabled"';
    } else if (!$pledge_cond and $pledge_uncond) { // unconditional pledge only
        $checkedString['yes'] = '';
        $disabledString['yes'] = 'disabled="disabled"';
        $checkedString['no'] = 'checked="checked"';
        $disabledString['no'] = '';
    } else if ($pledge_cond and $pledge_uncond) { // conditional AND unconditional pledges are available for this country/region
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
        } else { // conditional vs unconditional has not been set
            $checkedString['yes'] = '';
            $disabledString['yes'] = '';
            $checkedString['no'] = 'checked="checked"';
            $disabledString['no'] = '';
        }
    }
}

// Use the function

$disabledString = array();
$checkedString = array();

pledge_control($disabledString, $checkedString);
    
?>
<label for="conditional-no" <?php if ($disabledString['no'] == 'disabled="disabled"') { echo 'class="disabled"'; } ?> >
    <input type="radio" name="conditional" id="conditional-no" value="0" 
<?php echo $checkedString['no']; ?> <?php echo $disabledString['no']; ?> /> Unconditional
</label>

<label for="conditional-yes" <?php if ($disabledString['yes'] == 'disabled="disabled"') { echo 'class="disabled"'; } ?> >
    <input type="radio" name="conditional" id="conditional-yes" value="1" 
<?php echo $checkedString['yes']; ?> <?php echo $disabledString['yes']; ?> /> Conditional
</label>

