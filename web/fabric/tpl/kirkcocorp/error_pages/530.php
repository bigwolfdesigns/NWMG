<div style="margin:10px auto 0;width:500px;padding:10px;border:1px solid #ff3333;text-align:center;">
	<h1>Permission Error</h1>
	<p>You do not have the required permission to access this page.<br />&nbsp;<br /><?php
//Access Denied
$referrer = lc('uri')->get_referrer();
if($referrer!=''){
	?><a href="<?=$referrer?>">Return To Previous Page</a><?php
} else {
?><a href="/">Return Home</a><?php
}?>
</p>
</div>