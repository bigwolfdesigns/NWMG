<div class="row">
    <div class="col-lg-12">
		<h1 class="page-header"><?php echo"Delete this ".ucwords($class_key) ?>?</h1>
    </div>
</div>
<?php if($deleted){ ?>
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<h1>Successfully Deleted!</h1>
			</div>
		</div>
	</div>
	<?php
}else{
	?>
	<form action="<?php echo lc('uri')->create_auto_uri(array(CLASS_KEY => $class_key, TASK_KEY => 'delete', 'id' => $id)) ?>" method="POST" id='edit_form'>  
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<input type="submit"  class='form-control btn btn-primary' name="delete" value="Delete this Record?"/>
					</div>
				</div>
			</div>
		</div>
	</form>
	<?php
}
?><a href='<?php echo lc('uri')->create_auto_uri(array(CLASS_KEY => $class_key, TASK_KEY => 'manage')); ?>'>Go back to the list</a>
