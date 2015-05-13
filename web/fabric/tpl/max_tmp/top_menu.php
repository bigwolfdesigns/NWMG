<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-3">
		<a href="/home.html">
			<img class='logo' src='/images/image/3'/>
		</a>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-7 top-navigation">
		<ul>
			<?php
			foreach($top_nav_bars as $top_nav_bar){
				$page			 = $top_nav_bar['page'];
				$name			 = $top_nav_bar['name'];
				$class_key		 = str_replace('-', '_', lc('uri')->get(CLASS_KEY, ''));
				$t_page			 = str_replace(array('/', '-'), array('', '_'), substr($page, 0, strpos($page, '.')));
				$active_class	 = ($t_page==$class_key)?'active':'';
				?><li class="<?php echo $active_class ?>"><a class="nav-link" href="/<?php echo $page ?>"><?php echo $name ?></a></li><?php }
			?>
		</ul>
	</div>
	<div class='col-md-2'>
		<a class='btn btn-default pull-right request-quote-btn' href='/request-quote.html'>Request a Quote</a>
		<form class='search-form pull-right' action="<?php echo $search_url ?>" method="GET">
			<div class="input-group">
				<input name="q" type="text" class="form-control" placeholder="Search for...">
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit">Go!</button>
				</span>
			</div>
		</form>
		<span class='pull-right phone-number'>800-532-6099</span>
	</div>
	<div class='clear'></div>
</div>