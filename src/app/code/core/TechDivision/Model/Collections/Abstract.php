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

/**
 * Abstract implementation of a Collection for entities that supports caching.
 *
 * @package TechDivision_Model
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
abstract class TechDivision_Model_Collections_Abstract
	extends TechDivision_Collections_AbstractCollection
	implements Serializable, TechDivision_Model_Interfaces_Collections_Cachable
{
	
	/**
	 * The SQL query the Collection is based on.
	 * @var string
	 */
	protected $_query = '';

	/**
     * The cache keys to be serialized when cached.
     * @array
	 */
	protected $_cacheKeys = array();
	
	/**
     * The container instance to attache the collection to.
     * @var TechDivision_Model_Interfaces_Container
	 */
	protected $_container = null;
	
	/**
	 * Attaches the collection to the passed container instance.
	 * 
	 * @param TechDivision_Model_Interfaces_Container $container The container instance
	 * @return void
	 */
	public function __construct(TechDivision_Model_Interfaces_Container $container)
	{
	    $this->setContainer($container);
	}
	
	/**
	 * Returns the Collection's local home.
	 * 
	 * @return TechDivision_Model_Interfaces_LocalHome
	 * 		The entity's local home instance
	 */
	public abstract function getLocalHome();

	/**
	 * The class name of the Collection's items.
	 *
	 * @return string The items class name
	 */
    public function getItemType()
    {
    	return $this->getLocalHome($this->getContainer())->getEntityAlias();
    }

	/**
	 * Set the SQL query the Collection is based on.
	 * 
	 * @return TechDivision_Collections_Interfaces_Collection
	 * 		The instance itself
	 */
	public function setQuery($query)
	{
		$this->_query = $query;
		return $this;
	}

	/**
	 * Returns the SQL query the Collection is based on.
	 * 
	 * @return string The query
	 */
	public function getQuery()
	{
		return $this->_query;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see TechDivision_Model_Interfaces_Collections_Cachable::load()
	 */
	public function load()
	{
		// if cached values are available, return them
		if (sizeof($this->_cacheKeys) > 0) {
			// initialize the items if cached values are available
			foreach ($this->_cacheKeys as $counter => $primaryKey) {
				$this->_items[$counter] = $this->get($counter);
			}
			// return the instance
			return $this;
		}
		// load the local home
		$localHome = $this->getLocalHome($this->getContainer());
		// load the database manager
		$manager = $this->getContainer()->getSlaveManager();
		// load the mapping alias
		$mappingAlias = $localHome->getMappingAlias();
		// load the mappings
		$mappings = $manager->query($this->getQuery(), array(), array(), $mappingAlias);
		// load the entities
		foreach ($mappings as $mapping) {
			$this->add($localHome->findByPrimaryKey($mapping->getPrimaryKey()));
		}
		// return the instance itself
		return $this;		
	}
	
	/**
	 * This method adds the passed object with the passed key
	 * to the ArrayList.
	 *
	 * @param TechDivision_Model_Interfaces_Entity $item
	 * 		The item that should be added to the ArrayList
	 * @return TechDivision_Collections_Interfaces_Collection
	 * 		The instance itself
	 */
	public function add(TechDivision_Model_Interfaces_Entity $item)
	{
		// set the item in the array
		$this->_items[] = $item;
		// set the cache key
		$this->_cacheKeys[] = $item->getCacheKey()->intValue();
		// return the instance
		return $this;
	}
	
	/**
	 * Attaches the collection to the passed container instance.
	 * 
	 * @param TechDivision_Model_Interfaces_Container $container The container instance
	 * @return void
	 */
	public function setContainer(TechDivision_Model_Interfaces_Container $container)
	{
		$this->_container = $container;
		return $this;
	}

	/**
	 * The container to handle the items.
	 *
	 * @return TechDivision_Model_Interfaces_Container
	 * 		The container instance
	 */
	public function getContainer()
	{
		return $this->_container;
	}

	/**
	 * (non-PHPdoc)
	 * @see TechDivision_Collections_AbstractCollection::get()
	 */
	public function get($key)
	{
		// first check if the entity is available in the internal array
		if (parent::exists($key)) {
			return parent::get($key);
		}
		// load the cache key
		$cacheKey = new TechDivision_Lang_Integer((int) $this->_cacheKeys[$key]);
		// return the entity from the Container
		return $this->getContainer()->lookup($this->getItemType(), $cacheKey);
	}

	/**
	 * (non-PHPdoc)
	 * @see Serializable::unserialize()
	 */
	public function unserialize($str)
	{
		// unserialize the query and the cache keys
		$unserialized = unserialize($str);
		// set the values
		$this->_query = $unserialized['query'];
		$this->_cacheKeys = $unserialized['cacheKeys'];
	}

	/**
	 * (non-PHPdoc)
	 * @see Serializable::serialize()
	 */
	public function serialize()
	{
		// serialize the query and the cache keys
		return serialize(
			array(
				'query' => $this->_query, 
				'cacheKeys' => $this->_cacheKeys
			)
		);
	}
}