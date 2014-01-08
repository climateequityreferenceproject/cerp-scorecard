<?php

require_once "functions.php";

// Use the function

$disabledString = array();
$checkedString = array();

if (isset($_POST['country'])) {
    $country = $_POST['country'];
} else {
    $country = null;
}
if (isset($_POST['conditional']) && $_POST['conditional']) {
    $use_conditional = true;
} else {
    $use_conditional = false;
}
get_pledges($disabledString, $checkedString, $country, $use_conditional);

?>
<label for="conditional-no" <?php if ($disabledString['no'] == 'disabled="disabled"') { echo 'class="disabled"'; } ?> >
    <input type="radio" name="conditional" id="conditional-no" value="0" 
<?php echo $checkedString['no']; ?> <?php echo $disabledString['no']; ?> /> <?php echo _('Weaker pledge (Unconditional)') ?>
</label>

<label for="conditional-yes" <?php if ($disabledString['yes'] == 'disabled="disabled"') { echo 'class="disabled"'; } ?> >
    <input type="radio" name="conditional" id="conditional-yes" value="1" 
<?php echo $checkedString['yes']; ?> <?php echo $disabledString['yes']; ?> /> <?php echo _('Stronger pledge (Conditional)') ?>
</label>

