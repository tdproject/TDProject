<?php

/**
 * TDProject_Common_Util_WebRequestKeys
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * @category   	TDProject
 * @package    	TDProject_Core
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 * 				Open Software License (OSL 3.0)
 * @author      Bastian Stangl <b.stangl@techdivision.com>
 */
class TDProject_Common_Util_WebRequestKeys
{

	/**
	 * Private constructor for marking
	 * the class as utiltiy.
	 *
	 * @return void
	 */
	private final function __construct() { /* Class is a utility class */ }

	/**
	 * The Request parameter key for storing a Collection with DTO's.
	 * @var string
	 */
	const OVERVIEW_DATA = "overviewData";
    
	/**
	 * The Request parameter key for storing a DTO.
	 * @var string
	 */
	const VIEW_DATA = "viewData";
}

