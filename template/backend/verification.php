<?php
if(!empty($root->lic_response) && $root->lic_response['status'] == 0){
	echo '<div class="error settings-error notice is-dismissible" id="setting-error-irb_as_settings_updated"><p>' . $root->lic_response['response'] . '</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
}
?>

<div class="irbVerificationForm">
	<h2>Verify <?= $root->orientedTitle; ?></h2>
	<p class="message"></p>
	<p>
		<a class="btn-link" href="<?= $root->apiUrl . 'license/pvf/' . $root->handler()->preparingRequestData(json_encode(array('code'=>$root->app_uniqueid, 'url'=>$root->rootUrl))); ?>" target="_blank">Click here to get license code</a>
	</p>
	<form method="post" action="#" id="verificationform" name="verificationform">
		<p>
			<label for="name">Name<br>
			<input type="text" name="name" size="40" value="" class="input" id="name" placeholder="Name of the person to which license issued"></label>
		</p>
		<p>
			<label for="lic">License Code<br>
			<input type="text" name="lic" size="40" value="" class="input" id="lic" placeholder="License Code"></label>
		</p>
		<p class="submit">
			<input type="hidden" name="irbaction" value="irb_as_verification" />
			<?= wp_nonce_field($root->prefix . '_verification', $root->prefix . '_verificationField'); ?>
			<input type="submit" value="Verify Now" class="button button-primary button-large" id="wp-submit" name="wp-submit">
		</p>
		<p class="powered">Powered by <a href="<?= $root->companyUrl; ?>" target="_blank"><?= $root->companySiteTitle; ?></a></p>
	</form>
</div>