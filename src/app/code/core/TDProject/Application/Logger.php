<?php

/**
 * TDProject_Application_Logger
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

require_once "TDProject/Properties/Cached.php";
require_once "TechDivision/Logger/Interfaces/Logger.php";
require_once "TechDivision/Logger/System.php";
require_once "TechDivision/Logger/Exceptions/InvalidLogTypeException.php";

/**
 * @category    TDProject
 * @package     TDProject
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Application_Logger extends TechDivision_Logger_Logger
{

	/**
	 * This method initializes the passed property file or
	 * initializes a new one with default values.
	 *
	 * @param string $confFile Holds the path to the configuration file
	 * @return TechDivision_Properties_Properties The initialized properties
	 */
	protected static function _initializeProperties($confFile)
	{
        // initialize a new properties instance
        $properties = TDProject_Properties_Cached::create();
        // check if a configuration file was passed
	    if (!empty($confFile)) {
    		// if yes, load the properties from the configuration file
    		return $properties->load(
    		    $confFile
    		);
	    }
	    // if not, define new default properties
        $properties->setProperty(
            TechDivision_Logger_Logger::LOG_TYPE,
            TechDivision_Logger_System::LOG_TYPE_SYSTEM
        );
        $properties->setProperty(
            TechDivision_Logger_Abstract::LOG_LEVEL,
            TechDivision_Logger_Logger::LOG_ERR
        );
        // return the default properties
        return $properties;
	}
}