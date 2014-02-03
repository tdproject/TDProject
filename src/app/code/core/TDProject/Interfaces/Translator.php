<?php

/**
 * TDProject_Interfaces_Translator
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

require_once 'TechDivision/Lang/String.php';
require_once 'TechDivision/Collections/ArrayList.php';

/**
 * @category    TDProject
 * @package     TDProject
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
interface TDProject_Interfaces_Translator {

    /**
     * Returns the translation for the passed key.
     *
     * If no translation can be found, the key itself will be returned.
     *
     * @param TechDivision_Lang_String $key
     * 		The key to return the translation for
     * @param TechDivision_Lang_String $module
     * 		The module name to return the translation for
     * @param TechDivision_Collections_ArrayList $parameter
     * 		Holds an ArrayList with parameters with replacements for the
     * 		placeholders in the resource string
     * @param TechDivision_Lang_String $default
     * 		Default value to attach if key is not available
     * @return string The translation
     */
    public function translate(
        TechDivision_Lang_String $key,
        TechDivision_Lang_String $module,
        TechDivision_Collections_ArrayList $parameter = null,
	    TechDivision_Lang_String $default = null);

	/**
	 * Attaches the passed resource message
	 * to the Application's resource bundle.
	 *
	 * @param TechDivision_Lang_String $key The resource key
	 * @param TechDivision_Lang_String $module The resource module
	 * @param TechDivision_Lang_String $value The resource message to attach
	 * @return void
	 */
	public function attach(
	    TechDivision_Lang_String $key,
	    TechDivision_Lang_String $module,
	    TechDivision_Lang_String $value);
}