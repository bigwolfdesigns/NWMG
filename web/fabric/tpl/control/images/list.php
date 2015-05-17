<link href="/css/dropzone/dropzone.css" type="text/css" rel='stylesheet'>
<style>
	.image-wrapper{
		padding-top:15px;
	}
</style>
<div class="row">
    <div class="col-lg-12">
		<h1 class="page-header"><?php echo $display_table ?></h1>
    </div>
</div>
<form method='POST' action="<?php echo lc('uri')->create_auto_uri(array(CLASS_KEY => 'image', TASK_KEY => 'upload', 'json', 1)) ?>"  id="image-dropzone" class='dropzone' enctype="multipart/form-data">
	<div class="dz-message">
		Drop files here or click to upload.
	</div>
	<input name='upload' type="submit" value="Upload" />
</form>
<?php
if(isset($errors) && is_array($errors) && count($errors) > 0){
	?>
	<div class="error">
		<?php
		foreach($errors as $message){
			?>
			<div style="margin:20px;padding:10px;font-size:18px;color:#990000;background-color:#effdbd;text-align:center;border:2px solid #990000;"><?php echo $message; ?></div>
			<?php
		}
		?>
	</div>
	<?php
}
if($rows && count($rows) > 0){
	?>
	<div class='row image-wrapper'>
		<?php
		foreach($rows as $k => $row){
			$id		 = $row['id'];
			$name	 = $row['name'];
			?>
			<div class="col-xs-6 col-md-3" style='border: dotted 1px #0087F7;margin:5px'>
				<?php
				foreach($_config as $k => $_conf){
					$v		 = $row[$k];
					$form	 = isset($_conf['form'])?$_conf['form']:array();
				}
				?>
				<a style='width:150px;height:150px;overflow:auto;float:left' href="<?php echo lc('uri')->create_auto_uri(array(CLASS_KEY => 'image', TASK_KEY => 'edit', 'id' => $id)); ?>" class="thumbnail">
					<img src="<?php echo ll('limages')->get_image($id) ?>"/>
				</a>
				<a style="color: red;float:right;" href="<?php echo lc('uri')->create_auto_uri(array(CLASS_KEY => 'image', TASK_KEY => 'delete', 'id' => $id)); ?>" >DELETE</a>
				<div class="clearfix"></div>
				<p style="min-height:3em"><?php echo $name ?></p>
				<p class='pull-right'><?php echo $id ?></p>
			</div>
		<?php }
		?></div><?php
}
?>
<div style='clear:both'></div>
<div style="float:left;width:44%"><?php echo $this->pagination($_config, $row_count); ?></div>
<script type="text/javascript" src="/js/dropzone/dropzone.js"></script>
<script type="text/javascript">
	Dropzone.options.imageDropzone={// The camelized version of the ID of the form element
		// The configuration we've talked about above
		autoProcessQueue: false,
		uploadMultiple: true,
		parallelUploads: 100,
		maxFiles: 100,
		// The setting up of the dropzone
		init: function (){
			var myDropzone=this;
			// First change the button to actually tell Dropzone to process the queue.
			this.element.querySelector("input[type=submit]").addEventListener("click", function (e){
				// Make sure that the form isn't actually being sent.
				e.preventDefault();
				e.stopPropagation();
				myDropzone.processQueue();
			});

			// Listen to the sendingmultiple event. In this case, it's the sendingmultiple event instead
			// of the sending event because uploadMultiple is set to true.
			this.on("sendingmultiple", function (){
				// Gets triggered when the form is actually being sent.
				// Hide the success button or the complete form.
			});
			this.on("successmultiple", function (files, response){
				// Gets triggered when the files have successfully been sent.
				// Redirect user or notify of success.
			});
			this.on("errormultiple", function (files, response){
				// Gets triggered when there was an error sending the files.
				// Maybe show form again, and notify user of error
			});
		}

	}
</script>