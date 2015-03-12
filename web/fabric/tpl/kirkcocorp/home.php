<?php
foreach($categories as $category){
	$cat_url	 = $category['url'];
	$cat_name	 = $category['name'];
	$cat_desc	 = $category['description'];
	$cat_img	 = $category['image'];
	$sub_cats	 = $category['sub_categories'];
	?>
	<span class="bucket-left">
		<span class="product-header">
			<br>
			<a href="<?php echo $cat_url ?>"><?php $cat_name ?></a>
		</span>
		<?php if(is_array($sub_cats) && !empty($sub_cats)){ ?>
			<span class="drop-down">
				<select width="20" class="home_page_product_selector">
					<option value="">Select a Product</option>
					<?php foreach($sub_cats as $sub_cat){ ?>
						<option value="<?php echo $sub_cat['url'] ?>"><?php echo $sub_cat['name'] ?></option>
					<?php } ?>
				</select>
			</span>
		<?php } ?>
		<img alt="<?php echo $cat_name ?>" src="<?php echo $cat_img ?>" width="85" height="85" class="home-image">
		<p><?php echo $cat_desc ?></p>
	</span>
	<?php
}	