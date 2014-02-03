<?php

/**
 * TDProject_Interfaces_Event_Observer_Action
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
interface TDProject_Interfaces_Event_Observer_Action {

	/**
	 * Returns the action instance.
	 *
	 * @return TechDivision_Controller_Interfaces_Action
	 * 		The action instance
	 */
    public function getAction();
}