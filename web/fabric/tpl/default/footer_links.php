<?php

$links = array();
foreach($footer_links as $footer){
	foreach($footer as $footer_link){
		if(!empty($footer_link)){
			$page	 = $footer_link['page'];
			$name	 = $footer_link['name'];
			$links[] = "<a href='/$page'>$name</a>";
		}
	}
}
echo implode(' | ', $links);
