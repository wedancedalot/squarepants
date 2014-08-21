<?php
if (!defined('SP_ROOT')) exit;
/**
* DB abstract class
*/

abstract class SP_Database
{
	public static function init()
	{
		$config = SP_Config::get('db');
		$driver = !empty($config['driver'])? ucfirst($config['driver']) : 'Mysql'; // setting mysql as default driver
	
		# Load db driver depending on config
		if (!file_exists(SP_CORE . 'db_drivers/' . $driver . '.php')) {
			throw new Exception("Please use a valid DB driver");			
		}
		
		require_once(SP_CORE . 'db_drivers/' . $driver . '.php');

		$class = 'SP_DB_' . $driver;
		return new $class($config['host'], $config['username'], $config['password'], $config['dbname']);
	}

	# List of function needed for implementing in all db drivers
	abstract function fetchAll($query);
	abstract function fetchRow($query);
	abstract function fetchVar($query);
	abstract function sanitize($string);
	abstract function execute($string);
	abstract function affected();
	abstract function insert_id();
}