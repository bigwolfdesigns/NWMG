<div class="col-md-9">
	<?php
	$ecom_content	 = isset($ecom_content)?$ecom_content:'';
	$main_cat_alias	 = $category['alias'];
	echo $this->grab('breadcrumbs');
	?>
	<div clas="row">
		<h3 class="category-header"><?php echo $category['name']; ?></h3>
	</div>
	<div class="clear"></div>
	<div class="row">
		<?php
		if(is_array($sub_categories)&&!empty($sub_categories)){
			foreach($sub_categories as $sub_category){
				$sub_cat_alias		 = $sub_category['alias'];
				$sub_cat_name		 = $sub_category['name'];
				$sub_cat_image		 = $sub_category['image'];
				$sub_cat_description = $sub_category['description'];
				$url				 = lc('uri')->create_uri(array(CLASS_KEY => $main_cat_alias, TASK_KEY => $sub_cat_alias));
				?>
				<div class="row category-row">
					<div class="col-md-4">
						<style>
							.<?php echo $sub_cat_alias ?>:after {
								content: "<?php echo $sub_cat_name ?>";
								position: absolute;
								top: 50%;
								bottom: 0;
								left: 0;
								right: 0;
								color:white;
							}
						</style>
						<a href="<?php echo $url ?>" class="category-image-container tint <?php echo $sub_cat_alias ?>">
							<img src="<?php echo $sub_cat_image ?>" alt="<?php echo $sub_cat_name ?>" width="200" height="200" class="category-image-left">
						</a>
					</div>
					<div class="col-md-8 category-description">
						<p class='maxson-grey'><?php echo $sub_cat_description ?></p>
						<a class="pull-right" href="<?php echo $url ?>" style="float: right;">more...</a>
					</div>
				</div>
				<?php
			}
		}elseif(is_array($products)&&!empty($products)){
			if(count($products)>1){
				foreach($products as $product){
					$product_alias		 = $product['alias'];
					$product_name		 = $product['name'];
					$product_image		 = $product['image'];
					$product_description = $product['description'];
					$product_url		 = $product['url'];
					?>
					<div class="row maxson-grey product-row">
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
	</div>
	<?php echo $ecom_content; ?>
	<a class="btn btn-default request-quote-btn" href="/request-quote.html">Request a Quote</a>
</div>
