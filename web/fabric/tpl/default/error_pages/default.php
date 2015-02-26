Problem displaing the Page <b><?=lc('uri')->get_uri();?></b><br />
<?php
//page not found
$referrer = lc('uri')->get_referrer();
if($referrer!=''){
	?><a href="<?=$referrer?>">Please click here to go to the previous page</a><?php
}?>
