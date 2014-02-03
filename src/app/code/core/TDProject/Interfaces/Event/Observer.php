<?php

/**
 * TDProject_Interfaces_Event_Observer
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
interface TDProject_Interfaces_Event_Observer {

	/**
	 * Returns the callback information.
	 *
	 * @return TDProject_Interfaces_Event_Observer_Callback
	 * 		The callback information
	 */
    public function getCallback();

    /**
     * Returns the event name.
     *
     * @return string The event name to register the observer for
     */
    public function getEventName();

	/**
	 * The application instance.
	 *
	 * @return TDProject_Application The application instance
	 */
    public function getApp();

    /**
     * Returns the object factory used to create
     * a new callback instance.
     *
     * @return TDProject_Factory_Object The object factory instance
     */
    public function getObjectFactory();

    /**
     * Dispatches the observer instance.
     *
     * @return void
     */
    public function dispatch();
}