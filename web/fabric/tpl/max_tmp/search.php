<div class='col-md-9'>
	<?php
	if(isset($results['error'])){
		?><h2><?php echo $results['error'] ?></h2><?php
	}elseif(isset($results['empty'])){
		?><h2>We're sorry we could not find anything regarding your search..</h2><?php
	}elseif(is_array($results)){
		foreach($results as $table_name => $the_results){
			foreach($the_results as $result){
				?>
				<div class="row category-row">
					<div class="col-md-4">
						<div class="category-image-container tint fire-rated-doors-egress">
							<a href="<?php echo $result['url'] ?>">
								<img src="<?php echo $result['image'] ?>" alt="<?php echo $result['name'] ?>" width="200" height="200" class="category-image-left">
							</a>
						</div>
					</div>
					<div class="col-md-8 category-description">
						<p class="maxson-grey"><?php echo (isset($result['description']) && $result['description']!='')?$result['description']:$result['name'] ?></p>
						<a class="pull-right" href="<?php echo $result['url'] ?>" style="float: right;">more...</a>
					</div>
				</div>
				<?php
			}
		}
	}
	?>
</div>