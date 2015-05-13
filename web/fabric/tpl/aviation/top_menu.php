<div class="row">
	<div class="col-md-6">
		<a href="/home.html">
			<img class='logo' src='/images/image/30'/>
		</a>
	</div>
	<div class="col-md-2">
		<span class="call-to-action">
			<h4><font color="#ffd700">1-800-776-7075</font></h4>704-264-1647<br>
		</span>
	</div>
	<div class='col-md-4 pull-right'>
		<form class='search-form pull-right' action="<?php echo $search_url ?>" method="GET">
			<div class="input-group">
				<input name="q" type="text" class="form-control" placeholder="Search for...">
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit">Search</button>
				</span>
			</div>
		</form>
	</div>
	<div class='clear'></div>
</div>
<div class='row'>
	<div class="col-xs-12 col-sm-12 col-md-12 top-navigation">
		<ul class='nav navbar nav-pills'>
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
</div>