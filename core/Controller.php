<?php
if (!defined('SP_ROOT')) exit;
/**
* Controller class
*/
class SP_Controller
{	
	protected $view = null;
	protected $_models = array();
	protected $_params = array();

	public function __construct(){
		$this->view = new SP_View();
		$this->_params = SP_Config::get('params');
	}

	public function collapse($html)
	{
		$html = preg_replace("~\t~",'',$html);
		$html = preg_replace("~(\n)+~","\n",$html);
		
		return $html;
	}

	public function load($filename)
	{
		if (!array_key_exists($filename, $this->_models)) {
			require_once(SP_MODELS . $filename . '.php');
			$model = ucfirst($filename);
			$this->_models[$filename] = new $model; 
		}
		return true;
	}

	public function __get($key)
    {
        return @$this->_models[$key];
    }

    /*
    * Method to extract variables from routing
    */
    public function param($key)
    {
    	if (!empty($key) && !empty($this->_params[$key])) {
    		return $this->_params[$key];
    	}

    	return false;
    }
}