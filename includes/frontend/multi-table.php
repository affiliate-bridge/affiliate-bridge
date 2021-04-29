<?php

if (!defined('ABSPATH')) {
    exit;
}

if(!isset($image_css)) {
    error_log('exit multi-table.php');
    exit;
}

if (!isset($items)) {
    $items = [];
}

$image_css .= 'border: 0;';

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

        $ConditionDisplayName = isset($item['ConditionDisplayName']) ? sanitize_text_field($item['ConditionDisplayName']) . ' - ' : '';
        $CurrentPrice = $item['CurrentPrice'];
        $priceTitle = $ConditionDisplayName . sanitize_text_field($CurrentPrice['Value']) . ' ' . sanitize_text_field($CurrentPrice['CurrencyID']);
        $title = sanitize_text_field($item['Title']);

        if (in_array($title, $titles) || in_array($priceTitle, $priceTitles)) {
            continue;
        }

        if (!empty($item['PictureURL'])) {
            $pic = sanitize_text_field($item['PictureURL'][0]);
        } else {
            $pic = sanitize_text_field($item['GalleryURL']);
        }

        if (!$pic && $defimage) {
            $pic = $defimage;
        }

        $itemURL = sanitize_text_field($item['ViewItemURLForNaturalSearch']);
        $alt = "$title ($priceTitle)";

        ?>
        <tr>
            <td align=center valign=top class="ab-tbl-data">
                <a href="<?php echo esc_url($itemURL); ?>" target="_blank">
                    <img
                            style="margin: 5px auto 5px auto; display: block; <?php echo esc_attr($image_css); ?>"
                            src="<?php echo esc_attr($pic); ?>"
                            alt="<?php echo esc_attr($alt); ?>"
                            title="<?php echo esc_attr($alt) ?>"
                            class="ab-tbl-image"
                    />
                </a></td>
            <td align="center" valign="bottom" class="ab-tbl-data-multi">
                <a href="<?php echo esc_url($itemURL); ?>" target="_blank">
                    <?php echo esc_attr($title); ?>
                    <br/>
                    <b><?php echo esc_attr($priceTitle) ?></b>
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
