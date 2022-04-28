<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!isset($options) || !isset($defimage)) {
    exit;
}

?>

<div class="wrap">
<h1><?php echo __('Affiliate Bridge Settings', 'affiliate-bridge'); ?></h1>

<form method="post" action="">
<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row"><label for="source"><?php echo __('Default Source', 'affiliate-bridge') ?></label></th>
			<td>
				<select name="ab_source" id="source" class="regular-text" >
					<option value="ebay"><?php echo __('Ebay', 'affiliate-bridge'); ?></option>
				</select>
				<small>
                    <?php echo __('More Affiliate program in future plugin revisions...', 'affiliate-bridge') ?>
                </small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="app_id"><?php echo __('Ebay App ID', 'affiliate-bridge'); ?></label></th>
			<td>
				<input name="ab_app_id" type="text" id="ab_app_id" value="<?php echo esc_attr($options["ab_app_id"]); ?>" class="regular-text" />
				<small>
                    <?php echo __('Find your eBay App ID ', 'affiliate-bridge'); ?>
                    <a href="https://developer.ebay.com/my/keys" target="_blank">
                        <?php echo __('Here', 'affiliate-bridge') ?>
                    </a>
                </small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="keywords"><?php echo __('Default Keywords', 'affiliate-bridge'); ?></label></th>
			<td><input name="ab_keywords" type="text" id="keywords" value="<?php echo esc_attr($options["ab_keywords"]); ?>" class="regular-text" />
				<small><?php echo __('Add keywords (with spaces and other special characters). See', 'affiliate-bridge'); ?>
                    <a href="https://developer.ebay.com/DevZone/finding/Concepts/FindingAPIGuide.html#usekeywords" target="_blank">
                        <?php echo __('eBay Developer Program', 'affiliate-bridge'); ?>
                    </a>
                </small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="framed"><?php echo __('Framed', 'affiliate-bridge'); ?></label></th>
			<td>
				<select name="ab_framed" id="framed" class="regular-text" >
					<option value="Y" <?php echo esc_attr($options['ab_framed']) == 'Y' || esc_attr($options['ab_framed']) == "" ? 'selected' : ''; ?>><?php echo __('Yes', 'affiliate-bridge'); ?></option>
					<option value="N" <?php echo esc_attr($options['ab_framed']) == 'N' ? 'selected' : ''; ?>><?php echo __('No', 'affiliate-bridge'); ?></option>
					<option value="C" <?php echo esc_attr($options['ab_framed']) == 'C' ? 'selected' : ''; ?>><?php echo __('Custom', 'affiliate-bridge'); ?></option>
				</select>
				<small><?php echo __('Image frame options: "Yes": Boxed, "Custom": Custom frame, "No": No frame', 'affiliate-bridge'); ?></small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="categories"><?php echo __('Default Categories', 'affiliate-bridge'); ?></label></th>
			<td>
				<input name="ab_categories" type="text" id="categories" value="<?php echo esc_attr($options['ab_categories']); ?>" class="regular-text" />
				<small><?php echo __('Add Category IDs, separated by a comma. Download eBay Categories ', 'affiliate-bridge'); ?>
                    <a href="https://www.shelftrend.com/blog2/ebay-category-numbers-download-excel" target="_blank"><?php echo __('Here', 'affiliate-bridge'); ?></a>
                </small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="condition"><?php echo __('Default Condition', 'affiliate-bridge'); ?></label></th>
			<td>
				<select name="ab_condition" id="condition" class="regular-text" >
					<option value="All" <?php echo esc_attr($options['ab_condition']) == 'All' || esc_attr($options['ab_condition']) == "" ? 'selected' : ''; ?>><?php echo __('All (New & Used)', 'affiliate-bridge'); ?></option>
					<option value="New" <?php echo esc_attr($options['ab_condition']) == 'New' ? 'selected' : ''; ?>><?php echo __('New', 'affiliate-bridge'); ?></option>
					<option value="Used" <?php echo esc_attr($options['ab_condition']) == 'Used' ? 'selected' : ''; ?>><?php echo __('Used', 'affiliate-bridge'); ?></option>
				</select>
				<small><?php echo __('Specify Items Condition in search. See', 'affiliate-bridge'); ?>
                    <a href="https://developer.ebay.com/devzone/finding/callref/Enums/conditionIdList.html" target="_blank">
                        <?php echo __('eBay Item Condition options', 'affiliate-bridge'); ?></a>
                </small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="image_size"><?php echo __('Default Image Size', 'affiliate-bridge'); ?></label></th>
			<td>
				<select name="ab_image_size" id="image_size" class="regular-text" >
					<option value="small" <?php echo esc_attr($options['ab_image_size']) == "small" ? 'selected' : ''; ?>><?php echo __('Small (110px)', 'affiliate-bridge'); ?></option>
					<option value="medium" <?php echo esc_attr($options['ab_image_size']) == "medium" ? 'selected' : ''; ?>><?php echo __('Medium (200px)', 'affiliate-bridge'); ?></option>
					<option value="large" <?php echo esc_attr($options['ab_image_size']) == "large" || esc_attr($options['ab_image_size']) == "" ? 'selected' : ''; ?>><?php echo __('Large (400px)', 'affiliate-bridge'); ?></option>
				</select>
				<small><?php echo __('Default image size. there are 2 more Shortcode options: size="75%", and size="400px"', 'affiliate-bridge'); ?></small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="items"><?php echo __('Default Number of Items', 'affiliate-bridge'); ?></label></th>
				<td><input name="ab_items" type="text" id="items" value="<?php echo esc_attr($options['ab_items']) ? esc_attr($options['ab_items']) : 1; ?>" class="regular-text" />
				<small><?php echo __('If items > 1, Multiple Items will be shown in a table', 'affiliate-bridge'); ?></small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="def_image"><?php echo __('Default Image', 'affiliate-bridge'); ?></label></th>
			<td>
				<div style="display:inline-block;">
				    <input
                            type="text"
                            name="ab_def_image"
                            class="original_mp3 regular_def_image regular-text"
                            value="<?php echo (esc_url($options['ab_def_image'])) ? esc_url($options['ab_def_image']) : esc_attr($defimage); ?>"
                    />
				    <input
                            type="button"
                            name="upload-btn-original"
                            id="upload-btn"
                            class="button-secondary upload-default-btn"
                            value="Upload Image"
                    />
                    <br />
				    <img
                            id="def_def_image"
                            src="<?php echo esc_url($options['ab_def_image']) ? esc_url($options['ab_def_image']) : esc_attr($defimage); ?>"
                            alt="<?php echo __('def', 'affiliate-bridge') ?>"
                            title="<?php echo __('Default Image (if eBay item does not have an image)', 'affiliate-bridge') ?>"
                            style="margin-top:10px; width: 120px;"
                    />
				</div>
			</td>
		</tr>
	</tbody>
</table>

<p class="submit"><input type="submit" name="ab_submit" id="ab_submit" class="button button-primary" value="<?php echo __('Save Changes', 'affiliate-bridge'); ?>"></p></form>

</div>
