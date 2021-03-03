<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!isset($size) || !isset($defimage) || !isset($failMsg) || !isset($imageCss)) {
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
            <img class="ab-tbl-image" style="<?= $imageCss; ?>" src="<?= $defimage; ?>" border="0" alt=""/>
        </td>
        <td align="center" valign="bottom" class="ab-tbl-error">
            <b><?= $failMsg; ?></b>
        </td>
    </tr>
    </tbody>
</table>
</p>