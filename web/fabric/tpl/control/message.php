<?php
echo isset($message)?$message:"You've been redirected.";
?>
<a href='<?php echo lc('uri')->create_auto_uti(array(CLASS_KEY => 'control', TASK_KEY => 'login')) ?>' >Click here to log in.</a>
