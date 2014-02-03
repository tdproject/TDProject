<?php

/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision GmbH - Connect.
 *
 * TechDivision_Generator is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * TechDivision_Generator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 *
 * @package TechDivision_Generator
 */

require_once 'TechDivision/Lang/String.php';
require_once 'TechDivision/Util/SystemLocale.php';
require_once 'TechDivision/Resources/PropertyResourceBundle.php';
require_once 'TDProject/Factory.php';
require_once 'TDProject/Test/View.php';
require_once 'Mock/Request.php';
require_once 'Mock/Controller.php';
require_once 'TDProject/Aspectable/Test.php';

/**
 * This is the test for the Integer class.
 *
 * @package TDProject
 * @author Tim Wagner <tw@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TDProject_ApplicationTest
    extends PHPUnit_Framework_TestCase {

    /**
     * The Application instance to test.
     * @var TDProject_Application
     */
    protected $_app = null;

    /**
     * Static log buffer for Aspect testing.
     * @var string
     */
    public static $logged = '';

    /**
     * Prepars the unit test.
     *
     * @return void
     */
    public function setUp()
    {
        // initialize the Application instance to test
        $this->_app = TDProject_Factory::get();
        $this->_app->cleanCache();
    }

    /**
     * Cleans up after running a test.
     *
     * @return void
     */
    public function tearDown()
    {
    	// $this->_app->cleanCache();
    }

    /**
     * Tests the method to convert and concatenate URL parameters
     * from an array with key/value pairs to a valid query string
     * parameter.
     *
	 * @return void
     */
    public function testProcessUrlParams()
    {
        // initialize the parameters
        $params = array(
        	'param1' => 'value1',
            'param2' => 'value2'
        );
        // process the params
        $processedParams = $this->_app->processUrlParams($params);
        // assert the size of the name/values pairs
        $this->assertEquals(2, sizeof($processedParams));
        // intialize the counter
        $counter = 1;
        // check the name/value pairs
        foreach ($processedParams as $param) {
            $this->assertEquals($param, "param$counter=value$counter");
            $counter++;
        }
    }

    /**
     * Tests the method to create a valid URL.
     *
     * @return void.
     */
    public function testGetUrl()
    {
        // initialize the parameters
        $params = array(
        	'param1' => 'value1',
            'param2' => 'value2'
        );
        // initialize the Mock request instance
        $request = new Mock_Request(
            array('SCRIPT_NAME' => 'test.php')
        );
        // set the request to use
        $this->_app->setRequest($request);
        // initialize the URL
        $url = $this->_app->getUrl($params);
        // check the generated URL
        $this->assertEquals('test.php?param1=value1&param2=value2', $url);
    }

    /**
     * Tests the translation functionality.
	 *
	 * @return void
     */
    public function testTranslate()
    {
		// initialize the ResourceBundle
        $resourceBundle =
            TechDivision_Resources_PropertyResourceBundle::getBundle(
    		    new TechDivision_Lang_String(
    		    	'TDProject/META-INF/testresources'
    		    ),
    	        TechDivision_Util_SystemLocale::create(
    	            TechDivision_Util_SystemLocale::GERMANY
    	        )
           	);
        // initialize a new Controller instance
		$controller = new Mock_Controller();
		$controller->setResources($resourceBundle);
        // set the Controller instance
		$this->_app->setController($controller);
        // translate some string
		$translated = $this->_app->translate(
		    new TechDivision_Lang_String('test.key'),
		    new TechDivision_Lang_String('')
		);
        // validate that the string has been validated correctly
		$this->assertEquals('Testwert', $translated);
    }

    /**
     * Tests the main process method.
	 *
	 * @return void
     */
    public function testProcess()
    {
		// initialize the ResourceBundle
        $resourceBundle =
            TechDivision_Resources_PropertyResourceBundle::getBundle(
    		    new TechDivision_Lang_String(
    		    	'TDProject/META-INF/testresources'
    		    ),
    	        TechDivision_Util_SystemLocale::create(
    	            TechDivision_Util_SystemLocale::GERMANY
    	        )
           	);
        // initialize a new Controller instance
		$controller = new Mock_Controller(
		    array('/test' => 'TDProject/Test/View.php')
		);
		$controller->setResources($resourceBundle);
        // set the Controller instance
		$this->_app->setController($controller);
        // initialize the parameters
        $params = array(
            'namespace' => 'TDProject',
            'module' => 'Test',
        	'path' => '/test',
            'method' => 'test',
        	'SCRIPT_NAME' => 'test.php'
        );
        // initialize the Mock request instance and invoke the process method
		$instance = $this->_app->process(new Mock_Request($params));
		// assert the return value
		$this->assertTrue($instance instanceof TDProject_Application);
    }

    /**
     * Test the pointcut initialization.
     *
     * @return void
     */
    public function testProcessPointcuts()
    {
		// process pointcuts
        $this->_app->processPointcuts();
		// load the pointcuts
        $pointcuts = $this->_app->getPointcuts();
        // iterate over the pointcuts
        foreach ($pointcuts as $pointcut) {
        	// try to load the Test pointcut
        	if ($pointcut->getClassName() == 'TDProject_Pointcuts_Logging') {
        		// check the name for the include file
        		$this->assertEquals(
        			'TDProject/Pointcuts/Logging.php',
        			$pointcut->getIncludeFile()
        		);
        		// check the regex for the method interceptor
        		$this->assertEquals(
        			'.* .*->some.*(.*)',
        			$pointcut->getMethodToIntercept()
        		);
        		// assert the method name to intercept with
        		$this->assertEquals(
        			'log',
        			$pointcut->getInterceptWithMethod()
        		);
        		// assert that found advice
        		$this->assertEquals(
        			TechDivision_AOP_Advice_Before::IDENTIFIER,
        			$pointcut->getAdvice()
        		);
        	}
        }
    }

    /**
     * Tests if object creation with AOP support works.
     *
     * @return void
     */
    public function testCreateWithAspect()
    {
		// process the pointcuts
        $this->_app->processPointcuts();
		// create a new AOP supported instance
        $instance = $this->_app->newInstance('TDProject_Aspectable_Test');
		// call a function that has to be intercepted
        $result = $instance->somefunction($arg1 = 'test');
		// check the intercepted result
        $this->assertEquals($arg1 . 'test', $result);
        $this->assertEquals($arg1, self::$logged);
    }

    /**
     * Tests if the clean cache method works.
     *
     * @return void
     */
    public function testProcessCache()
    {
		// add a value to the cache
    	$this->_app->getCache()->save('Test', 'test');
		// initialize the ResourceBundle
        $resourceBundle =
            TechDivision_Resources_PropertyResourceBundle::getBundle(
    		    new TechDivision_Lang_String(
    		    	'TDProject/META-INF/testresources'
    		    ),
    	        TechDivision_Util_SystemLocale::create(
    	            TechDivision_Util_SystemLocale::GERMANY
    	        )
           	);
        // initialize a new Controller instance
		$controller = new Mock_Controller(
		    array('/test' => 'TDProject/Test/View.php')
		);
		$controller->setResources($resourceBundle);
        // set the Controller instance
		$this->_app->setController($controller);
        // initialize the parameters
        $params = array(
            'namespace' => 'TDProject',
            'module' => 'Test',
        	'path' => '/test',
            'method' => 'test',
        	'SCRIPT_NAME' => 'test.php',
            'cleanCacheMode' => Zend_Cache::CLEANING_MODE_ALL
        );
        // initialize the Mock request instance and set it
		$this->_app->setRequest(new Mock_Request($params));
		// check if the cached value is still there
    	$this->assertEquals('Test', $this->_app->getCache()->load('test'));
		// process the cache
		$this->_app->processCache();
		// assert that value has been removed successfully
    	$this->assertFalse($this->_app->getCache()->load('test'));
    }

    /**
     * Tests if the clean cache method works with tags.
     *
     * @return void
     */
    public function testProcessCacheWithTags()
    {
		// add a value to the cache
    	$this->_app->getCache()
    		->save($value = 'Test', $key = 'test', $tags = array('testtag'));
		// initialize the ResourceBundle
        $resourceBundle =
            TechDivision_Resources_PropertyResourceBundle::getBundle(
    		    new TechDivision_Lang_String(
    		    	'TDProject/META-INF/testresources'
    		    ),
    	        TechDivision_Util_SystemLocale::create(
    	            TechDivision_Util_SystemLocale::GERMANY
    	        )
           	);
        // initialize a new Controller instance
		$controller = new Mock_Controller(
		    array('/test' => 'TDProject/Test/View.php')
		);
		$controller->setResources($resourceBundle);
        // set the Controller instance
		$this->_app->setController($controller);
        // initialize the parameters
        $params = array(
            'namespace' => 'TDProject',
            'module' => 'Test',
        	'path' => '/test',
            'method' => 'test',
        	'SCRIPT_NAME' => 'test.php',
            'cleanCacheMode' => Zend_Cache::CLEANING_MODE_MATCHING_TAG,
        	'cleanCacheTags' => $tags
        );
        // initialize the Mock request instance and set it
		$this->_app->setRequest(new Mock_Request($params));
		// check if the cached value is still there
    	$this->assertEquals($value, $this->_app->getCache()->load($key));
		// process the cache
		$this->_app->processCache();
		// assert that value has been removed successfully
    	$this->assertFalse($this->_app->getCache()->load($key));
    }
}