<?php $class_key = isset($class_key)?$class_key:lc('uri')->get(CLASS_KEY); ?>
<table align="center" cellspacing="0" cellpadding="5" width="100%" class='table table-striped'>
	<thead>
		<tr>
			<?php
			foreach($_config as $k => $v){
				if(!isset($v['show']['list']) || (isset($v['show']['list']) && $v['show']['list'])){
					?>
					<th align="left"><b><?php echo $v['display'] ?></b></th>
					<?php
				}
			}
			?>
			<th align="center">Edit</th>
			<th align="center">Delete</th>
		</tr>

	</thead>
	<tbody>
		<?php
		if($rows && count($rows) > 0){
			foreach($rows as $k => $row){
				$id					 = $row['id'];
				$background_color	 = ($k % 2 == 0)?"#EEEEEE":"#FFFFFF";
				?>
				<tr style="background-color: <?php echo $background_color ?>">
					<?php
					foreach($_config as $k => $_conf){
						if(!isset($_conf['show']['list']) || (isset($_conf['show']['list']) && $_conf['show']['list'])){
							$v			 = $row[$k];
							$ellipsis	 = false;
							if(strlen($v) > 15){
								$ellipsis = true;
							}
							$form = isset($_conf['form'])?$_conf['form']:array();
							if($k == 'id'){
								$v = "<a href='".lc('uri')->create_auto_uri(array(CLASS_KEY => $class_key, TASK_KEY => 'edit', 'id' => $id))."'>$v</a>";
							}
							?>
							<td align="left"><span style='width:150px' class='<?php echo $ellipsis?'ellipsis':'' ?>'><?php echo $this->make_list_field($k, $v, $form); ?></span></td>
								<?php
							}
						}
						?>
					<td align="center"><a href="<?php echo lc('uri')->create_auto_uri(array(CLASS_KEY => $class_key, TASK_KEY => 'edit', 'id' => $id)) ?>"><i class="fa fa-edit fa-fw"></i></a></td>
					<td align="center"><a href="<?php echo lc('uri')->create_auto_uri(array(CLASS_KEY => $class_key, TASK_KEY => 'delete', 'id' => $id)) ?>" onclick = "return confirm_delete('Are you sure you want to delete this record?')"><i style="color:red" class="fa fa-exclamation-circle fa-fw"></i></a></td>
				</tr>
				<?php
			}
		}else{
			?>
			<tr>
				<td align="left" colspan="<?php echo $col_count + 2; //for edit and delete functionality                                                                                       ?>">No data is available...</td>
			</tr>
		<?php } ?>
	</tbody>
</table>