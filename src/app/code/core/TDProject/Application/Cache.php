<?php

/**
 * TDProject_Application_Controller
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

require_once 'Zend/Cache/Core.php';
require_once 'TDProject/Interfaces/Cache.php';
require_once 'TechDivision/Model/Interfaces/Container/Cache.php';

/**
 * @category    TDProject
 * @package     TDProject
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Application_Cache
    extends Zend_Cache_Core
    implements TDProject_Interfaces_Cache, TechDivision_Model_Interfaces_Container_Cache
{
}