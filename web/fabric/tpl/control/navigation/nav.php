<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Navigation</h1>
    </div>
</div>

<?php
foreach($nav_options as $nav_opts){
	foreach($nav_opts as $key => $nav_opt){
		switch($key){
			case'top_menu':
				?>
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								Top Menu Links
							</div>
							<div class="panel-body">
								<div class="panel-group" id="top_menu_link_accordion">
									<?php
									if(is_array($nav_opt) && !empty($nav_opt)){
										?>
										<div class="panel panel-default">
											<div class="panel-heading">
												<h4 class="panel-title">
													<a data-toggle="collapse" data-parent="#top_menu_link_accordion" href="#top_menu_org_panel" >Organization</a>
												</h4>
											</div>
											<div class="panel-collapse collapse" id="top_menu_org_panel">
												<div class="panel-body">
													<form method="POST" action="<?php echo lc('uri')->get_uri(); ?>">
														<p>Drag and Drop the Links to order them the way you would want them to appear on the site.</p>
														<div class="navigation">
															<ul id="sort-top-menu-links"><?php
																foreach($nav_opt as $opt){
																	$page	 = $opt['page'];
																	$name	 = $opt['name'];
																	$sort	 = $opt['sort'];
																	?>
																	<li class="ui-state-default" id="<?php echo $sort ?>"><?php echo $name ?></li>
																	<?php
																}
																?>
															</ul>
															<input name="top_menu_json" type="hidden" value="[]" />
															<br />
															<input class="btn btn-default" type="submit" value="Submit" name="edit_top_menu_org_submit"/>
														</div>

													</form>
												</div>
											</div>
										</div>
									<?php } ?>
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#top_menu_link_accordion" href="#top_menu_edit_panel" >Editing</a>
											</h4>
										</div>
										<div class="panel-collapse collapse in" id="top_menu_edit_panel">
											<div class="panel-body">
												<form method="POST" action="<?php echo lc('uri')->get_uri(); ?>">
													<p>Click on a Nav Button to edit it. Fille in the fields with the Display Name and the page to direct to. <strong>Don't forget to click Submit!</strong></p>
													<script type='text/javascript'>
														var top_menu_links_json=<?php echo json_encode($nav_opt); ?>;
													</script>
													<?php
													foreach($nav_opt as $opt){
														$page	 = $opt['page'];
														$name	 = $opt['name'];
														$sort	 = $opt['sort'];
														?>
														<span class="btn btn-primary edit-top-nav" id="top_sort_<?php echo $sort ?>"><?php echo $name ?></span>
														<?php
													}
													?>
													<span class="btn btn-primary edit-top-nav" id="top_sort_0">+ Add One...</span>													
													<br style="clear:both" />
													<br style='clear:both' />
													<div class="col-md-6" id='top-menu-edit-container'>
														<div class="form-group row">
															<label class="col-md-3">Name:</label>
															<div class="input-group col-md-9">
																<!--<span class="input-group-addon"><i class="fa-flag fa"></i></span>-->
																<input data-attribute='name' type='text' id='top-menu-edit-name' class='form-control top-menu-edit-attr'/>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-md-3">Page:</label>
															<div class="input-group col-md-9">
																<!--<span class="input-group-addon"><i class="fa-flag-o fa"></i></span>-->	
																<input data-attribute='page' type='text' id='top-menu-edit-page' class='form-control top-menu-edit-attr'/>
															</div>
														</div> 
														<span class='form-control top-menu-edit-attr btn btn-danger' data-attribute='delete' id='top-menu-edit-delete'>Delete</span>
													</div>
													<input name='edit_top_menu_json' type="hidden" value="[]"/>
													<br style='clear:both' />
													<br style='clear:both' />
													<input style='display:none' class='btn btn-default' name='edit_top_menu_edit_submit' type="submit" value="Submit"/>
											</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
				break;
			case'footer_links':
				?>
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								Bottom Menu Links
							</div>
							<div class="panel-body">
								<div class="panel-group" id="bottom_menu_link_accordion">
									<?php
									if(is_array($nav_opt) && !empty($nav_opt)){
										?>
										<div class="panel panel-default">
											<div class="panel-heading">
												<h4 class="panel-title">
													<a data-toggle="collapse" data-parent="#bottom_menu_link_accordion" href="#bottom_menu_org_panel" >Organization</a>
												</h4>
											</div>
											<div class="panel-collapse collapse" id="bottom_menu_org_panel">
												<div class="panel-body">
													<form method="POST" action="<?php echo lc('uri')->get_uri(); ?>">
														<p>Drag and Drop the Links to order them the way you would want them to appear on the site.</p>
														<div class="navigation">
															<ul id="sort-bottom-menu-links"><?php
																foreach($nav_opt as $opt){
																	$page	 = $opt['page'];
																	$name	 = $opt['name'];
																	$sort	 = $opt['sort'];
																	?>
																	<li class="ui-state-default" id="<?php echo $sort ?>"><?php echo $name ?></li>
																	<?php
																}
																?>
															</ul>
															<input name="bottom_menu_json" type="hidden" value="[]" />
															<br />
															<input class="btn btn-default" type="submit" value="Submit" name="edit_bottom_menu_org_submit"/>
														</div>

													</form>
												</div>
											</div>
										</div>
									<?php } ?>
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#bottom_menu_link_accordion" href="#bottom_menu_edit_panel" >Editing</a>
											</h4>
										</div>
										<div class="panel-collapse collapse in" id="bottom_menu_edit_panel">
											<div class="panel-body">
												<form method="POST" action="<?php echo lc('uri')->get_uri(); ?>">
													<p>Click on a Nav Button to edit it. Fille in the fields with the Display Name and the page to direct to. <strong>Don't forget to click Submit!</strong></p>
													<script type='text/javascript'>
														var bottom_menu_links_json=<?php echo json_encode($nav_opt); ?>;
													</script>
													<?php
													foreach($nav_opt as $opt){
														$page	 = $opt['page'];
														$name	 = $opt['name'];
														$sort	 = $opt['sort'];
														?>
														<span class="btn btn-primary edit-bottom-nav" id="bottom_sort_<?php echo $sort ?>"><?php echo $name ?></span>
														<?php
													}
													?>
													<span class="btn btn-primary edit-bottom-nav" id="bottom_sort_0">+ Add One...</span>													
													<br style="clear:both" />
													<br style='clear:both' />
													<div class="col-md-6" id='bottom-menu-edit-container'>
														<div class="form-group row">
															<label class="col-md-3">Name:</label>
															<div class="input-group col-md-9">
																<!--<span class="input-group-addon"><i class="fa-flag fa"></i></span>-->
																<input data-attribute='name' type='text' id='bottom-menu-edit-name' class='form-control bottom-menu-edit-attr'/>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-md-3">Page:</label>
															<div class="input-group col-md-9">
																<!--<span class="input-group-addon"><i class="fa-flag-o fa"></i></span>-->	
																<input data-attribute='page' type='text' id='bottom-menu-edit-page' class='form-control bottom-menu-edit-attr'/>
															</div>
														</div> 
														<span class='form-control bottom-menu-edit-attr btn btn-danger' data-attribute='delete' id='bottom-menu-edit-delete'>Delete</span>
													</div>
													<input name='edit_bottom_menu_json' type="hidden" value="[]"/>
													<br style='clear:both' />
													<br style='clear:both' />
													<input style='display:none' class='btn btn-default' name='edit_bottom_menu_edit_submit' type="submit" value="Submit"/>
											</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
				break;
		}
	}
}
?>