<?php

if (!defined('ABSPATH')) {
    exit;
}
if (!isset($size) || !isset($defimage) || !isset($fail_message) || !isset($image_css)) {
    error_log('exit empty-single.php');
    exit;
}
if ($size == "small"):
    $image_css .= 'width: 120px;';
    $fail_message = false;
elseif ($size == "medium"):
    $image_css .= 'width: 180px;';
    $fail_message = false;
elseif ($size == "large"):
    $image_css .= 'width: 400px;';
else:
    $image_css .= "width: $size;";
endif;

$image_css .= 'border: 0;';
//echo $image_css;
?>

<p>
<div style="text-align: center;">
    <img
            style="<?php echo esc_attr($image_css); ?>"
            src="<?php echo esc_url($defimage); ?>"
            alt="<?php echo __('Default Image', 'affiliate-bridge'); ?>"
            title="<?php echo __('Default Image', 'affiliate-bridge'); ?>"
            class="ab-tbl-image"

    />
</div>
<?php if ($fail_message): ?>
    <div style="text-align: center;">
        <b><?php echo $fail_message; ?></b>
    </div>
<?php endif; ?>
</p>
