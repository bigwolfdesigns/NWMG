<?php
/**
 * this will take in an array
 * and it will output an CSV file
 */
if(!isset($report_data) || !is_array($report_data) || count($report_data)<=1){
	//nothing to output
	echo '';
	return;
}
$csv = array();
//formatting the first line
foreach($report_data as $k=>$v){
	foreach($v as $k1=>$v1){
		if($k==0){
			$csv[$k][$k1] = is_array($v1)?$v1['value']:$v1;
		} else {
			$field_type		= isset($report_data[0][$k1]['type'])?$report_data[0][$k1]['type']:'';
			$field_value	= is_array($v1)?$v1['value']:$v1;
			if($field_type==''){
				is_numeric($field_value)	&& $field_type = 'number';
			}
			$field_type=='' && $field_type = 'text';
			$csv[$k][$k1] = $this->grab('report/_formatting_field', array(
															'type'		=>$field_type,
															'value'		=>$field_value,
															'output'	=>'csv',
															));
		}
	}
}
$f = fopen('php://output', 'w');
foreach($csv as $k=>$v){
	fputcsv($f, $v, ',', '"');
}
fclose($f);
?>