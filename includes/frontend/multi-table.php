<?php
if (!defined('ABSPATH')) {
    exit;
}

if(!isset($imageCss)) {
    error_log('exit multi-table.php');
    exit;
}

if (!isset($items)) {
    $items = [];
}

?>

<p>
<table style="width:100%;" align="center" border="3" cellspacing="0" cellpadding="3">
    <head>
        <colgroup>
            <col style="width: 20%;">
            <col>
        </colgroup>
    </head>
    <tbody>
    <?php

    $titles = [];
    $priceTitles = [];

    foreach ($items as $item) {
        if ((sizeof($titles) >= $count) && (sizeof($titles) > 0)) {
            break;
        }

        $ConditionDisplayName = isset($item['ConditionDisplayName']) ? $item['ConditionDisplayName'] . ' - ' : '';
        $CurrentPrice = $item['CurrentPrice'];
        $priceTitle = $ConditionDisplayName . $CurrentPrice['Value'] . ' ' . $CurrentPrice['CurrencyID'];
        $title = $item['Title'];
        if (in_array($title, $titles) || in_array($priceTitle, $priceTitles)) {
            continue;
        }
        if (!empty($item['PictureURL'])) {
            $pic = $item['PictureURL'][0];
        } else {
            $pic = $item['GalleryURL'];
        }

        if (!$pic && $defimage) {
            $pic = $defimage;
        }

        $itemURL = $item['ViewItemURLForNaturalSearch'];
        $alt = "$title ($priceTitle)";
        ?>
        <tr>
            <td align=center valign=top class="ab-tbl-data">
                <a href="<?= $itemURL; ?>" target="_blank">
                    <img
                            style="margin: 5px auto 5px auto;
                display: block; <?= $imageCss; ?>"
                            src="<?= $pic; ?>"
                            border="0"
                            alt="<?= $alt; ?>"
                            title="<?= $alt ?>"
                    />
                </a></td>
            <td align="center" valign="bottom" class="ab-tbl-data-multi">
                <a href="<?= $itemURL; ?>" target="_blank">
                    <?= $title; ?>
                    <br/>
                    <b><?= $priceTitle ?></b>
                </a>
            </td>
        </tr>
        <?php

        array_push($titles, $title);
        array_push($priceTitles, $priceTitle);
    }
    ?>
    </tbody>
</table>
