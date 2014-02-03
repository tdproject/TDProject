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

require_once 'TechDivision/Controller/Action/Controller.php';

/**
 * @category    TDProject
 * @package     TDProject
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Application_Controller
	extends TechDivision_Controller_Action_Controller {

	/**
	 * The Application instance.
	 * @var TDProject_Application
	 */
	protected $_app;

    /**
     * The constructor instanciates the internal member variables
     * and sets the config file and parses its values.
     *
     * @param TDProject_Application
     * 		The Application instance
     * @param TechDivision_Util_SystemLocale $locale
	 * 		The local for the application
     * @param TechDivision_Lang_String $logConfigFile
     * 		Holds the configuration file for the logger
     */
    public function __construct(
        TDProject_Application $app,
        TechDivision_Util_SystemLocale $locale,
        TechDivision_Lang_String $logConfigFile) {
        $this->_app = $app;
		parent::__construct($locale, $logConfigFile);
    }

	/**
	 * The destructor destroys the internal members
	 * and frees the memory.
	 *
	 * @return void
	 */
	public function __destruct()
	{
	    if ($this->_plugins != null) {
    		parent::__destruct();
	    }
	}

    /**
     * Returns the Application instance.
     *
     * @return TDProject_Application
     * 		The Application instance
     */
    public function getApplication()
    {
        return $this->_app;
    }

    /**
     * This method creates a new instance of the Action,
     * based on the information given by the ActionMapping
     * that is passed as a parameter, and returns it.
	 *
     * @throws TechDivision_Controller_Exceptions_EmptyActionClassTypeException
     * 		Is thrown if an invalid class type is not configured
     */
    protected function _processActionCreate() {
		// log the entry of the method
		$this->_getLogger()->debug(
			"Now in _processActionCreate method",
		    __LINE__,
		    __METHOD__
		);
		// load the ActionMapping
		$actionMapping = $this->_getActionMapping();
        // name of the ActionClass that should be instanciated
        $actionClass = $actionMapping->getType();
        // if a name was found, then try to instanciate the object
        if (empty($actionClass)) {
            // else throw an exception
            throw new TechDivision_Controller_Exceptions_EmptyActionClassTypeException(
            	'Class type of action not specified in configuration file for mapping with path ' . $actionMapping->getPath()
            );
        }
        // instanciate and return a new object of Action class
        $this->_setAction(
            $this->getApplication()->newInstance(
                $actionClass, array($this->getContext())
            )
        );
    }

    /**
     * This method creates a new instance of the ActionForm, based
     * on the information given by the ActionMapping that is passed
     * as a parameter, and sets it in the Controller.
     *
     * @return boolean TRUE if a ActionForm was registered for the actual
     * 		ActionMapping else FALSE
     * @throws TechDivision_Controller_Exceptions_InvalidActionFormNameException
     * 		Is thrown if an invalid ActionForm name was specified in the configuration file
     * @throws TechDivision_Controller_Exceptions_InvalidActionFormTypeException
     * 		Is thrown if an invalid ActionForm type was specified in the configuration file
     */
    protected function _processActionForm() {
		// log the entry of the method
		$this->_getLogger()->debug("Now in _processActionForm method", __LINE__, __METHOD__);
		// load the actual ActionMapping
		$actionMapping = $this->_getActionMapping();
        // alias of the ActionForm that should be instanciated
        $formBeanName = $actionMapping->getName();
        // if no name was found, then return false
        if (empty($formBeanName)) {
            // return FALSE if no ActionForm was registered
            return false;
        }
        // check if the ActionForm, which is requested, exists in the internal array
        if (($formBean = $this->_actionFormBeans->find($formBeanName)) === null) {
            // throw an Exception if no valid class can be found
			throw new TechDivision_Controller_Exceptions_InvalidActionFormNameException(
				'An ActionForm with name ' . $formBeanName . ' can not be found ' .
				'in the internal ActionFormBeans container'
			);
        }
        // load the request
        $request = $this->_getRequest();
        // check if the form has session scope
        $isSessionScope = strcmp(
            $actionMapping->getScope(),
            TechDivision_Controller_Action_Mapping::SESSION_SCOPE
        );
        // if yes then load it from the session
        if ($isSessionScope === 0) {
            $session = $request->getSession();
            if(($actionForm = $session->getAttribute($actionMapping->getName())) !== null) {
                // set the ActionForm in the Controller
                $this->_setActionForm($actionForm);
                // return TRUE if an ActionForm was found
                return true;
            }
        }
        // class name of the ActionForm that should be instanciated
        $formBeanClassName = $formBean->getType();
        // if no valid class name is found, then die
        if (empty($formBeanClassName)) {
			throw new TechDivision_Controller_Exceptions_InvalidActionFormTypeException(
				'Empty type for the ActionForm was specified in the config file'
			);
        }
        // instanciate a new object of the ActionForm
		$actionForm = $this->getApplication()
		    ->newInstance($formBeanClassName, array($this->getContext()));
        // reset the members of the ActionForm
        $actionForm->reset();
        // check if the ActionForm has to be registered in request or in session
        if ($isSessionScope === 0) {
        	$session->setAttribute($actionMapping->getName(), $actionForm);
            $actionForm = $session->getAttribute($actionMapping->getName());
        } else {
        	$request->setAttribute($actionMapping->getName(), $actionForm);
            $actionForm = $request->getAttribute($actionMapping->getName());
        }
        // set the ActionForm in the Controller
        $this->_setActionForm($actionForm);
        // return TRUE if an ActionForm was found and registered in the defined scope
        return true;
    }
}