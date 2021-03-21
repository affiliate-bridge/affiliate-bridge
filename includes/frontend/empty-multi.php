<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!isset($size) || !isset($defimage) || !isset($fail_message) || !isset($image_css)) {
    error_log('exit empty-multi.php');
    exit;
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
    <tr>
        <td align=center valign=top class="ab-tbl-data">
            <img class="ab-tbl-image" style="<?php echo esc_attr($image_css); ?>" src="<?php echo esc_url($defimage); ?>" border="0" alt=""/>
        </td>
        <td align="center" valign="bottom" class="ab-tbl-error">
            <b><?php echo $fail_message; ?></b>
        </td>
    </tr>
    </tbody>
</table>
</p>