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
require_once 'TechDivision/HttpUtils/HttpRequest.php';

// run the application
TDProject_Factory::get()
	->setRequest(TechDivision_HttpUtils_HttpRequest::singleton())
	->process();