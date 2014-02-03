<?PHP

/**
 * $Header: httputils/httpsessionhandler.php
 * $Revision: 2.1
 * $Date: 2008.11.12
 *
 * ====================================================================
 *
 * License:    GNU General Public License
 *
 * Copyright (c) 2004 struts4php.org.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of struts4php.
 *
 * struts4php is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * struts4php is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 */

require_once "TechDivision/Lang/Object.php";
require_once "TechDivision/Collections/Enum.php";
require_once "TechDivision/HttpUtils/Interfaces/Session.php";

/**
 * This class is a wrapper for the PHP
 * internal array $_SESSION.
 *
 * @package	httputils
 * @author	wagnert <tw@struts4php.org>
 * @version $Revision: 1.1 $ $Date: 2008-11-12 15:54:14 $
 * @copyright struts4php.org
 * @link www.struts4php.org
 */
class Mock_Session
	extends TechDivision_Lang_Object
	implements TechDivision_HttpUtils_Interfaces_Session {

	protected $_sessionId = '';

	protected $_sessionName = '';

	protected $_attributes = array();

    /**
     * The constructor initializes the internal array
     * with the global $_SESSION array.
     *
     * @param aray $attributes
     * 		The attributes to preinitialize the Mock session with
     * @return void
     */
    public function __construct(
        array $attributes = array()) {
		// initialize the session
		$this->_sessionId = 'SESS_' . md5($time = time());
		$this->_sessionName = md5($time);
		$this->_attributes = $attributes;
    }

    /**
     * The destructor unsets the internal members.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->_attributes = null;
    }

    /**
     * @see Session::getAttribute($name)
     */
    function getAttribute($name)
    {
		// get the value
		if (array_key_exists($name, $this->_attributes)) {
			return $this->_attributes[$name];
		}
    }

    /**
     * @see Session::setAttribute($name, $attribute)
     */
    function setAttribute($name, $attribute)
    {
		$this->_attributes[$name] = $attribute;
    }

    /**
     * @see Session::getId()
     */
    function getId()
    {
        return $this->_sessionId;
    }

    /**
     * This method returns the name of the session.
     *
     * @return string Holds the name of the session
     */
    function getName()
    {
        return $this->_sessionName();
    }

    /**
     * @see Session::getAttributeNames()
     */
    function getAttributeNames()
    {
		return new TechDivision_Lang_Enum(array_keys($this->_attributes));
    }

    /**
     * This method returns the number of attributes
     * found in the session.
     *
     * @return integer Holds the number of attributes found in the session
     */
    function count() {
        return sizeof($this->_attributes);
    }

    /**
     * @see Session::invalidate()
     */
    function invalidate()
    {
        $this->_attributes = array();
    }

    /**
     * @see Session::removeAttribute()
     */
    function removeAttribute($name)
    {
        unset($this->_attributes[$name]);
    }

	/**
	 * @see Session::getCreationTime()
	 */
	public function getCreationTime()
	{
		// @todo Still to implement
	}

	/**
	 * @see Session::getLastAccessedTime()
	 */
	public function getLastAccessedTime()
	{
		// @todo Still to implement
	}

	/**
	 * @see Session::setMaxInactiveInterval($interval)
	 */
	public function setMaxInactiveInterval($interval)
	{
		// @todo Still to implement
	}

	/**
	 * @see Session::getMaxInactiveInterval()
	 */
	public function getMaxInactiveInterval()
	{
		// @todo Still to implement
	}

	/**
	 * @see Session::isNew()
	 */
	public function isNew()
	{
		// @todo Still to implement
	}
}