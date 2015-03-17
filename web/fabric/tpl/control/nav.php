<nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="<?php echo lc('uri')->create_uri(array(CLASS_KEY => 'control')); ?>"><?php echo $client_name ?></a>
    </div>
    <!-- /.navbar-header -->

	<?php
	$is_logged = true;
	if($is_logged){
		?>
		<ul class="nav navbar-top-links navbar-right">
			<!-- /.dropdown -->
			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">
					<i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
				</a>
				<ul class="dropdown-menu dropdown-user">
					<li><a href="<?php echo ''; ?>"><i class="fa fa-user fa-fw"></i> User Profile</a>
					</li>
					<li class="divider"></li>
					<li><a class="sign-out" href="javascript:void(0);" data-alt="<?php echo '' ?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
					</li>
				</ul>
			</li>
		</ul>
		<div class="navbar-default navbar-static-side" role="navigation">
			<div class="sidebar-collapse">
				<ul class="nav" id="side-menu">
					<li class="navbar-user-info">
						<span><?php echo ''; ?></span>
					</li>
					<li class="sidebar-search">
						<div class="input-group custom-search-form" style="display:none">
							<input type="text" class="form-control" placeholder="Search...">
							<span class="input-group-btn">
								<button class="btn btn-default" type="button">
									<i class="fa fa-search"></i>
								</button>
							</span>
						</div>
						<div class="input-group" style="display:none">
							<div class="input-group-btn">
								<button id="search_submit" type="button" class="btn btn-default" tabindex="-1"><i class="fa fa-search"></i></button>
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul id="search_choices" class="dropdown-menu" role="menu">
								</ul>
							</div>
							<input id="search_params" type="text" class="form-control">
						</div>
					</li>
					<li>
						<a href="<?php echo lc('uri')->create_uri(array(CLASS_KEY => 'control')); ?>"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
					</li>
					<?php
					foreach($permissions as $perm){
						$has_perm = $perm['is_privileged'];
						if($has_perm){
							$perm_name				 = $perm['name'];
							$perm_icon				 = $perm['icon'];
							$perm_uri				 = $perm['uri'];
							$perm_additional_links	 = $perm['second_level_links'];
							?>
							<li>
								<a href="<?php echo $perm_uri ?>"><i class="fa fa-<?php echo $perm_icon ?> fa-fw"></i> <?php echo $perm_name ?><span class="fa arrow"></span></a>
								<?php if(is_array($perm_additional_links) && !empty($perm_additional_links)){ ?>
									<ul class="nav nav-second-level">
										<?php
										foreach($perm_additional_links as $perm_add){
											$add_name	 = $perm_add['name'];
											$add_uri	 = $perm_add['uri'];
											?>
											<li>
												<a href="<?php echo $add_uri ?>"><?php echo $add_name ?></a>
											</li>
											<?php
										}
										?>
									</ul>
									<?php
								}
							}
						}
						?>
				</ul>
			</div>
		</div>
		<?php
	}
	?>
</nav>