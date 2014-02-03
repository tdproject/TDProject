<?php

/**
 * TDProject_Event_Observer
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
class TDProject_Event_Observer
	extends TechDivision_Lang_Object
	implements TDProject_Interfaces_Event_Observer
{

	/**
	 * The event name the observer is bound to.
	 * @var string
	 */
	protected $_eventName = '';

	/**
	 * The callback information.
	 * @var TDProject_Interfaces_Event_Observer_Callback
	 */
	protected $_callback = null;

	/**
	 * The application instance.
	 * @var TDProject_Application
	 */
	protected $_app = null;

	/**
	 * Initializes the name of the event the observers is registered for.
	 *
	 * @param string $eventName
	 * 		The event name the observer has to be registered for
	 * @return void
	 */
	public function __construct($eventName)
	{
		$this->_eventName = $eventName;
	}

    /**
     * (non-PHPdoc)
     * @see TDProject_Interfaces_Event_Observer::getEventName()
     */
    public function getEventName()
    {
		return $this->_eventName;
    }

    /**
     * Sets the callback information the observer is bound to.
     *
     * @param TDProject_Interfaces_Event_Observer_Callback $callback
     * 		The callback the observer is bound to
     * @return TDProject_Interfaces_Event_Observer
     * 		The observer instance
     */
    public function setCallback(
    	TDProject_Interfaces_Event_Observer_Callback $callback)
    {
		$this->_callback = $callback;
		return $this;
    }

	/**
	 * (non-PHPdoc)
	 * @see TDProject_Interfaces_Event_Observer::getCallback()
	 */
    public function getCallback()
    {
		return $this->_callback;
    }

    /**
     * Sets the application instance.
     *
     * @param TDProject_Application $app The application instance
     * @return TDProject_Interfaces_Event_Observer
     * 		The observer instance
     */
    public function setApp(TDProject_Application $app)
    {
		$this->_app = $app;
		return $this;
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Interfaces_Event_Observer::getApp()
     */
    public function getApp()
    {
		return $this->_app;
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Interfaces_Event_Observer::getObjectFactory()
     */
    public function getObjectFactory()
    {
    	return $this->getApp()->getObjectFactory();
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Interfaces_Event_Observer::dispatch()
     */
    public function dispatch()
    {
		// load the class/method name
    	$className = $this->getCallback()->getClassName();
    	$methodName = $this->getCallback()->getMethodName();
		// create a new callback instance
    	$callback = $this->getObjectFactory()->newInstance($className);
		// check if requested callback method exists
    	if (method_exists($callback, $methodName) === false) {
    		throw new Exception(
    			"Method $methodName not available in class $className"
    		);
    	}
		// invoke the callback method
    	$callback->$methodName($this);
    }
}