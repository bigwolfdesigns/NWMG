<div class="bannernavigation">
	<ul>
		<?php
		foreach($top_nav_bars as $top_nav_bar){
			$page	 = $top_nav_bar['page'];
			$name	 = $top_nav_bar['name'];
			?><li><a class="navlink" href="/<?php echo $page ?>"><?php echo $name ?></a></li><?php }
		?>
	</ul>
</div>
