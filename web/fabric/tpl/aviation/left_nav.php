<div id="left-navigation" class="col-md-3">
<!--	<span class="call-to-action">
		<span class="call"><p>Call Us Toll Free</p></span>
		<h2><font color="#ffd700">1-800-776-7075</font></h2>704-264-1647<br>
		<a href="/contact.php">Email Us</a>
	</span>-->
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