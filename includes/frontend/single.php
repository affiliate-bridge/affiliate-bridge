<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!isset($item) || !isset($size) || !isset($defimage) || !isset($image_css)) {
    error_log('exit single.php');
    exit;
}

// parse item
$ConditionDisplayName = isset($item['ConditionDisplayName']) ? $item['ConditionDisplayName'] . ' - ' : '';
$CurrentPrice = $item['CurrentPrice'];
$title = $item['Title'];
$priceTitle = $ConditionDisplayName . $CurrentPrice['Value'] . ' ' . $CurrentPrice['CurrencyID'];
$itemURL = $item['ViewItemURLForNaturalSearch'];

if ($item['PictureURL'] != null) {
    $pic = $item['PictureURL'][0];
} else {
    $pic = $item['GalleryURL'];
}

// fallbacks
$alt = "$title ($priceTitle)";
$height = '';
$showTitle = true;

if (!isset($pic)) {
    $pic = $defimage;
}

if ($title && $itemURL && $size == "small"):
    $image_css .= 'width:110px; border: 0;';
    $showTitle = false;
elseif ($title && $itemURL && $size == "medium"):
    $image_css .= 'width:200px; border: 90;';
    $showTitle = false;
elseif ($title && $itemURL && $size == "large"):
    $image_css .= 'width:400px; border: 0;';

else:
    $image_css .= 'border: 0;';
    $image_css .= "width: $size;";
endif;
?>

<p>
<div style="text-align: center;">
    <a href="<?php echo esc_url($itemURL); ?>" target="_blank">
        <img
                style="<?php echo esc_attr($image_css); ?>"
                src="<?php echo esc_url($pic); ?>"
                alt="<?php echo esc_attr($alt); ?>"
                title="<?php echo esc_attr($alt); ?>"
        >
    </a>
</div>
<?php if ($showTitle): ?>
    <div style="text-align: center;">
        <a href="<?php echo esc_url($itemURL); ?>" target="_blank">
            <?php echo esc_attr($title); ?>
            <br/>
            <b>(<?php echo esc_attr($priceTitle); ?>)</b>
        </a>
    </div>
<?php endif; ?>
</p>