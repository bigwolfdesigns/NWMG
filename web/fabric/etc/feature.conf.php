<?php

$feature = array(
	'id'	 => array(
		'display'	 => 'Feature ID',
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
	'name'	 => array(
		'display'	 => 'Name',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type' => 'textarea',
		)
	),
	'field'	 => array(
		'display'	 => 'Feature Field',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type' => 'textarea',
		)
	),
	'value'	 => array(
		'display'	 => 'Feature Value',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type' => 'textarea',
		)
	)
);
