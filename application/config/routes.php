<?php
if (!defined('SP_ROOT')) exit;
/*
*	Routes (add without final trailing slash)
*/
$config['routes'] = array(

	'/' => array(
		'path'			=>  '',
		'controller'	=>  '',
		'action'		=>  '',
	),

	'home' => array(
		'path'			=>  '',
		'controller'	=>  '',
		'action'		=>  '',
	),

	'statistics/:num/:letters' => array(
		'path'			=>  '',
		'controller'	=>  '',
		'action'		=>  '',
		'regexp'		=> array(
			'letters' => '[0-9]*[a-zA-Z0-9\-]*\.html',
			'num' 	=> '[0-9]*'
		)
	),

);