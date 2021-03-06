<style>
    .pagination{
        display: inline-block;
        padding-left: 0;
        margin: 20px 0;
        border-radius: 4px;
    }
    .pagination>li{
        display:inline;
    }
    .pagination>li:first-child>a, .pagination>li:first-child>span {
        margin-left: 0;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }
    .pagination>.disabled>a, .pagination>.disabled>a:hover, .pagination>.disabled>a:focus {
        color: #777;
        cursor: not-allowed;
        background-color: #fff;
        border-color: #ddd;
    }
    .pagination>.disabled>span, .pagination>.disabled>span:hover, .pagination>.disabled>span:focus {
        color: #777;
        cursor: not-allowed;
        background-color: #fff;
        border-color: #ddd;
    }
    .pagination>li>a, .pagination>li>span {
        position: relative;
        float: left;
        padding: 6px 12px;
        margin-left: -1px;
        line-height: 1.42857143;
        color: #428bca;
        text-decoration: none;
        background-color: #fff;
        border: 1px solid #ddd;
    }
    .pagination>.active>a, .pagination>.active>span, .pagination>.active>a:hover, .pagination>.active>span:hover, .pagination>.active>a:focus, .pagination>.active>span:focus {
        z-index: 2;
        color: #fff;
        cursor: default;
        background-color: #428bca;
        border-color: #428bca;
    }
    .ellipsis {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        display:block;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo $display_table ?></h1>
    </div>
</div>
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
<?php } ?>
<!--<h2><?php echo $display_table." List" ?></h2>-->

<?php echo $this->grab('filters'); ?>
<div style='clear:both'></div>
<div width='100%' style='overflow:scroll'>
	<?php echo $this->grab('list_table'); ?>
</div>

<div style="float:left;width:44%"><?php echo $this->pagination($_config, $row_count); ?></div>
<script type="text/javascript">
	function confirm_delete(txt){
		return confirm(txt);
	}
</script>