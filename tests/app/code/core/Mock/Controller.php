<?php

/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision_Controller.
 *
 * TechDivision_Controller free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * TechDivision_Controller distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 *
 * @package TechDivision_Controller
 */

require_once 'TechDivision/Lang/Object.php';
require_once 'TechDivision/HttpUtils/Interfaces/Request.php';
require_once 'TechDivision/Resources/Interfaces/ResourceBundle.php';
require_once 'TechDivision/Controller/Interfaces/RequestProcessor.php';

/**
 * This class is the controller component of the framework and is
 * responsible for the workflow.
 *
 * The ActionController works in the following order. First it checks
 * if there is a valid ActionMapping under self::ACTION_PATH
 * specified in Request. If not, then a dummy ActionMapping with
 * a dummy ActionForward is instanciated an the controller returns
 * the self::ACTION_PATH specified in the Request.
 *
 * If yes it checks if an ActionForm is specified in the ActionMapping.
 * If in ActionForm is specified, then the controller creates a new
 * instance of it an registers it in the specified scope. This can
 * be session or request scope.
 *
 * After the ActionForm is instanciated the controller populates the
 * ActionForm with the values it found in the Request.
 *
 * Then the controller invokes the validate() method of the ActionForm.
 * If the method returns an ActionError in the ActionErrors container,
 * then the controller returns the value of the input attribute that is
 * specified in the ActionMapping.
 *
 * If the ActionErrors container returned by the validation() method of
 * the ActionForm holds no ActionError elements then the controller
 * instanciates a new object of the Action defined in the ActionMapping
 * and invokes the perform() method of it.
 *
 * The perform() method returns an ActionForward object. The controller
 * checks if the value specified as the path attribute of the returned
 * ActionForward is the Path of another Action or not. If yes then the
 * the controller recursively calls it's process() method with the Path
 * specified in the ActionForward.
 *
 * If the value specified as the path attribute of the returned
 * ActionForward is not the Path of another Action then the controller
 * returns the value of the path attribute.
 *
 * @category TechDivision
 * @package TechDivision_Controller
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL, version 2.0
 */
class Mock_Controller
	extends TechDivision_Lang_Object
	implements TechDivision_Controller_Interfaces_RequestProcessor {

	protected $_strutsConfig = array();

    /**
     * Variable that holds the default locale.
     * @var TechDivision_Util_SystemLocale
     */
    protected $_locale = "";

	/**
	 * The resource bundle for testing purposes.
	 * @var TechDivision_Resources_Interfaces_ResourceBundle
	 */
	protected $_resourceBundles = null;

	public function __construct(array $strutsConfig = array())
	{
	    $this->_strutsConfig = $strutsConfig;
	}

	/**
     * This method is the main one of the controller and has to
     * be invoked on each request. This method is responsible for
     * the flow of the framework.
     *
     * @param TechDivision_HttpUtils_Interfaces_Request $request
     * 		Holds a reference to the actual Request instance
     * @return string
     * 		Returns the value of the last path parameter of the ActionForward
     * 		specified in the actual ActionMapping
     */
    public function process(
        TechDivision_HttpUtils_Interfaces_Request $request) {
        $path = $request->getParameter('path');
	    if (!array_key_exists($path, $this->_strutsConfig)) {
	        throw new TechDivision_Controller_Exceptions_InvalidActionMappingException(
	        	'No path specified and no ActionMapping with unknown flag available'
	        );
	    }

	    return $this->_strutsConfig[$path];
    }

    /**
	 * This method initializes the ActionController.
	 *
	 * @param TechDivision_Controller_Interfaces_StrutsConfig $configuration
	 * 		Holds the configuration for the ActionController
	 * @return void
	 */
	public function initialize(
	    TechDivision_Controller_Interfaces_StrutsConfig $configuration = null) {
        return;
    }

    /**
     *
     * Enter description here ...
     * @param TechDivision_Resources_Interfaces_ResourceBundle $resourceBundle
     * 		The resource bundle for testing purposes
     */
    public function setResources(
        TechDivision_Resources_Interfaces_ResourceBundle $resourceBundle) {
        $this->_resourceBundle = $resourceBundle;
    }

    /**
     * This method returns the application resource bundle.
     *
     * @param TechDivision_Util_SystemLocale $locale
     * 		The locale to return the resources for
     * @param TechDivision_Lang_String $key
     * 		Holds the key of the resource bundle to use
     * @return TechDivision_Resources_AbstractResourceBundle
     * 		The requested resources
     */
    public final function getResources(TechDivision_Lang_String $key = null)
    {
        return $this->_resourceBundle;
    }

	/**
	 * Returns the Context for the actual
	 * request.
	 *
	 * @return TechDivision_Controller_Interfaces_Context
	 * 		The Context for the actual request
	 */
	public function getContext()
	{
	    return new TechDivision_Controller_Action_Context($this);
	}

    /**
     * This method sets the application locale.
     *
     * @return string Holds the application locale
     */
    public final function setLocale(TechDivision_Util_SystemLocale $locale) {
        $this->_locale = $locale;
    }
}