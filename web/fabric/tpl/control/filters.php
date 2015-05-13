<div class="row">
    <a class='pull-left' href="<?php echo lc('uri')->create_auto_uri(array(CLASS_KEY => lc('uri')->get(CLASS_KEY), TASK_KEY => 'add')) ?>">Add a <?php echo $display_table ?></a>
    <form action="<?php echo lc('uri')->create_auto_uri(array(CLASS_KEY => lc('uri')->get(CLASS_KEY), TASK_KEY => 'manage')) ?>" method="GET" id='edit_form'>
        <h2>Filters:</h2>
		<div class='col-md-6' style='background-color: #83caea; padding-top: 10px;'>
			<?php foreach($_config as $k => $v){ ?>
				<div class="form-group row">
					<label class="col-md-4" ><?php echo$v['display'] ?></label>
					<div class="input-group col-md-6 col-md-offset-2">
						<span class="input-group-addon"><?php echo $this->get_filter_operator($v['form']['type']);?></span>
						<?php
						echo $this->make_filter_field($k, $v['form'], $this->get_form_value($k, isset($v['form']['default'])?$v['form']['default']:''));
						?>
					</div>
				</div>
			<?php } ?>                
			<div class="form-group">
				<input class='form-control btn btn-primary' type="submit" name="filter_submit" value="Filter"/>
			</div>
		</div>
    </form>
</div>