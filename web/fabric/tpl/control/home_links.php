<div style='padding-top:15px;'>
	<div class="row">
		<?php
		foreach($permissions as $perm){
			$has_perm = $perm['is_privileged'];
			if($has_perm){
				$perm_name	 = $perm['name'];
				$perm_icon	 = $perm['icon'];
				$perm_uri	 = $perm['uri'];
				?>
				<div class="col-xs-6 col-md-3">
					<a style='width:250px;height:250px' href="<?php echo $perm_uri ?>" class="thumbnail"><?php echo$perm_name ?></a>
				</div>
				<?php
			}
		}
		?>
	</div>
</div>