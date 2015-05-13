<div class="col-md-9 category-main">
	<?php
	$ecom_content	 = isset($ecom_content)?$ecom_content:'';
	$main_cat_alias	 = $category['alias'];
	echo $this->grab('breadcrumbs');
	?>
	<div clas="row">
		<h3 class="category-header"><?php echo $category['name']; ?></h3>
	</div>
	<div class="clear"></div>
	<?php
	if(is_array($sub_categories)&&!empty($sub_categories)){
		?>
		<div class="row"><?php
			$i = 0;
			foreach($sub_categories as $sub_category){
				$i++;
				$sub_cat_alias		 = $sub_category['alias'];
				$sub_cat_name		 = $sub_category['name'];
				$sub_cat_image		 = $sub_category['image'];
				$sub_cat_description = $sub_category['description'];
				$url				 = lc('uri')->create_uri(array(CLASS_KEY => $main_cat_alias, TASK_KEY => $sub_cat_alias));
				?>
				<div class="col-xs-3 col-xs-offset-1 col-sm-3 col-sm-offset-1 col-md-3 col-md-offset-1 home-bucket">
					<a href="<?php echo $url ?>">
						<h3><?php echo $sub_cat_name ?></h3>
					</a>
				</div>
				<?php
				if($i%3==0){
					?><div class='clearfix'></div><?php
				}
			}
			?>
		</div><?php
	}elseif(is_array($products)&&!empty($products)){
		if(count($products)>1){
			foreach($products as $product){
				$product_alias		 = $product['alias'];
				$product_name		 = $product['name'];
				$product_image		 = $product['image'];
				$product_description = $product['description'];
				$product_url		 = $product['url'];
					?>
				<div class="col-md-12 maxson-grey product-row">
					<img align='right' src="<?php echo $product_image ?>" width="250" height="150">
					<p><?php
			echo $product_description;
					?></p>
				</div>
				<?php
			}
		}else{
			echo $this->grab('product', array('product' => $products[0], 'show_breadcrumbs' => false));
		}
	}
	?>
	<?php echo $ecom_content; ?>
	<!--<a class="btn btn-default request-quote-btn" href="/request-quote.html">Request a Quote</a>-->
</div>
