<div class="wrap">
<h1>Affiliate Bridge Settings</h1>

<form method="post" action="">
<table class="form-table" role="presentation">
	<tbody>
		<tr>
			<th scope="row"><label for="source">Default Source</label></th>
			<td>
				<select name="ab_source" id="source" class="regular-text" >
					<option value="ebay">Ebay</option>
				</select>
				<small>More Affiliate program in future plugin revisions...</small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="app_id">Ebay App ID</label></th>
			<td>
				<input name="ab_app_id" type="text" id="ab_app_id" value="<?php echo get_option("ab_app_id"); ?>" class="regular-text" />
				<small>Find your eBay App ID <a href="https://developer.ebay.com/my/keys" target="_blank">Here</a></small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="keywords">Default Keywords</label></th>
			<td><input name="ab_keywords" type="text" id="keywords" value="<?php echo get_option("ab_keywords"); ?>" class="regular-text" />
				<small>Add keywords (with spaces and other special characters). See <a href="https://developer.ebay.com/DevZone/finding/Concepts/FindingAPIGuide.html#usekeywords" target="_blank">eBay Developer Program</a></small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="framed">Framed</label></th>
			<td>
				<select name="ab_framed" id="framed" class="regular-text" >
					<option value="Y" <?php echo ((get_option("ab_framed") == "Y" || get_option("ab_framed") == "") ? 'selected' : ''); ?>>Yes</option>
					<option value="N" <?php echo ((get_option("ab_framed") == "N") ? 'selected' : ''); ?>>No</option>
					<option value="C" <?php echo ((get_option("ab_framed") == "C") ? 'selected' : ''); ?>>Custom</option>
				</select>
				<small>Image frame options: "Yes": Boxed, "Custom": Custom frame, "No": No frame</small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="categories">Default Categories</label></th>
			<td>
				<input name="ab_categories" type="text" id="categories" value="<?php echo get_option("ab_categories"); ?>" class="regular-text" />
				<small>Add Category IDs, seperated by a comma. Download eBay Categories <a href="https://www.shelftrend.com/blog2/ebay-category-numbers-download-excel" target="_blank">Here</a></small>
			</td>		
		</tr>
		<tr>
			<th scope="row"><label for="condition">Default Condition</label></th>
			<td>
				<select name="ab_condition" id="condition" class="regular-text" >
					<option value="All" <?php echo ((get_option("ab_condition") == "All" || get_option("ab_condition") == "") ? 'selected' : ''); ?>>All (New & Used)</option>
					<option value="New" <?php echo ((get_option("ab_condition") == "New") ? 'selected' : ''); ?>>New</option>
					<option value="Used" <?php echo ((get_option("ab_condition") == "Used") ? 'selected' : ''); ?>>Used</option>
				</select>
				<small>Specify Items Condition in search. See <a href="https://developer.ebay.com/devzone/finding/callref/Enums/conditionIdList.html" target="_blank">eBay Item Condition options</a></small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="image_size">Default Image Size</label></th>
			<td>
				<select name="ab_image_size" id="image_size" class="regular-text" >
					<option value="110" <?php echo ((get_option("ab_image_size") == "110") ? 'selected' : ''); ?>>Small (110px)</option>
					<option value="200" <?php echo ((get_option("ab_image_size") == "200") ? 'selected' : ''); ?>>Medium (200px)</option>
					<option value="400" <?php echo ((get_option("ab_image_size") == "400" || get_option("ab_image_size") == "") ? 'selected' : ''); ?>>Large (400px)</option>
				</select>
				<small>Default image size. there are 2 more Shortcode options: size="75%", and size="400px"</small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="items">Default Number of Items</label></th>
				<td><input name="ab_items" type="text" id="items" value="<?php echo (get_option("ab_items")) ? get_option("ab_items") : 1; ?>" class="regular-text" />
				<small>If items > 1, Multiple Items will be shown in a table</small>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="def_image">Default Image</label></th>
			<td>
				<div style="display:inline-block;">
				    <input type="text" name="ab_def_image" class="original_mp3 regular_def_image regular-text" value="<?php echo (get_option("ab_def_image")) ? get_option("ab_def_image") : $def_image; ?>"/>
				    <input type="button" name="upload-btn-original" id="upload-btn" class="button-secondary upload-default-btn" value="Upload Image" /><br />
				    <img id="def_def_image" src="<?php echo (get_option("ab_def_image")) ? get_option("ab_def_image") : $def_image; ?>" alt="def" title="Default Image (if eBay item does not have an image)" width="120" style="margin-top:10px;" />
				</div>
			</td>
		</tr>
	</tbody>
</table>

<p class="submit"><input type="submit" name="ab_submit" id="ab_submit" class="button button-primary" value="Save Changes"></p></form>

</div>
