<?php

/**
 * TDProject_Event_Observer_Callback
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * @category    TDProject
 * @package     TDProject
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Event_Observer_Callback
	extends TechDivision_Lang_Object
	implements TDProject_Interfaces_Event_Observer_Callback
{

	/**
	 * The callback class name.
	 * @var string
	 */
	protected $_className = null;

	/**
	 * The callback method name.
	 * @var string
	 */
	protected $_methodName = null;

	/**
	 * Sets the callback class name.
	 *
	 * @param string $className The callback class name
	 * @return TDProject_Interfaces_Event_Observer_Callback The instance itself
	 */
	public function setClassName($className)
	{
		$this->_className = $className;
		return $this;
	}

    /**
     * (non-PHPdoc)
     * @see TDProject_Interfaces_Event_Observer_Callback::getClassName()
     */
    public function getClassName()
    {
		return $this->_className;
    }

	/**
	 * Sets the callback method name.
	 *
	 * @param string $className The callback method name
	 * @return TDProject_Interfaces_Event_Observer_Callback The instance itself
	 */
	public function setMethodName($methodName)
	{
		$this->_methodName = $methodName;
		return $this;
	}

    /**
     * (non-PHPdoc)
     * @see TDProject_Interfaces_Event_Observer_Callback::getMethodName()
     */
    public function getMethodName()
    {
		return $this->_methodName;
    }
}