<?php

$category = array(
	'id'			 => array(
		'display'	 => 'Category ID',
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
	'parent_id'		 => array(
		'display'	 => 'Parent',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'			 => 'select',
			'select_show'	 => 'name',
			'table'			 => 'category'
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
	'alias'			 => array(
		'display'	 => 'Alias',
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
	'active'		 => array(
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
	'title'			 => array(
		'display'	 => 'Page Title',
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
	'description'	 => array(
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
	'home'			 => array(
		'display'	 => 'Home Category',
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
	'nav'			 => array(
		'display'	 => 'Navigation Category',
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
	'catalog'		 => array(
		'display'	 => 'Catalog Category',
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
);
