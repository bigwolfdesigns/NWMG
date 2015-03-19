<?php

$order_type = array(
	'id'	 => array(
		'display'	 => 'Order Type ID',
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
			'type'		 => 'text',
			'length'	 => '100',
			'required'	 => true,
		)
	),
);
