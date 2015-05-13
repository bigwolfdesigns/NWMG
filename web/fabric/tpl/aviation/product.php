<?php
$show_breadcrumbs = (bool)isset($show_breadcrumbs)?$show_breadcrumbs:true;
if($show_breadcrumbs){
	echo $this->grab('breadcrumbs');
}
?>
<div class="col-md-12 maxson-grey product-row">
	<img align='right' src="<?php echo $product['image'] ?>" width="250" height="150">
	<p><?php
		echo $product['description'];
		?></p>
</div>
<?php
foreach($product['features'] as $feature){
	$feature_name	 = $feature['name'];
	$feature_value	 = $feature['value'];
	?><h3><?php echo $feature_name ?>:</h3><?php
	if(count($feature_value)>1){
		?><ul><?php
			foreach($feature_value as $value){
				?><li><?php echo $value ?></li><?php
			}
			?></ul><?php
	}elseif(count($feature_value)>0){
		echo $feature_value[0];
	}
}