<?php
if (!defined('SP_ROOT')) exit;
/**
* Controller class
*/
class SP_Controller
{	
	protected $view = null;
	protected $_models = array();

	public function __construct(){
		$this->view = new SP_View();
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
}