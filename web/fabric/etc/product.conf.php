<?php

$product = array(
	'id'				 => array(
		'display'	 => 'Product ID',
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
	'name'				 => array(
		'display'	 => 'Name',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'text',
			'length' => '150',
			'required'=>true,
		)
	),
	'alias'				 => array(
		'display'	 => 'Alias',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'text',
			'length' => '150',
			'required'=>true,
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
			'transform'	 => array('y' => 'Yes', 'n' => 'No'),
			'default'=>'y'
		)
	),
	'featured'			 => array(
		'display'	 => 'Featured Product',
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
	'short_description'	 => array(
		'display'	 => 'Short Description',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'text',
			'length' => 75
		)
	),
	'description'		 => array(
		'display'	 => 'Description',
		'show'		 => array(
			'list'	 => false,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'textarea',
			'length' => 0
		)
	),
	'category_id'		 => array(
		'display'	 => 'Category',
		'show'		 => array(
			'list'	 => false,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'			 => 'select',
			'select_show'	 => 'name',
			'table'			 => 'category'
		)
	)
);
