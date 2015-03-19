<?php

$order = array(
	'id'			 => array(
		'display'	 => 'Order ID',
		'show'		 => array(
			'list'	 => true,
			'add'	 => false,
			'edit'	 => false
		),
		'form'		 => array(
			'type'	 => 'text',
			'length' => '8',
		)
	),
	'customer_id'	 => array(
		'display'	 => 'Customer ID',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'		 => 'text',
			'length'	 => '8',
			'required'	 => true
		)
	),
	'order_type_id'	 => array(
		'display'	 => 'Order Type',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'			 => 'select',
			'select_show'	 => 'name',
			'table'			 => 'order_type',
			'required'		 => true,
		)
	),
	'assigned_to'	 => array(
		'display'	 => 'Assigned To',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'			 => 'select',
			'select_show'	 => 'CONCAT(first_name," ",last_name)',
			'table'			 => 'user',
		)
	),
	'date_added'	 => array(
		'display'	 => 'Date Added',
		'show'		 => array(
			'list'	 => true,
			'add'	 => false,
			'edit'	 => false
		),
		'form'		 => array(
			'type'		 => 'date',
			'default'	 => true,
		)
	),
	'name'			 => array(
		'display'	 => 'Name',
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
	'company'		 => array(
		'display'	 => 'Company',
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
	'phone'			 => array(
		'display'	 => 'Phone Number',
		'show'		 => array(
			'list'	 => false,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'text',
			'length' => '50'
		)
	),
	'fax'			 => array(
		'display'	 => 'Fax Number',
		'show'		 => array(
			'list'	 => false,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'text',
			'length' => '50'
		)
	),
	'address_line_1' => array(
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
	'address_line_2' => array(
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
	'city'			 => array(
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
	'state_id'		 => array(
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
	'country_id'	 => array(
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
	'zip_code'		 => array(
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
);
