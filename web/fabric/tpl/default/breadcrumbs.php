<span class="breadcrumbs">
	<?php
	foreach($breadcrumbs as $breadcrumb){
		$name	 = $breadcrumb['name'];
		$url	 = $breadcrumb['url'];
		?>&gt;
		<a href="<?php echo $url ?>">
			<nobr><?php echo $name ?></nobr>
		</a>
		<?php
	}
	?>
</span>