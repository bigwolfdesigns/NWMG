<?php
$ecom_content	 = isset($ecom_content)?$ecom_content:'';
$main_cat_alias	 = $category['alias'];
echo $this->grab('breadcrumbs');
?>
<h1><?php echo $category['name']; ?></h1>
<div>
	<?php
	if(is_array($sub_categories) && !empty($sub_categories)){
		foreach($sub_categories as $sub_category){
			$sub_cat_alias		 = $sub_category['alias'];
			$sub_cat_name		 = $sub_category['name'];
			$sub_cat_image		 = $sub_category['image'];
			$sub_cat_description = $sub_category['description'];
			?>
			<div class="landing" style="width:210px;">
				<span class="landing-header">
					<a href="<?php echo lc('uri')->create_uri(array(CLASS_KEY => $main_cat_alias, TASK_KEY => $sub_cat_alias)); ?>"><?php echo $sub_cat_name ?></a>
				</span>
				<img src="<?php echo $sub_cat_image ?>" width="110" height="110" class="landing-image-left">
				<?php echo $sub_cat_description ?>
				<ul class="nav-links" style="clear:both;">
					<li>
						<a href="<?php echo lc('uri')->create_uri(array(CLASS_KEY => $main_cat_alias, TASK_KEY => $sub_cat_alias)); ?>"><?php echo $sub_cat_name ?></a>
					</li>
				</ul>
			</div>
			<?php
		}
	}elseif(is_array($products) && !empty($products)){
		foreach($products as $product){
			$product_alias		 = $product['alias'];
			$product_name		 = $product['name'];
			$product_image		 = $product['image'];
			$product_description = $product['description'];
			$product_url = $product['url'];
			?>
			<div class="landing" width="210">
				<span class="landing-header">
					<a href="<?php echo $product_url ?>"><?php echo $product_name ?></a>
				</span>
				<img src="<?php echo $product_image ?>" width="110" height="110" class="landing-image-left">
				<?php echo $product_description ?>
				<ul class="nav-links" style="clear:both;">
					<li>
						<a href="<?php echo $product_url?>"><?php echo $product_name ?></a>
					</li>
				</ul>
			</div>
			<?php
		}
	}
	?>
</div>
<?php
echo $ecom_content;
