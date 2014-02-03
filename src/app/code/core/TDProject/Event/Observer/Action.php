<?php

/**
 * TDProject_Event_Observer_Action
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
class TDProject_Event_Observer_Action
	extends TDProject_Event_Observer
	implements TDProject_Interfaces_Event_Observer_Action
{

	/**
	 * The action instance.
	 * @var TechDivision_Controller_Interfaces_Action
	 */
	protected $_action = null;

	/**
	 * Sets the action instance.
	 *
	 * @param TechDivision_Controller_Interfaces_Action $action
	 * @return TDProject_Interfaces_Event_Observer_Action The instance itself
	 */
	public function setAction(
		TechDivision_Controller_Interfaces_Action $action)
	{
		$this->_action = $action;
		return $this;
	}

    /**
     * (non-PHPdoc)
     * @see TDProject_Interfaces_Event_Observer_Action::getAction()
     */
    public function getAction()
    {
		return $this->_action;
    }
}