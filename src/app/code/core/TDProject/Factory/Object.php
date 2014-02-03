<?php

/**
 * TDProject_Factory_Object
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

require_once 'TechDivision/Lang/Object.php';

/**
 * @category    TDProject
 * @package     TDProject
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Factory_Object extends TechDivision_Lang_Object
{
    /**
     * The singleton instance.
     * @var TDProject_Factory_Object
     */
    protected static $_instance = null;

    /**
     * Protect class because we want to use singleton pattern.
     *
     * @return void
     */
    protected function __construct()
    {
        // prevents class from direct initialization (singleton)
    }

    /**
     * Singleton method.
     *
     * @return TDProject_Factory_Object The singleton
     */
    public static function get()
    {
        // initialize and return the instance
        if (self::$_instance == null) {
            self::$_instance = new TDProject_Factory_Object();
        }
        return self::$_instance;
    }

    /**
     * Factory method for a new instance of the
     * class with the passed name.
     *
     * @param string Name of the class to create and return the oject for
     * @param array The arguments passed to the classes constructor
	 * @return TechDivision_Lang_Object The instance
     */
    public function newInstance($className, array $arguments = array())
    {
        // instanciate the return the object
        $reflectionClass = new ReflectionClass($className);
        // check if a constructor is available
        if ($reflectionClass->hasMethod('__construct')) {
        	return $reflectionClass->newInstanceArgs($arguments);
        }
        // create a new instance WITHOUT constructor
        return $reflectionClass->newInstance();
    }
}