<style>
    .required_field{
        border-color: red;
    }

	#overlay {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: #000;
		opacity: 0.5;
		filter: alpha(opacity=50);
	}
	#modal {
		position:absolute;
		background:url(/images/tint20.png) 0 0 repeat;
		background:rgba(0,0,0,0.2);
		border-radius:14px;
		padding:8px;
	}

	#content {
		border-radius:8px;
		background:#fff;
		padding:20px;
	}
	#close {
		position:absolute;
		background:url(/images/delete.gif) 0 0 no-repeat;
		width:24px;
		height:27px;
		display:block;
		text-indent:-9999px;
		top:-7px;
		right:-7px;
	}
	.clear{
		clear:both;
	}

</style>
<div class="row">
    <div class="col-lg-12">
		<h1 class="page-header"><?php echo ucwords($action)." ".$display_table ?></h1>
    </div>
</div>
<?php
$delete_gif	 = '/images/delete.gif';
$edit_gif	 = '/images/edit.gif';
//echo validation_errors("<div class='row'> <div class='alert alert-danger col-md-6'>", "</div><div class='col-md-6'></div></div>");

if(isset($errors) && is_array($errors) && count($errors) > 0){
	?>
	<div class="row">
		<?php
		foreach($errors as $message){
			?>
			<div class="alert alert-danger col-md-6 clear"><?php echo $message; ?></div>
			<?php
		}
		?>
	</div>
<?php }
?>
<div style='padding-top: 20px'>
	<?php
	if($action == 'edit'){
		?>
		<a style='color:red' href="<?php echo lc('uri')->create_auto_uri(array(CLASS_KEY => lc('uri')->get(CLASS_KEY), TASK_KEY => 'delete', 'id' => $id)) ?>" onclick = "return confirm_delete('Are you sure you want to delete this record?')">Delete this Record</a>
	<?php } ?>
    <form action="<?php echo $form_url ?>" method="POST" id='edit_form'>
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<?php
					foreach($_config as $k => $v){
						if(!isset($v['show'][$action]) || (isset($v['show'][$action]) && $v['show'][$action])){
							$extras = isset($v['form']['class'])?$v['form']['class']:'';
							$extras .= ' form-control';
							$default = isset($v['form']['default'])?$v['form']['default']:'';
							?>
							<div class="form-group row">
								<label class="col-md-3" ><?php echo$v['display']; ?></label>
								<div class="input-group col-md-9">
									<?php echo $this->make_form_field($k, $v['form'], $this->get_form_value($k, isset($info[$k])?$info[$k]:$default), $action, $extras); ?>
								</div>
							</div>
							<?php
						}
					}
					if($action == 'edit' && isset($related) && !empty($related)){
						echo $this->grab('related/table');
					}
					?>
					<div class="form-group">
						<input type="submit"  class='form-control btn btn-primary' name="submitted" value="<?php echo ucwords($action)." Record" ?>"/>
					</div>
				</div>
			</div>
		</div>
    </form>
</div>