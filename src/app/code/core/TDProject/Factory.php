<?php

/**
 * TDProject_Factory
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

require_once 'TechDivision/Lang/Object.php';
require_once 'TDProject/Application.php';

/**
 * @category    TDProject
 * @package     TDProject
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Factory extends TechDivision_Lang_Object
{

    /**
     * The singleton.
     * @var TDProject_Application
     */
    protected static $_instance = null;

    /**
     * Protected constructor to avoid
     * direct class inizialization.
     *
     * @return void
     */
    protected function __construct()
    {
        // To avoid direct instanciation
    }

    /**
     * Singleton method for getting the Application
     * instance.
     *
	 * @return TDProject_Application The instance
     */
    public static function get()
    {
    	// check if already an instance is available
        if (self::$_instance == null) {
        	// create a new Application instance
            self::$_instance = new TDProject_Application();
        }
        // return the instance
        return self::$_instance;
    }
}