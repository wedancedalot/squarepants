<?php
if (!defined('SP_ROOT')) exit;
/**
*  View Class
*/
class SP_View
{
	protected $_variables = array();

    public function __get($key)
    {
        return @$this->_variables[$key];
    }

    public function __set($key, $value)
    {
        $this->_variables[$key] = $value;
    }

    public function render($view)
	{
		if (empty($view)) {
			throw new Exception("Cannot render template: empty view param passed", 1);
			return false;
		}

		extract($this->_variables);
		ob_start();
		include SP_VIEWS . $view . '.php';
		$this->_variables = array();
		return ob_get_clean();
	}
}