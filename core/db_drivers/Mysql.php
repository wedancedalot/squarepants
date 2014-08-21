<?php
if (!defined('SP_ROOT')) exit;
/**
 * MYQSL Connection driver
 */
class SP_DB_Mysql extends SP_Database
{
    private $handle = null;

    protected function __clone(){}

    public function __construct($host=false, $username=false, $password=false, $db=false)
    {
        if ($this->handle === null) {
            // Ensure reporting is setup correctly 
            mysqli_report(MYSQLI_REPORT_STRICT); 
            
            // Define error
            if (!defined('MYSQL_CONN_ERROR')) {
                define('MYSQL_CONN_ERROR', 'Unable to connect to database.'); 
            }

            $config = SP_Config::get('db');
            $this->handle = new mysqli($host, $username, $password, $db); 
        }
    }

    public function fetchAll($query)
    {
        $output = array();
        
        if ($result = $this->handle->query($query)) {

            while ($row = $result->fetch_assoc()) {
                $output[] = $row;
            }

            # Clear the query
            $result->free();
            return $output;
        }

        return false;
    }

    public function fetchRow($query)
    {
        if ($result = $this->handle->query($query)) {
            return $result->fetch_assoc();
        }

        return false;
    }

    public function fetchVar($query)
    {
        if ($result = $this->handle->query($query)) {

            $row = $result->fetch_assoc();
            if (!empty($row)) {
                $result->free();
                return current($row);
            }            
        }

        return false;
    }

    public function sanitize($string)
    {
        return $this->handle->real_escape_string($string);
    }

    public function execute($query)
    {
        return $this->handle->query($query);
    }

    public function affected()
    {
        return $this->handle->affected_rows;
    }

    public function insert_id()
    {
        return $this->handle->insert_id;
    }
}