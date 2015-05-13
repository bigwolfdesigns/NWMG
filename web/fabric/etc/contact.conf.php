<?php

$contact = array(
	'id'			 => array(
		'display'	 => 'Contact ID',
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
		'display'	 => 'Customer',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'			 => 'select',
			'select_show'	 => 'CONCAT(first_name," ",last_name)',
			'table'			 => 'customer'
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
			'table'			 => 'user'
		)
	),
	'status'		 => array(
		'display'	 => 'Status',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'		 => 'select',
			'transform'	 => array('open' => 'Open', 'quoted' => 'Quoted', 'replied' => 'Replied', 'received_order' => 'Received Order', 'other' => 'Other'),
		)
	),
	'name'			 => array(
		'display'	 => 'Name',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'		 => 'text',
			'length'	 => '30',
			'required'	 => true,
		)
	),
	'company'		 => array(
		'display'	 => 'Company',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'		 => 'text',
			'length'	 => '30',
			'required'	 => true,
		)
	),
	'email'			 => array(
		'display'	 => 'Email',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'required'	 => true,
			'type'		 => 'text',
			'length'	 => '50'
		)
	),
	'phone'			 => array(
		'display'	 => 'Phone',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'		 => 'text',
			'length'	 => '50',
			'required'	 => true,
		)
	),
	'fax'			 => array(
		'display'	 => 'Fax',
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
			'length' => '50'
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
			'length' => '50'
		)
	),
	'city'			 => array(
		'display'	 => 'City',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'		 => 'text',
			'length'	 => '50',
			'required'	 => true,
		)
	),
	'state_id'		 => array(
		'display'	 => 'State',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'			 => 'select',
			'select_show'	 => 'name',
			'table'			 => 'state',
			'required'		 => true,
		)
	),
	'country_id'	 => array(
		'display'	 => 'Country',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'			 => 'select',
			'select_show'	 => 'name',
			'table'			 => 'country',
			'required'		 => true,
		)
	),
	'zip_code'		 => array(
		'display'	 => 'Zip',
		'show'		 => array(
			'list'	 => false,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'text',
			'length' => '30'
		)
	),
	'comment'		 => array(
		'display'	 => 'Comment',
		'show'		 => array(
			'list'	 => false,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'textarea',
			'length' => '255'
		)
	),
);
