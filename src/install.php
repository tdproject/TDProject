<?php

// set the session timeout to unlimited
ini_set('session.gc_maxlifetime', 0);
ini_set('zend.enable_gc', '0');

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('BP', dirname(__FILE__));

$paths[] = BP . DS . 'var' . DS . 'cache';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'local';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'community';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'core';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'lib';

// set the new include path
set_include_path(implode(PS, $paths) . PS . get_include_path());

require_once 'TDProject/Factory.php';
require_once 'TDProject/Application/Logger.php';
require_once 'TechDivision/HttpUtils/HttpRequest.php';

// initialize the long options
$longOpts = array(
	'method:',		// the setup method 'install' or 'update'
	'db_name:',		// the database name to use
	'db_host:',		// the database host to use
	'db_user:',		// the database user to use
	'db_pass:',		// the database password to use
	'db_charset:'	// the database charset to use
);

// parse the console options	
if (($opts = getopt('', $longOpts)) === false) {
	echo 'Missing initialization parameters!';
	exit;
}

// load the Request instance
$request = TechDivision_HttpUtils_HttpRequest::singleton();
$request->setAttribute('path', '/install');
$request->setAttribute('method', $opts['method']);
$request->setAttribute('options', $opts);

// initialize a console logger instance
$logProperties = 'TDProject/WEB-INF/log4php.console.properties';
$logger = TDProject_Application_Logger::forClass(__CLASS__, $logProperties);

// run the application
TDProject_Factory::get()
	->setLogger($logger)
	->setRequest($request)
	->process();