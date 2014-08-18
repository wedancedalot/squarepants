<?php
# # # # # # # # # # # # # # # #
# Basic constants
define('SP_ENVIRONMENT', 				'development'); //error output level(development, production)
define('SP_ROOT',                       dirname(__FILE__));
define('SP_APPLICATION',                SP_ROOT.'/application/');
define('SP_CORE',                       SP_ROOT.'/core/');
define('SP_LIBRARIES',                  SP_ROOT.'/libraries/');
define('SP_LOGS',                  		SP_ROOT.'/logs/');
define('SP_CONTROLLERS',                SP_APPLICATION.'controller/');
define('SP_VIEWS',                      SP_APPLICATION.'view/');
define('SP_MODELS',                     SP_APPLICATION.'model/');
define('SP_DEFAULT_CONTROLLER',			'index');
define('SP_DEFAULT_ACTION',				'index');

require_once(SP_CORE.'Bootstrap.php');
$bootstrap = new SP_Bootstrap();
$bootstrap->init();