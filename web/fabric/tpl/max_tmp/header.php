<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title><?php echo $title; ?></title>
		<meta name="description" content="Kirkco Corporation is a distributor of adhesive, sealant and lubricant metering, mixing, and dispensing equipment. Kirkco offers the following product lines: single component systems, two component systems, spraying and coating, hot melt adhesive and cold glue adhesive systems. " />
		<meta name="keywords" content="adhesive applicator,adhesive applicators,adhesive dispensing equipment,adhesive epoxy,dispensing equipment,hot melt adhesive,hot melt adhesives,hot melt equipment,hot melt glue gun,industrial adhesives,industrial coating,industrial coatings" />
		<?php
		//loading meta tags
		foreach($meta as $key => $v1){
			echo '<meta ';
			foreach($v1 as $name => $value){
				echo $name.'="'.htmlspecialchars($value, ENT_QUOTES).'" ';
			}
			echo '/>';
		}
		?>
		<link type="text/css" rel="stylesheet" title="default" href="/css/bootstrap/bootstrap.css" media="all" />
		<link type="text/css" rel="stylesheet" title="default" href="/css/bootstrap/bootstrap.css.map" media="all" />
		<?php
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
		<div class='container'>
			<?php
			echo ll('client')->show_top_menu();
			if(lc('uri')->get(CLASS_KEY, '') == 'home'){
				echo ll('client')->show_banner();
			}
			?>
			<div id="main-content" clas="row">
				<?php
				if($this->get_hide_show('nav')){
					echo ll('client')->show_nav_menu();
				}
				