<?php

/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision GmbH - Connect.
 *
 * TechDivision_Properties is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * TechDivision_Properties is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 *
 * @package TechDivision_Properties
 */

require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Core.php';
require_once 'TechDivision/Properties/Properties.php';
require_once 'TechDivision/AOP/Interfaces/Cache.php';

/**
 * The Properties class represents a persistent set of properties.
 * The Properties can be saved to a stream or loaded from a stream.
 * Each key and its corresponding value in the property list is a string.
 *
 * A property list can contain another property list as its "defaults";
 * this second property list is searched if the property key is not
 * found in the original property list.
 *
 * Because Properties inherits from HashMap, the put method can be
 * applied to a Properties object. Their use is strongly discouraged
 * as they allow the caller to insert entries whose keys or values are
 * not Strings. The setProperty method should be used instead. If the
 * store or save method is called on a "compromised" Properties object
 * that contains a non-String key or value, the call will fail.
 *
 * @package TechDivision_Properties
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TDProject_Properties_Cached
    extends TechDivision_Properties_Properties 
{
	
	/**
	 * The cache instance to use.
	 * @var TDProject_Application_Cache
	 */
	protected $_cache = null;

	/**
	 * This member is TRUE if the sections should be parsed, else FALSE
	 */
	protected $_sections = false;

	/**
	 * Factory method.
	 *
	 * @param TechDivision_Properties_Properties $defaults
	 * 		Default properties to initialize the new ones with
	 * @return TDProject_Properties_Cached The initialized properties
	 */
	public static function create(
	    TechDivision_Properties_Properties $defaults = null) {
        return new TDProject_Properties_Cached($defaults);
	}

	/**
	 * The default constructor.
	 *
	 * @param TechDivision_Properties_Properties $defaults Default values
	 * @return void
	 */
	public function __construct(
	    TechDivision_Properties_Properties $defaults = null)
	{
		// call the parent constructor
		parent::__construct();
		// check if APC is available for caching
		if (extension_loaded('apc')) {
        	// log that APC will be used for caching
	        // initialize the cache backend
			$backend = 'Apc';
			$backendOptions = array();
		}
		// at least, use file caching
		else {
			// initialize the cache backend
			$backend = 'File';
			$backendOptions = array(
				'cache_dir' => '/tmp'
			);
		}
		// initialize the cache frontend
		$frontendOptions = array(
			'lifetime' => 7200,
			'automatic_serialization' => true
		);
		// instanciate the cache instance
		$this->_cache = Zend_Cache::factory(
			'TDProject_Application_Cache',
			$backend,
			$frontendOptions,
		    $backendOptions,
			true
		);
	}
	
	/**
	 * Returns the actual cache instance to use.
	 * 
	 * @return TDProject_Application_Cache The cache instance to use.
	 */
	public function getCache()
	{
		return $this->_cache;
	}

	/**
	 * Reads a property list (key and element pairs)
	 * from the passed file.
	 *
	 * @param string $file
	 * 		The path and the name of the file to load the properties from
	 * @param sections $sections
	 * 		Has to be true to parse the sections
	 * @return TechDivision_Properties_Properties
	 * 		The initialized properties
	 * @throws PropertyFileParseException
	 * 		Is thrown if an error occurse while parsing the property file
	 * @throws PropertyFileNotFoundException
	 * 		Is thrown if the property file passed as parameter does not
	 * 		exist in the include path
	 */
	public function load($file, $sections = false)
	{
		// create the cache key
		$cacheKey = str_replace(array(DIRECTORY_SEPARATOR, '.', '-'), '_', $file);
		// check if cached items are available
		if ($items = $this->getCache()->load($cacheKey)) {
			// if yes, load them
			$this->_items = $items;
		}
		else {
			// if not, load them from files
			parent::load($file, $sections);
			// and save them in the cache
			if (!$this->getCache()->save($this->_items, $cacheKey)) {
				throw new Exception('Error when saving properties in cache');
			}
		}
		// return the initialized properties
		return $this;
	}

	/**
	 * Searches for the property with the specified
	 * key in this property list.
	 *
	 * @param string $key Holds the key of the value to return
	 * @param string $section
	 * 		Holds a string with the section name to return the key for
	 * 		(only matters if sections is set to TRUE)
	 * @return string Holds the value of the passed key
	 * @throws NullPointerException
	 * 		Is thrown if the passed key, or, if sections are TRUE,
	 * 		the passed section is NULL
	 */
	public function getProperty($key, $section = null)
	{
		// initialize the property value
		$property = null;
		// check if the sections are included
		if ($this->_sections) {
			// if the passed section OR the passed key is NULL
			// throw an exception
			if ($section == null) {
				throw new TechDivision_Lang_Exceptions_NullPointerException(
					'Passed section is null'
				);
			}
			if ($key == null) {
				throw new TechDivision_Lang_Exceptions_NullPointerException(
					'Passed key is null'
				);
			}
			// if the section exists ...
			if (array_key_exists($section, $this->_items)) {
				// get all entries of the section
				$entries = new TechDivision_Collections_HashMap(
				    $this->_items[$section]
				);
				if (array_key_exists($key, $entries)) {
					// if yes set it
					$property = $entries[$key];
				}
			}
		} 
		else {
			// if the passed key is NULL throw an exception
			if ($key == null) {
				throw new TechDivision_Lang_Exceptions_NullPointerException(
					'Passed key is null'
			    );
			}
			// check if the property exists in the internal list
			if (array_key_exists($key, $this->_items)) {
				// if yes set it
				$property = $this->_items[$key];
			}
		}
		// return the property or null
		return $property;		
	}
}