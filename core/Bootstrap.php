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
    	$request  = ($_SERVER['REQUEST_URI'] == '/') ? false : trim(strtolower($_SERVER['REQUEST_URI']), DIRECTORY_SEPARATOR);
    	$routes = SP_Config::get('routes');

    	# If empty go to default controller
    	if (empty($request)) {
    		$this->dispatch();
    		return true;
    	}

		# Is there a literal match?  If so we're done
		if (!strstr($request, ':') && isset($routes[$request])) {
			$this->dispatch($routes[$request]['controller'], $routes[$request]['path'], $routes[$request]['action']);
			return true;
		}

		# Loop through the routes array looking for wild-cards
		$request = explode('/', $request);
		foreach ($routes as $route => $val) {

			# Skip this if has no wildcard 
			if (!strstr($route, ':')) {
				continue;
			}

			$keys = array_filter(explode('/', $route));
			$match = true;
			$params = array(); // Array to be used for getting uri parameters
			foreach ($keys as $key => $uri_part) {
				if ($uri_part{0} == ':') {
					# This is regexp: check whether it matches the request
					$regexp = $val['regexp'][substr($uri_part, 1)];
					if (!preg_match('#^'.$regexp.'$#', $request[$key])) {
						$match = false;
						break;
					}
					$params[substr($uri_part, 1)] = $request[$key];
				} elseif($uri_part != $request[$key]) {
					# This is text and text doesn't match
					$match = false;
					break;
				}
			}
			if ($match) {
				$this->dispatch($routes[$route]['controller'], $routes[$route]['path'], $routes[$route]['action'], $params);
				return true;
			}
		}

    	switch (SP_ENVIRONMENT) {
			case 'development':
				throw new Exception('This route is not set! Please add it to: ' .SP_APPLICATION. 'config'.'/routes.php', 1);
				break;
			default:
			case 'production':
				header('HTTP/1.1 301 Moved Permanently');
		        header("Location: /", TRUE, 301);
			break;
		}

		return false;
    }

    private function dispatch($controller = null, $action = null, $path = null, $params = false)
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

    	if (!empty($params)) {
    		SP_Config::set($params,'params');
    	}

    	$controller = ucfirst(strtolower($controller)).'Controller';
    	$action 	= strtolower($action) . 'Action';

    	require_once(SP_CONTROLLERS . $path . $controller . '.php');

    	$squarepants = new $controller;
    	$squarepants->$action();
    }
}