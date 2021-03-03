<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!isset($size) || !isset($defimage) || !isset($failMsg) || !isset($imageCss)) {
    error_log('exit empty-single.php');
    exit;
}
if ($size == "small"):
    $size = "120";
    $failMsg = false;
elseif ($size == "medium"):
    $size = "180";
    $failMsg = false;
elseif ($size == "large"):
    $size = "400";
endif;
?>

<p>
<div style="text-align: center;">
    <img
            style="<?= $imageCss; ?>"
            src="<?= $defimage; ?>"
            border="0"
            width="<?= $size; ?>"
            alt="Default Image"
            title="Default Image"
    />
</div>
<?php if ($failMsg): ?>
    <div style="text-align: center;">
        <b><?= $failMsg; ?></b>
    </div>
<?php endif; ?>
</p>
