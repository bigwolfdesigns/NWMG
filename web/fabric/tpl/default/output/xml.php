<?php
if(!isset($no_xml_head) || $no_xml_head !== true){
	?><?= '<?' ?>xml version="1.0" encoding="utf-8" <?= '?>' ?><?php
}
if(isset($xml_header)) echo $xml_header."\n";
if(!function_exists('_tt_display_node')){
	function _tt_display_node($xml, $parent = '', $dist = 0){
		if(isset($xml) && is_array($xml)){
			foreach($xml as $key=> $value){
				$xtr_key = '';
				if(is_numeric(substr($key, 0, 1))){
					$key = $parent.'_'.$key;
				}
				if(stristr($key, '|') !== false){
					$key = substr($key, 0, strpos($key, '|'));
				}
				if(stristr($key, ' ') !== false){
					$xtr_key = substr($key, strpos($key, ' '));
					$key	 = substr($key, 0, strpos($key, ' '));
				}
				echo str_repeat('   ', $dist).'<'.trim($key.' '.$xtr_key);
				if(is_null($value)){
					echo '/>';
				}else{
					echo '>';
					if(is_array($value)){
						echo "\n"; //subnode
						_tt_display_node($value, $key, $dist + 1);
					}else{
						$text					 = htmlentities($value, ENT_COMPAT, 'UTF-8');
						// XML character entity array from Wiki
						// Note: &apos; is useless in UTF-8 or in UTF-16
						$arr_xml_special_char	 = array("&quot;", "&amp;", "&apos;", "&lt;", "&gt;");

						// Building the regex string to exclude all strings with xml special char
						$arr_xml_special_char_regex = "(?";
						foreach($arr_xml_special_char as $key=> $value){
							$arr_xml_special_char_regex .= "(?!$value)";
						}
						$arr_xml_special_char_regex .= ")";

						// Scan the array for &something_not_xml; syntax
						$pattern = "/$arr_xml_special_char_regex&([a-zA-Z0-9]+;)/";

						// Replace the &something_not_xml; with &amp;something_not_xml;
						$replacement = '&amp;${1}';
						echo preg_replace($pattern, $replacement, $text);
					}
					echo '</'.$key.'>';
				}
				echo "\n";
			}
		}
	}
}
if(isset($xml)) _tt_display_node($xml, '', 0);
if(isset($xml_footer)) echo $xml_footer."\n";
?>