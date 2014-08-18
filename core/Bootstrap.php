<?php
if (!defined('SP_ROOT')) exit;
/**
 * Bootstrap class. Magic starts here
 */
class SP_Bootstrap
{
	function __construct()
	{
		# Include base classes
		foreach (scandir(dirname(__FILE__)) as $filename) {
		    $path = dirname(__FILE__) . '/' .$filename;
		    if (is_file($path) && $path != __FILE__) {
				require_once($path);
		    }
		}

		# Include config classes
		require_once(SP_APPLICATION . 'config/config.php');
		require_once(SP_APPLICATION . 'config/routes.php');
		array_walk($config, array('SP_Config', 'set'));
		
		# Include all front-controller classes
		foreach (scandir(SP_APPLICATION.'pattern') as $filename) {
		    $path = SP_APPLICATION . 'pattern' . '/' . $filename;
		    if (is_file($path)) {
				require_once($path);
		    }
		}

		set_error_handler(array('SP_Errorhandler', 'handleErrors'));
		set_exception_handler(array('SP_Errorhandler', 'handleExceptions'));
	}

    public function init()
    {
    	$route  = ($_SERVER['REQUEST_URI'] == '/') ? false : trim(strtolower($_SERVER['REQUEST_URI']), DIRECTORY_SEPARATOR);
    	$routes = SP_Config::get('routes');

    	# If empty go to default controller
    	if (empty($route)) {
    		$this->dispatch();
    		return true;
    	}

		# Is there a literal match?  If so we're done
		if (isset($routes[$route])) {
			//return $this->_set_request(explode('/', $this->routes[$uri]));
		}

		# Loop through the route array looking for wild-cards
		foreach ($routes as $key => $val) {
			# Skip this if has no wildcard 
			if (!strstr($key, ':')) {
				continue;
			}

			# Build a regexp;
			$key = array_filter(explode('/', $key));
			foreach ($key as &$element) {
				if ($element{0} == ':') {
					$element = $val['regexp'][substr($element, 1)];
				}
			} 
			$key = implode('\/', $key);
			# Does the RegEx match?
			if (preg_match('#^'.$key.'$#', $route)) {
				$this->dispatch($val['controller'], $val['path'], $val['action']);
				return true;
			}
		}

    	switch (SP_ENVIRONMENT) {
			case 'development':
				throw new Exception("This route is not set: " . $route . '. Please add it to .'.SP_APPLICATION.'config'.'/routes.php', 1);
				break;
			default:
			case 'production':
				header('HTTP/1.1 301 Moved Permanently');
		        header("Location: /", TRUE, 301);
			break;
		}
		return false;
    }

    private function dispatch($controller = null, $action = null, $path = null)
    {    
    	if (empty($controller)) {
    		$controller = SP_DEFAULT_CONTROLLER;
    	}

    	if (empty($action)) {
    		$action = SP_DEFAULT_ACTION;
    	}

    	if (!empty($path)) {
    		$path = rtrim(strtolower($path), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    	}

    	$controller = ucfirst(strtolower($controller)).'Controller';
    	$action 	= strtolower($action) . 'Action';

    	require_once(SP_CONTROLLERS . $path . $controller . '.php');

    	$squarepants = new $controller;
    	$squarepants->$action();
    }
}