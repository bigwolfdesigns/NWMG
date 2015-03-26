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
		if(is_array($sub_categories) && !empty($sub_categories)){
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
						<div class="category-image-container tint <?php echo $sub_cat_alias ?>">
							<a href="<?php echo $url ?>">
								<img src="<?php echo $sub_cat_image ?>" alt="<?php echo $sub_cat_name ?>" width="200" height="200" class="category-image-left">
							</a>
						</div>
					</div>
					<div class="col-md-8 category-description">
						<p><?php echo $sub_cat_description ?></p>
						<a class="pull-right" href="<?php echo $url ?>" style="float: right;">more...</a>
					</div>
				</div>
				<?php
			}
		}elseif(is_array($products) && !empty($products)){
			foreach($products as $product){
				$product_alias		 = $product['alias'];
				$product_name		 = $product['name'];
				$product_image		 = $product['image'];
				$product_description = $product['description'];
				$product_url		 = $product['url'];
				?>
				<div class="landing" width="210">
					<span class="landing-header">
						<a href="<?php echo $product_url ?>"><?php echo $product_name ?></a>
					</span>
					<img src="<?php echo $product_image ?>" width="110" height="110" class="landing-image-left">
					<?php echo $product_description ?>
					<ul class="nav-links" style="clear:both;">
						<li>
							<a href="<?php echo $product_url ?>"><?php echo $product_name ?></a>
						</li>
					</ul>
				</div>
				<?php
			}
		}
		?>
	</div>
	<?php echo $ecom_content; ?>
</div>
