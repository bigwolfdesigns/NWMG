<div class='home-page col-md-12'>
	<h1>Choose a Category to Browse</h1>
	<div class="row">
		<?php
		$i = 0;
		foreach($categories as $category){
			$i++;
			$cat_url	 = $category['url'];
			$cat_name	 = $category['name'];
			$cat_desc	 = $category['description'];
			$cat_img	 = $category['image'];
			$sub_cats	 = $category['sub_categories'];
			?>
			<div class="col-xs-3 col-xs-offset-1 col-sm-3 col-sm-offset-1 col-md-3 col-md-offset-1 home-bucket">
				<a href="<?php echo $cat_url ?>">
					<h3><?php echo $cat_name ?></h3>
				</a>
			</div>
			<?php
			if($i%3==0){
				?><div class='clearfix'></div><?php
			}
		}
		?>
	</div>
</div>