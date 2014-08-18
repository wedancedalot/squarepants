<?php
if (!defined('SP_ROOT')) exit;
/**
* Config class
*/
class SP_Config
{
	/**
	 * Config array
	 */
	private static $config = array();

	private function __construct(){}

	public static function set($value, $key)
	{
		if (empty($key) || empty($value)) {
			return false;
		}

		self::$config[$key] = $value;
	}
	
	public static function get($key)
	{
		if (empty($key)) {
			return false;
		}

		return !empty(self::$config[$key])? self::$config[$key] : false;
	}

	public static function debug()
	{
		var_dump(self::$config);
		return true;
	}
}