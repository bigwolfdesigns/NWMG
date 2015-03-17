<div class='related'>
	<style>
		.related{
			padding-bottom:15px;
		}
		.sortable-list{
			list-style-type: none;
			margin: 0;
			float: left;
			margin-right: 10px;
			background: #eee;
			padding: 5px;
			width: 143px;
			max-height:300px;
			overflow:auto;
		}
		#page{
			width:auto;
		}
	</style>
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Related Tables</h1>
		</div>
	</div>
	<script>
		var sortables=[];
	</script>
	<?php
	foreach($related as $group_name => $groups){
		$sortable = array();
		?>
		<div class="row">
			<div class="col-lg-12">
				<?php
				foreach($groups as $table_name => $group){
					$str	 = 'base-';
					$base	 = false;
					if(strpos($table_name, $str) !== false){
						$table_name	 = str_replace($str, '', $table_name);
						$base		 = true;
					}else{
						//get the value serialized
						?><input type='hidden' name='<?php echo 'related_table_'.$table_name ?>' value=""/><?php
					}
					$sortable[] = "#$table_name";
					?>
					<div class="col-md-6">
						<h2><?php echo ll('format')->de_underscore($table_name); ?></h2>
						<ul id="<?php echo $table_name; ?>" class="sortable-list connected-<?php echo $group_name ?>">
							<?php
							foreach($group as $row){
								$my_id = '';
								if(!$base){
									$field	 = $group_name;
									$value	 = $row[$group_name];
									$my_id	 = $field.'_'.$value;
								}else{
									$field	 = $group_name;
									$value	 = $row['id'];
									$my_id	 = $field.'_'.$value;
								}
								echo $this->show_related($table_name, $row, $my_id);
							}
							?>
						</ul>

					</div>
				<?php }
				?>
				<script>
					$(function (){
						sortables.push(<?php echo $table_name ?>);
						$("<?php echo implode(', ', $sortable); ?>").sortable({
							connectWith: ".connected-<?php echo $group_name ?>"
						}).disableSelection();
					});
				</script>
			</div>
		</div>
		<?php
	}
	?>
	<script>
		$(function (){
			//get all the related and store them correctly
			$('#edit_form').submit(function (e){
				e.preventDefault();
				for(var k in sortables){
					var table_name=$(sortables[k]).attr('id');
					$('input[name=related_table_'+table_name+']').val($(sortables[k]).sortable('serialize'));
				}
				$('#edit_form')[0].submit();
			});

			$('.edit-related').click(function (){
				var field_name=$(this).data('field');
				var display=field_name.toLowerCase().replace(/\b[a-z]/g, function (letter){
					return letter.toUpperCase();
				});
				var value=$(this).data('value');
				var table=$(this).data('table');
				var id=$(this).data('id');
				$('.edit-modal-field').html(display);
				$('.edit-modal-field').data('field', field_name);
				$('.edit-modal-value').val(value);
				$('.edit-modal-table').data('table', table);
				$('.edit-modal-table-id').data('id', id);
				$('#edit-modal').modal();
			});
			$('#edit-save').click(function (){
				var params={
					table: $('.edit-modal-table').data('table'),
					id: $('.edit-modal-table-id').data('id'),
					field: $('.edit-modal-field').data('field'),
					value: $('.edit-modal-value').val()
				};
				$.post('/api/save_related_field.html', params, function (data){
					if(typeof data.success!=='undefined'){
						$('#edit-modal').modal('toggle');
					}else if(typeof data.error!=='undefined'){
						var str='';
						for(var k in data.error){
							str+=data.error[k]+"\n";
						}
						alert(str);
						$('#edit-modal').modal('toggle');
					}else{
						alert('Something Went Wrong...');
						$('#edit-modal').modal('toggle');
					}
				});
			});
		});
	</script>
	<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Edit</h4>
				</div>
				<div class="modal-body">
					<div class="form-group row">
						<span class=' edit-modal-table' style='display:none'></span>
						<span class=' edit-modal-table-id' style='display:none'></span>
						<label class="col-md-3 edit-modal-field"></label>
						<div class="input-group col-md-9">
							<select class="form-control edit-modal-value" ><option value="">Please Select</option><option value="y">Yes</option><option value="n">No</option></select>								</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button id='edit-save' type="button" class="btn btn-primary" >Save changes</button>
				</div>
			</div>
		</div>
	</div>
</div>
