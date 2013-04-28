<?php

if (isset($_POST['kyoto'])) {
    $include_kyoto = (bool) $_POST['kyoto'];
} else {
    $include_kyoto = true;
}
    
?>

<label for="kyoto-include" >
    <input type="radio" name="kyoto" id="kyoto-include" value="1" <?php if ($include_kyoto) { echo 'checked="checked"'; } ?> /> <?php echo _('Include Kyoto obligations') ?>
</label>

<label for="kyoto-exclude" >
    <input type="radio" name="kyoto" id="kyoto-exclude" value="0" <?php if (!$include_kyoto) { echo 'checked="checked"'; } ?> /> <?php echo _('Exclude Kyoto obligations') ?>
</label>