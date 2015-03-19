<?php

$order_comment = array(
	'order_id'	 => array(
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
	'id'		 => array(
		'display'	 => 'Order Comment ID',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'	 => 'text',
			'length' => '8',
		)
	),
	'comment'	 => array(
		'display'	 => 'Comment',
		'show'		 => array(
			'list'	 => true,
			'add'	 => true,
			'edit'	 => true
		),
		'form'		 => array(
			'type'		 => 'text',
			'length'	 => '0',
			'required'	 => true,
		)
	),
);
