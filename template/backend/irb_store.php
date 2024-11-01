<?php
$irbStoreData = $root->handler()->fetchIRBStoreItems();
?>
<p></p>
<div class="<?= ($irbStoreData === false) ? 'jumbotron' : ''; ?>">
	<?php 
	if($irbStoreData === false) {
		echo '<h2 class="text-center">Unable to fetch data</h2>';
	} else {
		foreach($irbStoreData as $type => $products) { ?>
		<div class="<?= $type; ?>">
			<?php foreach($products as $product) { 
			$productsCats = explode(',', $product['product_categories']);
			$productsTags = explode(',', $product['product_tags']);
			?>
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4><a class="text-black" target="_blank" href="<?= $product['product_url']; ?>"><?= $product['product_title']; ?> <sup><span class="label label-success">v <?= $product['product_version']; ?></span></sup></a></h4>
						<?php if($type == 'latest'){ ?><div class="ribbon"><i><span>Latest</span></i></div><?php } ?>
						<div class="ribbon <?= ($product['is_premium'] == 1) ? 'orange' : 'green'; ?>"><i><span><?= ($product['is_premium'] == 1) ? 'Premium' : 'Free'; ?></span></i></div>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3"><img class="img thumbnail" width="100" src="<?= $product['product_image']; ?>" /></div>
							<p class="col-md-9"><?= $product['product_details']; ?></p>
						</div>
						<div class="">
							<b class="pull-left">Categories: &nbsp;</b> 
							<span><?= implode(', ', $productsCats); ?></span>
						</div>
						<div class="">
							<b class="pull-left">Tags: &nbsp;</b> 
							<span><?= implode(', ', $productsTags); ?></span>
						</div>
					</div>
					<div class="panel-footer">
						<a class="btn btn-flat btn-default" target="_blank" href="<?= $product['product_url']; ?>">View Details</a>
						<a class="btn btn-flat btn-success pull-right" target="_blank" href="<?= $product['product_url']; ?>"><?= ($type == 'latest') ? 'Upgrade' : 'Download'; ?></a>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php } 
	}
		?>
</div>
