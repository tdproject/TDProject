<?php

/**
 * TDProject_Interfaces_Event_Observer_Callback
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
interface TDProject_Interfaces_Event_Observer_Callback {

	/**
	 * Returns the callback class name.
	 *
	 * @return string The callback class name
	 */
    public function getClassName();

    /**
     * Returns the callback method name.
     *
     * @return string The callback method name
     */
    public function getMethodName();
}