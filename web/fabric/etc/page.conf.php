<?php

$page = array(
	'id'		 => array(
		'display'	 => 'Page ID',
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
	'name'		 => array(
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
	'alias'		 => array(
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
	'content'	 => array(
		'display'	 => 'Content',
		'show'		 => array(
			'list'	 => false,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'textarea',
			'class'	 => 'ckeditor'
		)
	),
	'active'	 => array(
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
	'title'		 => array(
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
	)
);
