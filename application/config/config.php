<?php
if (!defined('SP_ROOT')) exit;
/**
 * Basic Settings
 */
$config['title'] 	= 'Test Site';
$config['base_url'] = 'http://squarepants.tk';
$config['log_file'] = 'errors.log';

/**
 * DB Connection Settings
 */
$config['db']['driver']		= 'mysql'; // available options: 'mysql'
$config['db']['host'] 		= 'localhost';		
$config['db']['username'] 	= 'root';
$config['db']['password']	= '';
$config['db']['dbname'] 	= 'fancyproducts';
$config['db']['prefix'] 	= 'sp_';
