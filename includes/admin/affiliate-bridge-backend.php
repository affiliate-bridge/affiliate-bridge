<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!isset($options) || !isset($defimage)) {
    exit;
}

?>

<div class="wrap">
<h1><?= __('Affiliate Bridge Settings', 'affiliate-bridge'); ?></h1>

<form method="post" action="">
<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row"><label for="source"><?= __('Default Source', 'affiliate-bridge') ?></label></th>
			<td>
				<select name="ab_source" id="source" class="regular-text" >
					<option value="ebay"><?= __('Ebay', 'affiliate-bridge'); ?></option>
				</select>
				<small><?= __('More Affiliate program in future plugin revisions...', 'affiliate-bridge') ?></small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="app_id"><?= __('Ebay App ID', 'affiliate-bridge'); ?></label></th>
			<td>
				<input name="ab_app_id" type="text" id="ab_app_id" value="<?= $options["ab_app_id"]; ?>" class="regular-text" />
				<small><?= __('Find your eBay App ID ', 'affiliate-bridge'); ?><a href="https://developer.ebay.com/my/keys" target="_blank"><?= __('Here', 'affiliate-bridge') ?></a></small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="keywords"><?= __('Default Keywords', 'affiliate-bridge'); ?></label></th>
			<td><input name="ab_keywords" type="text" id="keywords" value="<?= $options["ab_keywords"]; ?>" class="regular-text" />
				<small><?= __('Add keywords (with spaces and other special characters). See', 'affiliate-bridge'); ?>
                    <a href="https://developer.ebay.com/DevZone/finding/Concepts/FindingAPIGuide.html#usekeywords" target="_blank">
                        <?= __('eBay Developer Program', 'affiliate-bridge'); ?>
                    </a>
                </small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="framed"><?= __('Framed', 'affiliate-bridge'); ?></label></th>
			<td>
				<select name="ab_framed" id="framed" class="regular-text" >
					<option value="Y" <?= ($options['ab_framed'] == 'Y' || $options['ab_framed'] == "" ? 'selected' : ''); ?>><?= __('Yes', 'affiliate-bridge'); ?></option>
					<option value="N" <?= ($options['ab_framed'] == 'N' ? 'selected' : ''); ?>><?= __('No', 'affiliate-bridge'); ?></option>
					<option value="C" <?= ($options['ab_framed'] == 'C' ? 'selected' : ''); ?>><?= __('Custom', 'affiliate-bridge'); ?></option>
				</select>
				<small><?= __('Image frame options: "Yes": Boxed, "Custom": Custom frame, "No": No frame', 'affiliate-bridge'); ?></small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="categories"><?= __('Default Categories', 'affiliate-bridge'); ?></label></th>
			<td>
				<input name="ab_categories" type="text" id="categories" value="<?= $options['ab_categories']; ?>" class="regular-text" />
				<small><?= __('Add Category IDs, separated by a comma. Download eBay Categories ', 'affiliate-bridge'); ?>
                    <a href="https://www.shelftrend.com/blog2/ebay-category-numbers-download-excel" target="_blank"><?= __('Here', 'affiliate-bridge'); ?></a>
                </small>
			</td>		
		</tr>
		<tr>
			<th scope="row"><label for="condition"><?= __('Default Condition', 'affiliate-bridge'); ?></label></th>
			<td>
				<select name="ab_condition" id="condition" class="regular-text" >
					<option value="All" <?= $options['ab_condition'] == 'All' || $options['ab_condition'] == "" ? 'selected' : ''; ?>><?= __('All (New & Used)', 'affiliate-bridge'); ?></option>
					<option value="New" <?= $options['ab_condition'] == 'New' ? 'selected' : ''; ?>><?= __('New', 'affiliate-bridge'); ?></option>
					<option value="Used" <?= $options['ab_condition'] == 'Used' ? 'selected' : ''; ?>><?= __('Used', 'affiliate-bridge'); ?></option>
				</select>
				<small><?= __('Specify Items Condition in search. See', 'affiliate-bridge'); ?>
                    <a href="https://developer.ebay.com/devzone/finding/callref/Enums/conditionIdList.html" target="_blank">
                        <?= __('eBay Item Condition options', 'affiliate-bridge'); ?></a>
                </small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="image_size"><?= __('Default Image Size', 'affiliate-bridge'); ?></label></th>
			<td>
				<select name="ab_image_size" id="image_size" class="regular-text" >
					<option value="small" <?= $options['ab_image_size'] == "small" ? 'selected' : ''; ?>><?= __('Small (110px)', 'affiliate-bridge'); ?></option>
					<option value="medium" <?= $options['ab_image_size'] == "medium" ? 'selected' : ''; ?>><?= __('Medium (200px)', 'affiliate-bridge'); ?></option>
					<option value="large" <?= $options['ab_image_size'] == "large" || $options['ab_image_size'] == "" ? 'selected' : ''; ?>><?= __('Large (400px)', 'affiliate-bridge'); ?></option>
				</select>
				<small><?= __('Default image size. there are 2 more Shortcode options: size="75%", and size="400px"', 'affiliate-bridge'); ?></small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="items"><?= __('Default Number of Items', 'affiliate-bridge'); ?></label></th>
				<td><input name="ab_items" type="text" id="items" value="<?= $options['ab_items'] ? $options['ab_items'] : 1; ?>" class="regular-text" />
				<small><?= __('If items > 1, Multiple Items will be shown in a table', 'affiliate-bridge'); ?></small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="def_image"><?= __('Default Image', 'affiliate-bridge'); ?></label></th>
			<td>
				<div style="display:inline-block;">
				    <input type="text" name="ab_def_image" class="original_mp3 regular_def_image regular-text" value="<?= ($options['ab_def_image']) ? $options['ab_def_image'] : $defimage; ?>"/>
				    <input type="button" name="upload-btn-original" id="upload-btn" class="button-secondary upload-default-btn" value="Upload Image" /><br />
				    <img
                            id="def_def_image"
                            src="<?= $options['ab_def_image'] ? $options['ab_def_image'] : $defimage; ?>"
                            alt="<?= __('def', 'affiliate-bridge') ?>"
                            title="<?= __('Default Image (if eBay item does not have an image)', 'affiliate-bridge') ?>"
                            width="120"
                            style="margin-top:10px;"
                    />
				</div>
			</td>
		</tr>
	</tbody>
</table>

<p class="submit"><input type="submit" name="ab_submit" id="ab_submit" class="button button-primary" value="<?= __('Save Changes', 'affiliate-bridge'); ?>"></p></form>

</div>
