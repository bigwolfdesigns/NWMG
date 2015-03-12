<div id="left-navigation">
	<?php
	foreach($categories as $category){
		$main_category_alias = $category['alias'];
		$main_category_name	 = $category['name'];
		$main_category_desc	 = $category['description'];
		$sub_categories		 = $category['sub_categories']
		?>
		<span class="left-navgrey">
			<span class="left-header">
				<a href="/<?php echo $main_category_alias; ?>.html"><?php echo $main_category_name; ?></a>
			</span>
			<img alt="" src="/images/left-nav-divider(btqr72).gif" width="200" height="3" class="left-navigation-divider" />
			<?php
			if(trim($main_category_desc) !== ''){
				?>
				<p><?php echo $main_category_desc; ?></p>
				<?php
			}
			if(is_array($sub_categories) && !empty($sub_categories)){
				?>
				<ul class="nav-links">
					<?php
					foreach($sub_categories as $sub_category){
						$sub_category_alias	 = $sub_category['alias'];
						$sub_category_name	 = $sub_category['name'];
						?>
						<li>
							<a href="/<?php echo $main_category_alias.'/'.$sub_category_alias ?>.html"><?php echo $sub_category_name; ?></a>
						</li>
						<?php
					}
					?>
				</ul>
			<?php } ?>

		</span>
	<?php } ?>
</div>