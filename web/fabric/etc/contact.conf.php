<?php

$contact						 = array();
$contact['customer_id']		 = array(
	'post'		 => true,
	'required'	 => false,
	'display'	 => 'Customer ID',
	'default'	 => 0,
);
$contact['name']				 = array(
	'post'		 => true,
	'required'	 => true,
	'display'	 => 'Name',
	'default'	 => '',
);
$contact['company']			 = array(
	'post'		 => true,
	'required'	 => true,
	'display'	 => 'Company',
	'default'	 => '',
);
$contact['email']			 = array(
	'post'		 => true,
	'required'	 => true,
	'display'	 => 'Email',
	'default'	 => '',
);
$contact['phone']			 = array(
	'post'		 => true,
	'required'	 => true,
	'display'	 => 'Phone',
	'default'	 => '',
);
$contact['fax']				 = array(
	'post'		 => true,
	'required'	 => false,
	'display'	 => 'Fax',
	'default'	 => '',
);
$contact['address_line_1']	 = array(
	'post'		 => true,
	'required'	 => false,
	'display'	 => 'Street Address Line 1',
	'default'	 => '',
);
$contact['address_line_2']	 = array(
	'post'		 => true,
	'required'	 => false,
	'display'	 => 'Street Address Line 2',
	'default'	 => '',
);
$contact['city']				 = array(
	'post'		 => true,
	'required'	 => true,
	'display'	 => 'City',
	'default'	 => '',
);
$contact['state_id']			 = array(
	'post'		 => true,
	'required'	 => true,
	'display'	 => 'State',
	'default'	 => '',
);
$contact['country_id']		 = array(
	'post'		 => true,
	'required'	 => true,
	'display'	 => 'Country',
	'default'	 => '',
);
$contact['zip_code']			 = array(
	'post'		 => true,
	'required'	 => false,
	'display'	 => 'Zip Code',
	'default'	 => '',
);
$contact['comment']			 = array(
	'post'		 => true,
	'required'	 => false,
	'display'	 => 'Comment',
	'default'	 => '',
);
