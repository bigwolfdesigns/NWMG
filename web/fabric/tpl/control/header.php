<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title><?php echo $title; ?></title>	<?php
		//loading meta tags
		foreach($meta as $key => $v1){
			echo '<meta ';
			foreach($v1 as $name => $value){
				echo $name.'="'.htmlspecialchars($value, ENT_QUOTES).'" ';
			}
			echo '/>';
		}
		//loading CSS
		foreach($link as $key => $v1){
			echo '<link ';
			foreach($v1 as $name => $value){
				echo $name.'="'.htmlspecialchars($value, ENT_QUOTES).'" ';
			}
			echo '/>';
		}
		//loading the JS
		foreach($script as $key => $v1){
			echo '<script ';
			foreach($v1 as $name => $value){
				echo $name.'="'.htmlspecialchars($value, ENT_QUOTES).'" ';
			}
			echo '></script>';
		}
		//unloading not needed vars from the display class to freeup memory
		$this
				->delete('meta')
				->delete('link')
				->delete('script');
		?>
	</head>
	<body>
		<?php if(ll('display')->get_hide_show('nav')){ ?>
			<div id="wrapper">
				<?php echo ll('client')->show_control_nav(); ?>
				<div id="page-wrapper">
					<?php
					}					