<?php

$customer = array(
	'id'				 => array(
		'display'	 => 'Customer ID',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => false
		),
		'form'		 => array(
			'type'		 => 'text',
			'length'	 => '8',
			'required'	 => true
		)
	),
	'first_name'		 => array(
		'display'	 => 'First Name',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'text',
			'length' => '75',
		)
	),
	'last_name'			 => array(
		'display'	 => 'Last Name',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'text',
			'length' => '75',
		)
	),
	'company'			 => array(
		'display'	 => 'Company',
		'show'		 => array(
			'list'	 => false,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'text',
			'length' => 75
		)
	),
	'email'				 => array(
		'display'	 => 'Email',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'text',
			'length' => '150'
		)
	),
	'active'			 => array(
		'display'	 => 'Active',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'		 => 'select',
			'transform'	 => array('y' => 'Yes', 'n' => 'No')
		)
	),
	'address_line_1'	 => array(
		'display'	 => 'Street Address(Line 1)',
		'show'		 => array(
			'list'	 => false,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'text',
			'length' => 100
		)
	),
	'address_line_2'	 => array(
		'display'	 => 'Street Address(Line 2)',
		'show'		 => array(
			'list'	 => false,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'text',
			'length' => 100
		)
	),
	'city'				 => array(
		'display'	 => 'City',
		'show'		 => array(
			'list'	 => false,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'text',
			'length' => '75'
		)
	),
	'state_id'			 => array(
		'display'	 => 'State',
		'show'		 => array(
			'list'	 => false,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'			 => 'select',
			'select_show'	 => 'name',
			'table'			 => 'state'
		)
	),
	'country_id'		 => array(
		'display'	 => 'Country',
		'show'		 => array(
			'list'	 => false,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'			 => 'select',
			'select_show'	 => 'name',
			'table'			 => 'country'
		)
	),
	'zip_code'			 => array(
		'display'	 => 'Zip Code',
		'show'		 => array(
			'list'	 => false,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'text',
			'length' => '20'
		)
	),
	'date_added'		 => array(
		'display'	 => 'Date Added',
		'show'		 => array(
			'list'	 => true,
			'add'	 => false,
			'edit'	 => false
		),
		'form'		 => array(
			'type' => 'date',
		)
	),
	'date_registered'	 => array(
		'display'	 => 'Date Registered',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type' => 'date',
		)
	),
);
