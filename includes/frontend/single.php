<?php
if (!defined('ABSPATH')) {
    exit;
}

if(!isset($item) || !isset($size) || !isset($defimage) || !isset($imageCss)) {
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
$showTitle = false;
$border = 'border="0"';
$width = 'width="' . $size .'"';
$height = '';
$showTitle = true;

if(!isset($pic)) {
    $pic = $defimage;
}

if ($title && $itemURL && $size == "small"):
$border = 'border="0"';
$width= 'width="110"';
$height= 'height="90"';
$showTitle = false;
elseif ($title && $itemURL && $size == "medium"):
$border = 'border="90"';
$width= 'width="110"';
$height= 'height="90"';
$showTitle = false;
elseif ($title && $itemURL && $size == "large"):
$border = 'border="0"';
$width= 'width="400"';
$height= '';

endif;
?>

<p>
<div style="text-align: center;">
    <a href="<?= $itemURL; ?>" target="_blank">
        <img
            style="<?= $imageCss; ?>"
            src="<?= $pic; ?>"
            <?= $border; ?>
            <?= $width; ?>
            <?= $height; ?>
            alt="<?= $alt; ?>"
            title="<?= $alt; ?>"
        >
    </a>
</div>
<?php if($showTitle): ?>
<div style="text-align: center;">
    <a href="<?= $itemURL; ?>" target="_blank">
        <?= $title; ?>
        <br/>
        <b>(<?= $priceTitle; ?>)</b>
    </a>
</div>
<?php endif; ?>
</p>