<?php
if (!defined('SP_ROOT')) exit;
/**
 * DB Connection class
 */
class SP_DB
{
	 /**
     * Call this method to get singleton
     */
    public static function getInstance()
    {
        static $inst = null;
        if ($inst === null) {
           	// Ensure reporting is setup correctly 
			mysqli_report(MYSQLI_REPORT_STRICT); 
			
			// Define error
			define("MYSQL_CONN_ERROR", "Unable to connect to database."); 

			$config = SP_Config::get('db');
		    $inst = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']); 
        }

        return $inst;
    }

    protected function __construct(){}
    protected function __clone(){}
}