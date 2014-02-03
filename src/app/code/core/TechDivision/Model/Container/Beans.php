<?php

/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision GmbH - Connect.
 *
 * TechDivision_Model is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * TechDivision_Model is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 *
 * @package TechDivision_Model
 */

require_once 'TechDivision/Lang/Object.php';
require_once 'TechDivision/Model/Interfaces/Bean.php';

/**
 * This is the container to handle the information of
 * a storable in the container.
 *
 * @package TechDivision_Model
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TechDivision_Model_Container_Beans 
	extends TechDivision_Lang_Object
{

	/**
	 * The cache tag.
	 * @var string
	 */
	const CACHE_TAG = 'container_beans';

	/**
	 * The cache key prefix.
	 * @var string
	 */
	const CACHE_KEY_PREFIX = 'bean_';

    /**
     * Holds the class name of the bean.
     * @var string
     */
    protected $_name = null;

    /**
     * The cache instance to use.
     * @var TechDivision_Model_Interfaces_Container_Cache
     */
    protected $_cache = null;

	/**
	 * Temporary bean storage.
	 * @var array
	 */
    protected $_beans = array();

    /**
     * Initializes the container for the bean instances and passes
     * the cache instance to use.
     *
     * @param TechDivision_Model_Interfaces_Container_Cache $cache
     * 		The cache instance to use
     */
    public function __construct(
    	TechDivision_Model_Interfaces_Container_Cache $cache)
    {
    	$this->setCache($cache);
    }

    /**
     * Persists the beans to the cache.
     *
     * @return void
     */
    public function __destruct()
    {
    	// iterate over the beans and persist them in cache
    	foreach ($this->_beans as $bean) {
    		$bean->disconnect();
    	}
    }

    /**
     * Sets the cache instance to use.
     *
     * @param TechDivision_Model_Interfaces_Container_Cache $cache
     * @return TechDivision_Model_Container_Entity
     * 		The instance itself
     */
    public function setCache(
    	TechDivision_Model_Interfaces_Container_Cache $cache)
    {
    	$this->_cache = $cache;
    	return $this;
    }

    /**
     * Returns the cache instance to use.
     *
     * @return TechDivision_Model_Interfaces_Container_Cache
     * 		The cache instance
     */
    public function getCache()
    {
    	return $this->_cache;
    }

    /**
     * Returns the unique cache key for the bean.
     *
     * @param string $key The key to create a cache key for
     */
    public function getCacheKey($key)
    {
		return self::CACHE_KEY_PREFIX .
			strtolower($this->getName()) . '_' . $key;
    }

    /**
     * This method adds a bean to the internal array.
     *
     * @param TechDivision_Model_Interfaces_Bean $entity
     * 		The bean that should be registered in the container
     */
    public function add(TechDivision_Model_Interfaces_Bean $bean)
    {
    	$cacheKey = $this->getCacheKey($bean->getPrimaryKey());
    	$this->_beans[$cacheKey] = $bean;
    }

    /**
     * Removes the passed bean from the container.
     *
     * @param TechDivision_Model_Interfaces_Bean $bean
     * 		The entity that should be remove from the container
     */
    public function remove(TechDivision_Model_Interfaces_Bean $bean)
    {
    	$cacheKey = $this->getCacheKey($bean->getPrimaryKey());
    	unset($this->_beans[$cacheKey]);
    }

    /**
     * This method looks in the internal hash map
     * for the entity with the passed key. If it
     * is found the entity is returned.
     *
     * @param integer $key Holds the key of the bean that should be returned
     * @return TechDivision_Model_Interfaces_Bean
     * 		Returns the bean with the passed key or null
     */
    public function lookup($key)
    {
		// create the unique cache key of the entity
    	$cacheKey = $this->getCacheKey($key);
    	// check if a entity with the passed primary key is available
        if (array_key_exists($cacheKey, $this->_beans)) {
        	return $this->_beans[$cacheKey];
        } else { // if not return null
        	return null;
        }
    }

    /**
     * This method sets the classname of the entity.
     *
     * @param string $string Holds the classname of the entity
     */
    public function setName($string)
    {
        $this->_name = $string;
    }

    /**
     * This method returns the classname of the entity.
     *
     * @return string Holds the classname of the entity
     */
    public function getName()
    {
        return $this->_name;
    }
}