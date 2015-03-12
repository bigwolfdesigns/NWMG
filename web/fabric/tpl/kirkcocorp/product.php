<?php
echo $this->grab('breadcrumbs');
?>
<h1><?php echo $product['name']; ?></h1>
<img alt="" src="<?php echo $product['image']; ?>" width="200" height="200" class="landing-image-right-clear">
<h3><?php echo $product['short_description']; ?></h3>
<p><?php echo $product['description']; ?></p>
<?php
//display features
foreach($product['features'] as $feature){
	$feature_name	 = $feature['name'];
	$feature_value	 = $feature['value'];
	?><h3><?php echo $feature_name ?></h3><?php
	if(count($feature_value) > 1){
		?><ul><?php
			foreach($feature_value as $value){
				?><li><?php echo $value ?></li><?php
			}
			?></ul><?php
	}elseif(count($feature_value) > 0){
		echo $feature_value[0];
	}
}