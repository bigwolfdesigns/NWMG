<div class='home-page'>
	<div class="row">
		<?php
		foreach($categories as $category){
			$cat_url	 = $category['url'];
			$cat_name	 = $category['name'];
			$cat_desc	 = $category['description'];
			$cat_img	 = $category['image'];
			$sub_cats	 = $category['sub_categories'];
			?>
			<div class="col-sm-6 col-md-4 home-bucket">
				<img alt="<?php echo $cat_name ?>" src="<?php echo $cat_img ?>"class="home-image">
				<h3><?php echo $cat_name?></h3>
			</div>
		<?php }
		?>
	</div>
</div>