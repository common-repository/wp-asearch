<?php
$settings = $root->controller()->irbAsGetSettings();
if(!empty($root->requestResult)){
	echo '<div class="' . (($root->requestResult['status'] == 1) ? 'updated' : 'error') . ' settings-error notice is-dismissible" id="setting-error-irb_as_settings_updated"> 
		<p><strong>' . $root->requestResult['response'] . '</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button>
	</div>';
}
$allPostTypes = get_post_statuses();
foreach($allPostTypes as $type => $name){
	$postTypes[$type] = $name . '<br />';
}
$searchFromFields = array('post'=> 'Post<br />', 'page'=> 'Page<br />');
if(class_exists( 'WooCommerce' ))
	$searchFromFields['product'] = 'Product';
?>

<div class="wrap">
	<h2><?= $root->company . ' ' . $root->title . ' Settings'; ?></h2>
	<form name="irb_as_settingsForm" id="irb_as_settingsForm" method="post" action="#">
		<table class="form-table">
			<tr>
				<th scope="row"><label for="irb_as_display_records">Records to display in search</label></th>
				<td><input name="irb_as_display_records" type="number" id="irb_as_display_records" value="<?= $settings['irb_as_display_records']; ?>" class="regular-number" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="irb_as_min_char_req">Minimum characters required to start search - (Optional)</label></th>
				<td><input name="irb_as_min_char_req" type="number" id="irb_as_min_char_req" value="<?= $settings['irb_as_min_char_req']; ?>" class="regular-number" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="irb_as_template">Search Result Template</label></th>
				<td>
					<?= irbSelectField(array(
						'name'=> 'irb_as_template',
						'attr'=> array(), 
						'options'=> array('template1'=> 'Template 1', 'template2'=> 'Template 2'), 
						'value'=> $settings['irb_as_template']
					)); ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label>Search From</label></th>
				<td>
					<?= irbCheckboxField(array(
						'name'=> 'irb_as_searchFrom[]',
						'attr'=> array(), 
						'options'=> $searchFromFields, 
						'value'=> $settings['irb_as_searchFrom']
					)); ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label>Post Status to show in search</label></th>
				<td>
					<?= irbCheckboxField(array(
						'name'=> 'irb_as_postStatus[]',
						'attr'=> array(), 
						'options'=> $postTypes,
						'value'=> $settings['irb_as_postStatus']
					)); ?>
				</td>
			</tr>
		</table>
		<p class="submit">
			<?= wp_nonce_field($root->prefix . '_action', $root->prefix . '_field'); ?>
			<input type="hidden" name="irbaction" value="<?= (empty($root->licenseCode['response']['rv']) ? 'irb_as_saveSetting' : 'irb_as_saveSettings'); ?>" />
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  />
		</p>
	</form>
</div>