<div id="left-navigation" class="col-md-3">
	<ul class="nav">
		<?php
		foreach($categories as $category){
			$main_category_alias = $category['alias'];
			$main_category_name	 = $category['name'];
			$main_category_desc	 = $category['description'];
			$sub_categories		 = $category['sub_categories']
			?>
			<li >
				<a href="/<?php echo $main_category_alias ?>.html"><?php echo $main_category_name; ?></a>
			</li>
		<?php } ?>
	</ul>
</div>