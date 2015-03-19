<img class='logo' src='/images/image/3'/>
<div class="top-navigation">
	<ul>
		<?php
		foreach($top_nav_bars as $top_nav_bar){
			$page	 = $top_nav_bar['page'];
			$name	 = $top_nav_bar['name'];
			?><li><a class="nav-link" href="/<?php echo $page ?>"><?php echo $name ?></a></li><?php }
		?>
	</ul>
</div>
<div class='top-right'>
	<a class='btn btn-default pull-right request-quote-btn' href='/request-quote.html'>Request a Quote</a>
	<form class='search-form'>
		<div class="input-group">
			<input type="text" class="form-control" placeholder="Search for...">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button">Go!</button>
			</span>
		</div>
	</form>
	<span class='pull-right phone-number'>800-532-6099</span>
</div>
<div class='clear'></div>