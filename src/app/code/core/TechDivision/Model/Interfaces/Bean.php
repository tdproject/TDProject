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

require_once 'TechDivision/Model/Interfaces/Value.php';

/**
 * Interface of all beans.
 *
 * @package TechDivision_Model
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
interface TechDivision_Model_Interfaces_Bean
	extends TechDivision_Model_Interfaces_Value
{

    /**
     * Returns the cache key 
     *
     * @return TechDivision_Lang_Integer
     * 		Holds the value of the primary key field
     */
    public function getCacheKey();

    /**
     * Returns the cache tags
     *
     * @return array Array with the entities cache tags
     */
    public function getCacheTags();

    /**
     * Returns the value of the primary key field
     *
     * @return TechDivision_Lang_Integer
     * 		Holds the value of the primary key field
     */
    public function getPrimaryKey();

    /**
     * This method returns the container that
     * handles the entity.
     *
     * @return TechDivision_Model_Interfaces_Container
     * 		Returns a reference to the container used to handle the storable
     */
    public function getContainer();

	/**
	 * This method returns the classname of the
	 * actual object.
	 *
	 * @return string Holds the classname of the actual object
	 */
	public function getClass();

	/**
	 * Connects the entity to the passed container instance.
	 *
	 * @param TechDivision_Model_Interfaces_Container $container
	 * 		The container instance to connect the entity to
	 * @return TechDivision_Model_Interfaces_Entity
	 * 		The instance itself
	 */
	public function connect(
		TechDivision_Model_Interfaces_Container $container);

	/**
	 * Destroys the internal references that are not serializable, e. g.
	 * reference to the container instance.
	 *
	 * @return TechDivision_Model_Interfaces_Entity
	 * 		The instance itself
	 */
	public function disconnect();
}