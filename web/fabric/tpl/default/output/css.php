@charset "UTF-8";
*{
<?php
if(isset($css) && !is_array($css)){
	$olf_k = '';
	foreach($css as $k=>$v){
		if($old_k!=$k){
			?>}
			<?=$k?>{
			<?php
		}
		?><?=$v?><?php
	}
}
?>
}