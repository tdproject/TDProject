<?php

/**
 * TDProject_Application
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

require_once 'PEAR/Config.php';
require_once 'Zend/Cache.php';
require_once 'TechDivision/Lang/Boolean.php';
require_once 'TechDivision/Lang/String.php';
require_once 'TechDivision/Logger/Logger.php';
require_once 'TechDivision/Logger/System.php';
require_once 'TechDivision/Logger/Mail.php';
require_once 'TechDivision/Util/SystemLocale.php';
require_once 'TechDivision/HttpUtils/Interfaces/Request.php';;
require_once 'TDProject/Interfaces/Translator.php';
require_once 'TechDivision/Resources/DBResourceBundle.php';
require_once 'TechDivision/AOP/Interfaces/Proxy.php';
require_once 'TDProject/Proxy/Generator.php';
require_once 'TDProject/Aspectable/Pointcut.php';
require_once 'TDProject/Factory/Object.php';
require_once 'TDProject/Application/Controller.php';
require_once 'TDProject/Application/ClassLoader.php';
require_once 'TDProject/Application/Logger.php';

// TODO This HAS to be refactored, because unit tests will NOT work
require_once 'TDProject/Core/Common/ValueObjects/System/UserValue.php';

/**
 * @category    TDProject
 * @package     TDProject_Core
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Application
    extends TechDivision_Lang_Object
    implements TDProject_Interfaces_Translator
{

	/**
	 * Context key for the application.
	 * @var string
	 */
	const CONTEXT = 'tdproject.application';

    /**
     * Request parameter name for the namespace.
     * @var string
     */
    const PACKAGE_NAMESPACE = 'namespace';

    /**
     * Request parameter name for the module name.
     * @var string
     */
    const PACKAGE_MODULE = 'module';

    /**
     * Boolean Request parameter to replace namespace and package in Session.
     * @var string
     */
    const REPLACE_PACKAGE = 'replacePackage';

    /**
     * Request parameter with mode for cleaning Zend_Cache instance.
     * @var string
     */
    const CLEAN_CACHE_MODE = 'cleanCacheMode';

    /**
     * Request parameter with tags for cleaning Zend_Cache instance.
     * @var string
     */
    const CLEAN_CACHE_TAGS = 'cleanCacheTags';
    
    /**
     * The cache key for the ACL.
     * @var string
     */
    const CACHE_KEY_ACL = 'tdproject_cache_key_acl';

    /**
     * The locale to use.
     * @var string
     */
    const LOCALE = 'locale';

    /**
     * The default logger instance.
     * @var TechDivision_Logger_Interfaces_Logger
     */
    protected $_logger = null;

    /**
     * The default locale to use.
     * @var TechDivision_Util_SystemLocale
     */
    protected $_defaultLocale = null;

    /**
     * Default namespace to use.
     * @var string
     */
    protected $_namespace = 'TDProject';

    /**
     * Default module name to use.
     * @var string
     */
    protected $_module = 'Core';

    /**
     * The internal request instance.
     * @var TechDivision_HttpUtils_Interfaces_Request
     */
    protected $_request = null;

    /**
     * The controller instance.
     * @var TechDivision_Controller_Interfaces_RequestProcessor
     */
    protected $_controller = null;

    /**
     * The keys of the DB resource messages.
     * @var array
     */
    protected $_resourceKeys = null;

    /**
     * Array with the defined pointcuts.
     * @var array
     */
    protected $_pointcuts = array();

    /**
     * The object factory instance.
     * @var TDProject_Factory_Object
     */
    protected $_objectFactory = null;

    /**
     * The ClassLoader instance.
     * @var TechDivision_VFS_ClassLoader
     */
    protected $_classLoader = null;

    /**
     * The cache instance.
     * @var Zend_Cache_Core
     */
    protected $_cache = null;

    /**
     * The Zend_Cache Backend to use.
     * @var string
     */
    protected $_cacheBackend = 'apc';

    /**
     * The configuration for the controller instance.
     * @var TechDivision_Controller_Interfaces_StrutsConfig
     */
    protected $_configuration = null;

    /**
     * Array with observers to dispatch events.
     * @var array
     */
    protected $_observers = array();

    /**
     * The system configuration.
     * @var PEAR_Config
     */
    protected $_systemConfig = null;

    /**
     * The container handling entity/session beans.
     * @var TechDivision_Model_Interfaces_Container
     */
    protected $_container = null;

    /**
     * The system ACL's.
     * @var Zend_Acl
     */
    protected $_acl = null;

    /**
     * Initializes the internal logger instance.
     *
     * @return void
     */
    public function __construct()
    {
        // initialize the PEAR_Config instance
        $this->setSystemConfig(
        	PEAR_Config::singleton('cfg/.pearrc')
        );
        // load configuration variables
        $cacheBackend = $this->getSystemConfig()->get('tdproject_cache_backend');
        $cacheTTL = $this->getSystemConfig()->get('tdproject_cache_ttl');
        $logProperties = $this->getSystemConfig()->get('tdproject_log_properties');
		// intialize the applications default system locale
		$this->_defaultLocale =
		    TechDivision_Util_SystemLocale::create(
		        TechDivision_Util_SystemLocale::US
		    );
		// set the default locale in the system
		TechDivision_Util_SystemLocale::setDefault(
		    $this->getDefaultLocale()
		);
		// set the frontend options
		$frontendOptions = array(
		    'lifetime' => 0,
			'automatic_serialization' => true,
			'automatic_cleaning_factor' => 0
		);
		// set the cache directory
		$backendOptions = array(
			'cache_dir' => $this->getSystemConfig()->get('cache_dir')
		);
		// initialize the cache instance
		$this->_cache = Zend_Cache::factory(
			'TDProject_Application_Cache',
			'File',
		    $frontendOptions,
		    $backendOptions,
		    true
		);
        // initialize a new VFS class loader instance
        $this->_classLoader = TDProject_Application_ClassLoader::register()->setApp($this);
        // initialize the object factory
        $this->_objectFactory = TDProject_Factory_Object::get();
		// initialize the default logger instance
		$this->setLogger(
			TDProject_Application_Logger::forClass(__CLASS__, $logProperties)
		);
    }

    /**
     * Returns the cache instance.
     *
     * @return Zend_Cache_Core The cache instance
     */
    public function getCache()
    {
        return $this->_cache;
    }

    /**
     * Returns the application's resource bundle of the passed package.
     *
     * @param string $package The package to return the resource bundle for
     */
    public function getResources($package)
    {
        return $this->getController()->getResources($package);
    }

    /**
     * Returns the core service.
     *
     * @return TDProject_Core_Model_Services_DomainProcessor
     * 		The core service
     */
    public function getDelegate()
    {
        return TDProject_Core_Common_Delegates_DomainProcessorDelegateUtil::getDelegate($this);
    }

    /**
     * The cached ACL.
     *
     * @return Zend_Acl The ACL
     */
    public function getAcl()
    {
        // check if the ACL has already been loaded
    	if (($acl = $this->getCache()->load(TDProject_Application::CACHE_KEY_ACL)) === false) {
	    	// load the ACL's from the container
	    	$this->getCache()->save($acl = $this->getDelegate()->getAcl(), TDProject_Application::CACHE_KEY_ACL);
    	}
    	return $acl;
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Interfaces_Translator::translate()
     */
	public function translate(
	    TechDivision_Lang_String $key,
	    TechDivision_Lang_String $module,
        TechDivision_Collections_ArrayList $parameter = null,
	    TechDivision_Lang_String $default = null) {
		// add key, module name and locale to array with key values
		$keyValues = array($key, $module, $this->getLocale());
		// check if parameters has been passed
		if ($parameter != null) {
			// initialize a string for the parameter hash
			$paramHash = new TechDivision_Lang_String();
			// prepare the parameter string
			for ($i = 0; $i < $parameter->size(); $i++) {
				$paramHash->concat(new TechDivision_Lang_String($parameter->get($i)));
			}
			// add the parameter string to the key values
			$keyValues[] = $paramHash;
		}
		// prepare the parameter key
		$resourceKey = 'resource_' . md5(implode('_', $keyValues));
		// check if the translation is already
		if ($value = $this->getCache()->load($resourceKey)) {
			// log that a translation has been found in cache
			$this->getLogger()->debug(
				"Found translation for cache tag $resourceKey",
				__LINE__,
			    __METHOD__
			);
			// return the cached value
			return $value;
		}
        // translate passed key and module
        $translated = $this->getResources($module)->find($key, $parameter);
        // check if a translation for the key in the module's
        // resource bundle is available
        if (!empty($translated)) {
            // if yes, save the translation to cache and return it
            if (!$this->getCache()->save($translated, $resourceKey)) {
	            $this->getLogger()->error(
	            	"Error when saving resource $resourceKey to cache",
	            	__LINE__,
	            	__METHOD__
	            );
            }
            // return the translated value
            return $translated;
        }
		// then load the DBResourceBundle
		$resourceBundle =
		    TechDivision_Resources_DBResourceBundle::getBundle(
    		    new TechDivision_Lang_String(
    		    	'TDProject/WEB-INF/dbresources'
    		    ),
    		    TechDivision_Util_SystemLocale::getDefault()
    		);
        // initialize the DB resource messages
        if ($this->_resourceKeys == null) {
            $this->_resourceKeys = $resourceBundle->getKeys()->toArray();
        }
        // check if the resouce message already exists
        if (in_array($key, $this->_resourceKeys)) {
        	// load the translation
        	$translated = $resourceBundle->find($key, $parameter);
            // save the translation to cache and return it
            if (!$this->getCache()->save($translated, $resourceKey)) {
	            $this->getLogger()->error(
	            	"Error when saving resource $resourceKey to cache",
	            	__LINE__,
	            	__METHOD__
	            );
            }
            // translate passed key and module
            return $translated;
        }
        // else, return the translation key itself
        if ($default == null) {
        	// use the key itself as translation
        	$translated = $key->__toString();
        	// save the translation to cache and return it
        	if (!$this->getCache()->save($translated, $resourceKey)) {
        		$this->getLogger()->error(
        			"Error when saving resource $resourceKey to cache",
        			__LINE__,
        		    __METHOD__
        		);
        	}
            return $translated;
        }
        // else, attach the key itself as value
        $this->attach($key, $module, $default);
        // save the default translation to cache and return it
        if (!$this->getCache()->save($default, $resourceKey)) {
        	$this->getLogger()->error(
        		"Error when saving resource $resourceKey to cache",
        		__LINE__,
        	    __METHOD__
            );
        }
        // else, return the default value
        return $default;
	}

	/**
	 * (non-PHPdoc)
	 * @see TDProject_Interfaces_Translator::attach()
	 */
	public function attach(
	    TechDivision_Lang_String $key,
	    TechDivision_Lang_String $module,
	    TechDivision_Lang_String $value) {
		try {
    		// load the ResourceBundle
    		$resourceBundle =
    		    TechDivision_Resources_DBResourceBundle::getBundle(
        		    new TechDivision_Lang_String(
        		    	'TDProject/WEB-INF/dbresources'
        		    ),
        		    TechDivision_Util_SystemLocale::getDefault()
        		);
            // log the successfull translation
            $this->getLogger()->debug(
            	"Successfully created: $module:$key - $value",
                __LINE__,
                __METHOD__
            );
		    // store the resource key with the original title
		    $resourceBundle->attach($key, $value);
		} catch (Exception $e) {
		    $this->getLogger()->error($e->__toString(), __LINE__, __METHOD__);
		}
	}

	/**
	 * This method returns the logger of the requested
	 * type for logging purposes.
	 *
     * @param string The log type to use
	 * @return TechDivision_Logger_Interfaces_Logger Holds the Logger object
	 * @throws Exception Is thrown if the requested logger type is not initialized or doesn't exist
	 * @deprecated 0.6.24 - 2011/12/16
	 */
	protected function _getLogger(
        $logType = TechDivision_Logger_System::LOG_TYPE_SYSTEM)
    {
		return $this->getLogger($logType);
	}

    /**
     * This method sets the logger used for logging purposes.
     *
     * @param TechDivision_Logger_Interfaces_Logger $logger The Logger instance
     * @return TDProject_Application The instance itself
     * @since 0.6.29 - 2012/01/21
     */
	public function setLogger(TechDivision_Logger_Interfaces_Logger $logger)
	{
		$this->_logger = $logger;
		return $this;
	}

    /**
     * This method returns the logger of the requested
     * type for logging purposes.
     *
     * Passed log type is NOT longer supported.
     *
     * @param string The log type to use
     * @return TechDivision_Logger_Logger Holds the Logger object
     * @since 0.6.25 - 2011/12/16
     */
	public function getLogger(
        $logType = TechDivision_Logger_System::LOG_TYPE_SYSTEM)
	{
		return $this->_logger;
	}

	/**
	 * Returns the applications Base-URL.
	 *
	 * @return void
	 */
	public function getBaseUrl()
	{
		return dirname($this->getRequest()->getScriptName());
	}

	/**
	 * Returns the URL to the directory with the image, prepended
	 * with the design Base-URL.
	 *
	 * @param string $imageName The image name to return the URL for
	 * @return string The URL to the image
	 * @see TDProject_Interfaces_Translator::getBaseUrl()
	 */
    public function getDesignUrl($imageName)
    {
		// check if base URL is empty
    	if (($baseUrl = $this->getBaseUrl()) == '/') {
        	// if yes, return the www folder directly
    		return "/www/design/" . $imageName;
    	}
		// else prepend the base URL
    	return $baseUrl . "/www/design/" . $imageName;
    }

	/**
	 * Returns the URL appended with the passed
	 * parameter.
	 *
	 * @param array $params The URL parameter to append
	 * @return string The requested URL
	 */
	public function getUrl(array $params = array())
	{
		// trigger XDebug's debugger
		if ($this->useDebugger()->booleanValue()) {
			$params['XDEBUG_SESSION_START'] = true;
		}
		// trigger XDebug's profiler
		if ($this->useProfiler()->booleanValue()) {
			$params['XDEBUG_PROFILE'] = true;
		}
	    // convert the array with the params
	    $values = $this->processUrlParams($params);
	    // add the parameter and return the URL
	    return '?' . implode('&', $values);
	}

	/**
	 * Returns TRUE if the debugger has to be used, else FALSE.
	 *
	 * @return TechDivision_Lang_Boolean
	 * 		TRUE if the debugger has to be use, else FALSE
	 */
	public function useDebugger()
	{
		return new TechDivision_Lang_Boolean(
			$this->getSystemConfig()->get('tdproject_dev_debugger')
		);
	}

	/**
	 * Returns TRUE if the profiler has to be used, else FALSE.
	 *
	 * @return TechDivision_Lang_Boolean
	 * 		TRUE if the profiler has to be use, else FALSE
	 */
	public function useProfiler()
	{
		return new TechDivision_Lang_Boolean(
			$this->getSystemConfig()->get('tdproject_dev_profiler')
		);
	}

	/**
	 * Converts the array with the passed params
	 * into an array with valid URL params.
	 *
	 * @param array $params The params to convert
	 * @return The converted params
	 */
	public function processUrlParams($params = array())
	{
	    // initialize the array for the parameter
	    $elements = array();
	    // iterate over the params and add the elements to the array
	    foreach ($params as $key => $value) {
	        if (is_array($value)) {
	            foreach($value as $k => $v) {
	                if (is_array($v)) {
	                    $elements[] = $this->processUrlParams($v);
	                } else {
	                    $elements[] = $key . '[' . $k . ']=' . $v;
	                }
	            }
	        } else {
	            $elements[] = $key . '=' . $value;
	        }
	    }
	    // return the array with the URL elements
	    return $elements;
	}

    /**
     * This method initializes the controller of the namespace
     * and model specified as request parameters.
	 *
     * @return TDProject_Application The instance itself
     */
    public function process()
    {
		// log memory usage
    	$this->logMemoryUsage(__METHOD__, __LINE__);
    	$startTime = $this->timeUsage();
        // initialize and process the application
        $result = $this
        	->processCache()
        	->processLocale()
        	->processPointcuts()
        	->processConfiguration()
        	->processObservers()
        	->processController();
		// log memory usage
    	$this->logMemoryUsage(__METHOD__, __LINE__);
    	$this->logTimeUsage(__METHOD__, __LINE__, $startTime);
    	// return the instance itself
    	return $this;
    }

    /**
     * Prepares the Zend_Cache instance.
     *
     * @return TDProject_Application The instance itself
     */
    public function processCache()
    {
    	// load the Request instance
    	$request = $this->getRequest();
		// load the cache clean mode passed as Request parameter
    	$cleanMode = $request
    		->getParameter(self::CLEAN_CACHE_MODE, FILTER_SANITIZE_STRING);
    	// if not found, try to load it as Request attribute
    	if ($cleanMode == null) {
	    	$cleanMode = $request->getAttribute(self::CLEAN_CACHE_MODE);
    	}
		// if a mode has been passed
		if ($cleanMode != null) {
			// load the found mode
			$this->getLogger()
				->debug("Found cache clean mode $cleanMode", __LINE__, __METHOD__);
			// load the cache tags to be cleaned
			$cleanTags = $this->getRequest()
				->getParameterValues(self::CLEAN_CACHE_TAGS);
			// check if tags has been found
			if (is_array($cleanTags) === false) {
				$cleanTags = array();
			} else {
				// if tags has been found, concatenate the tags
				$tags = implode(', ', $cleanTags);
				// log the found tags
				$this->getLogger()
					->debug("Found cache tags to clean: $tags", __LINE__, __METHOD__);
			}
			// clean the cache with the found values
			$this->cleanCache($cleanMode, $cleanTags);
		}
		// return the instance itself
		return $this;
    }

    /**
     * Loads the locale to use from the Request if available, or the Session.
     *
	 * @return TDProject_Application The instance itself
     */
    public function processLocale()
    {
        // load the Request instance
        $request = $this->getRequest();
        // try to load the locale as request parameter
        $locale = $request->getParameter(self::LOCALE, FILTER_SANITIZE_STRING);
        // check if a locale was given
        if ($locale == null) {
            // if not try to load it from the session
            $locale = $request->getSession()->getAttribute(self::LOCALE);
        }
        // if a supported locale was found
        if ($locale != null) {
            // initialize the locale
            $systemLocale = TechDivision_Util_SystemLocale::create($locale);
            // check if the passed locale ist supported
            if ($this->isSupportedLocale($systemLocale)) {
                // store the locale in the session
                $request->getSession()->setAttribute(self::LOCALE, $locale);
                // and set it as the default locale
                TechDivision_Util_SystemLocale::setDefault($systemLocale);
            }
        }
        // returns the instance itself
        return $this;
    }

    /**
     * Initializes the pointcuts found in the projects
     * configuration files.
     *
	 * @return TDProject_Application The instance itself
     */
    public function processPointcuts()
    {
    	// initialize the array for the pointcut configuration
    	$pointcuts = array();
	    // check if configuration has already been cached
	    if (($pointcuts = $this->getCache()->load('pointcuts'))) {
	        // log that pointcut configuration was found cached
	        $this->getLogger()->debug('Found cached pointcut configuration', __LINE__, __METHOD__);
	        // set the cached pointcut configuration and return the instance itself
	        return $this->setPointcuts($pointcuts);
	    }
        // log that no cached configuration was found
        $this->getLogger()->debug('Load poincut configuration', __LINE__, __METHOD__);
        // create the directory iterator
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(getcwd())
        );
        // iterate over the directory recursively and look for configurations
        while ($it->valid()) {
            if (!$it->isDot()) {
                // if a configuration file was found
                if (basename($it->getSubPathName()) == 'pointcuts.xml') {
                    // initialize the SimpleXMLElement with the content of pointcut XML file
                    $sxe = new SimpleXMLElement(
                        file_get_contents($it->getSubPathName(), true)
                    );
                    // iterate over the found nodes
                    foreach ($sxe->xpath('/pointcuts/pointcut') as $child) {
                        // initialize the pointcut
                        $pointcut = TDProject_Aspectable_Pointcut::create()
                            ->setClassName((string) $child->className)
                            ->setIncludeFile((string) $child->includeFile)
                            ->setInterceptWithMethod((string) $child->interceptWithMethod)
                            ->setMethodToIntercept((string) $child->methodToIntercept)
                            ->setAdvice((string) $child->advice);
                        // add it the array
                        $pointcuts[] = $pointcut;
                    }
                }
            }
            // proceed with the next folder
            $it->next();
        }
        // cache the pointcut configuration
        $this->getCache()->save($pointcuts, 'pointcuts');
		// set the poincut configuration and return the instance itself
        return $this->setPointcuts($pointcuts);
    }

	/**
	 * This method iterates over the configuration files
	 * of all extensions and should merge them to one.
	 *
	 * This method is actually NOT in use, because
	 * functionality is not yet implemented.
     *
	 * @return TDProject_Application The instance itself
	 */
	public function processConfiguration()
	{
	    // check if configuration has already been cached
	    if (($configuration = $this->getCache()->load('configuration'))) {
	        // log that configuration was found cached
	        $this->getLogger()->debug('Found cached configuration', __LINE__, __METHOD__);
	        // set the configuration and return the instance itself
	        return $this->setConfiguration($configuration);
	    }
	    // log that no cached configuration was found
	    $this->getLogger()->error('Load configuration', __LINE__, __METHOD__);
        // load the basic configuration
		$configuration =
		    TechDivision_Controller_XML_SAXParserStruts::getConfiguration(
            	new TechDivision_Lang_String(
            	    'TDProject/WEB-INF/struts-config.xml'
            	)
	        );
        // intialize the basic configuration
	    $configuration->initialize();
        // create the directory iterator
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(getcwd())
        );
        // iterate over the directory recursively and look for configurations
        while ($it->valid()) {
            if (!$it->isDot()) {
                // if a configuration file was found
                if (basename($it->getSubPathName()) == 'struts-config.xml' &&
                	strrchr($it->getSubPath(), 'WEB-INF') !== false) {
                    // don't reimport import basic configuration
                    if ($it->getSubPathName() == 'TDProject/WEB-INF/struts-config.xml') {
                        $it->next();
                        continue;
                    }
                    // load ...
                    $toMerge =
            		    TechDivision_Controller_XML_SAXParserStruts::getConfiguration(
                        	new TechDivision_Lang_String($it->getSubPathName())
            	        );
            	    // initialize ..
            	    $toMerge->initialize();
                    // and merge it to the basic configuration
            	    $configuration
            	        ->merge($toMerge);
                }
            }
            // proceed with the next folder
            $it->next();
        }
        // cache the configuration
        $this->getCache()->save($configuration, 'configuration');
	    // set the configuration and return the instance itself
        return $this->setConfiguration($configuration);
	}

	/**
	 * This method iterates over the event configuration files
	 * of all extensions and initializes the observers.
     *
	 * @return TDProject_Application The instance itself
	 */
	public function processObservers()
	{
		// check if configuration has already been cached
		if (($observers = $this->getCache()->load('observers'))) {
			// log that observer configuration has been found cached
			$this->getLogger()->debug('Found cached observer configuration', __LINE__, __METHOD__);
			// set the cached event configuration and return the instance itself
			return $this->setObservers($observers);
		}
		// initialize the array for the event configuration
		$observers = array();
		// log that no cached observer configuration has been found
		$this->getLogger()->error('Load observer configuration', __LINE__, __METHOD__);
		// create the directory iterator
		$it = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(getcwd())
		);
		// iterate over the directory recursively and look for configurations
		while ($it->valid()) {
			if (!$it->isDot()) {
				// if a configuration file was found
				if (basename($it->getSubPathName()) == 'observers.xml' &&
                	strrchr($it->getSubPath(), 'WEB-INF') !== false) {
					// initialize the SimpleXMLElement with the content of observer XML file
					$sxe = new SimpleXMLElement(
		            	file_get_contents($it->getSubPathName(), true)
					);
					// iterate over the found nodes
					foreach ($sxe->xpath('/observers/observer') as $child) {
						// initialize the event
						$observer = $this->getObjectFactory()
							->newInstance(
								(string) $child->observerType,
								array((string) $child->eventName)
							);
						// initialize the callback
						$callback = new TDProject_Event_Observer_Callback();
						// set the callback class name
						$callback->setClassName(
							(string) $child->callbackClassName
						);
						// set the callback method name
						$callback->setMethodName(
							(string) $child->callbackMethodName
						);
						// add the callback to the observer
						$observer->setCallback($callback);
						// add the observer to the array
						$observers[$observer->getEventName()][] = $observer;
					}
				}
			}
			// proceed with the next folder
			$it->next();
		}
		// cache the observer configuration
		$this->getCache()->save($observers, 'observers');
		// set the observer configuration and return the instance itself
		return $this->setObservers($observers);
	}

	/**
	 * Process the MVC controller instance.
     *
	 * @return TDProject_Application The instance itself
	 */
	public function processController()
	{
		// check if the controller has already been initialized
		if (($controller = $this->getController()) == null) {
			// if not, initialize the controller instance
			$controller = new TDProject_Application_Controller(
				$this,
				TechDivision_Util_SystemLocale::getDefault(),
				new TechDivision_Lang_String(
					'TDProject/WEB-INF/log4php.properties'
				)
			);
	        // load the system configuration and initialize the controller instance
	        $controller->initialize($this->getConfiguration());
	        // set the locale
	        $controller->setLocale(
	        	TechDivision_Util_SystemLocale::getDefault()
	        );
		    // add the Application itself to the Context
		    $controller->getContext()->setAttribute(
		    	TDProject_Application::CONTEXT, $this
		    );
		    // set the internal controller instance
		    $this->setController($controller);
		}
	    // process the request and render the view
	    $path = $controller->process($this->getRequest());
	    // load the block instance from the Request
	    $block = $this->getRequest()->getAttribute($path);
	    // check if a object/string is set
	    if (is_object($block)) {
	    	$block->prepareLayout();
	    	$block->trsl();
	    	$block->toHtml();
	    } elseif(is_string($path)) {
	    	require_once $path;
	    } else {
	    	// do nothing here
	    }
		// the instance itself
        return $this;
	}

    /**
     * Sets the internal request instance.
     *
     * @param TechDivision_HttpUtils_Interfaces_Request $request
     *		The internal request instance
     * @return TDProject_Application The instance itself
     */
    public function setRequest(
        TechDivision_HttpUtils_Interfaces_Request $request) {
        $this->_request = $request;
        return $this;
    }

    /**
     * Return the internal request instance.
     *
     * @return TechDivision_HttpUtils_Interfaces_Request
     *		The internal request instance
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Returns the session instance.
     * 
     * @return TechDivision_HttpUtils_Interfaces_Session
     *     The internal session instance
     */
    public function getSession()
    {
    	return $this->getRequest()->getSession();
    }

    /**
     * Returns the actual session ID.
     * 
     * @return string The session ID
     */
    public function getSessionId()
    {
    	return $this->getSession()->getId();
    }

    /**
     * Sets the initialized controller configuration.
     *
     * @param TechDivision_Controller_Interfaces_StrutsConfig $configuration
     * 		The controller configuration
     * @return TDProject_Application The instance itself
     */
    public function setConfiguration(
    	TechDivision_Controller_Interfaces_StrutsConfig $configuration)
    {
    	$this->_configuration = $configuration;
    	return $this;
    }

    /**
     * Returns the controller configuration.
     *
     * @return TechDivision_Controller_Interfaces_StrutsConfig
     * 		The configuration
     */
    public function getConfiguration()
    {
    	return $this->_configuration;
    }

    /**
     * Sets the internal controller instance.
     *
     * @param TechDivision_Controller_Interfaces_RequestProcessor $controller
     * 		The controller instance to set
     */
    public function setController(
    	TechDivision_Controller_Interfaces_RequestProcessor $controller)
    {
		$this->_controller = $controller;
    }

	/**
	 * Returns the actual Controller instance.
	 *
	 * @return TechDivision_Controller_Interfacs_RequestProcessor
	 * 		The actual Controller instance
	 */
	public function getController()
	{
	    return $this->_controller;
	}

	/**
	 * Returns the actual system locale to use.
	 *
	 * @return TechDivision_Util_SystemLocale
	 * 		The system locale to use
	 */
	public function getLocale()
	{
	    return TechDivision_Util_SystemLocale::getDefault();
	}

	/**
	 * Returns the default system locale.
	 *
	 * @return TechDivision_Util_SystemLocale
	 * 		The default system locale
	 */
	public function getDefaultLocale()
	{
	    return $this->_defaultLocale;
	}

	/**
	 * Returns the actual namespace the application is in, e. g. if
	 * functionality of TDProject_Core is requested, namespace is
	 * 'TDProject'.
	 *
	 * @return TechDivision_Lang_String The requested namespace
	 */
	public function getNamespace()
	{
	    return new TechDivision_Lang_String($this->_namespace);
	}

	/**
	 * Returns the actual module the application is in, e. g. if
	 * functionality of TDProject_Core is requested, module is
	 * 'Core'.
	 *
	 * @return TechDivision_Lang_String The requested module
	 */
	public function getModule()
	{
	    return new TechDivision_Lang_String($this->_module);
	}

	/**
	 * Checks if the passed locale is supported by the system.
	 *
	 * @param TechDivision_Util_SystemLocale $systemLocale
	 * 		The locale to be checked
	 * @return boolean TRUE if the passed locale is supported, else FALSE
	 */
	public function isSupportedLocale(
	    TechDivision_Util_SystemLocale $systemLocale) {
	    // iterate over the system locales
	    foreach ($this->getSupportedLocales() as $supportedLocale) {
	        if ($supportedLocale->equals($systemLocale)) {
	            // return TRUE if locale IS supported
	            return true;
	        }
	    }
	    // return FALSE if locale is NOT supported
	    return false;
	}

	/**
	 * Returns an array with the locales supported by
	 * the Application.
	 *
	 * @return TechDivision_Collections_ArrayList
	 * 		An ArrayList with supported locales
	 */
	public function getSupportedLocales()
	{
	    return TechDivision_Util_SystemLocale::getAvailableLocales();
	}

    /**
     * Attaches the defined Pointcuts to the
     * passed Proxy.
     *
     * @param TechDivision_AOP_Interfaces_Proxy $proxy
     * 		The Proxy to initialize
     * @return TechDivision_AOP_Interfaces_Proxy
     * 		The initialized Proxy
     */
    public function attachPointcuts(
        TechDivision_AOP_Interfaces_Proxy $proxy) {
        // add the Proxy's Pointcuts
        foreach ($this->getPointcuts() as $pointcut) {
            // add the pointcut to the Proxy
            $proxy->addPointcut($pointcut->getInstance($this));
        }
        // return the initialized Proxy
        return $proxy;
    }

    /**
     * Sets the pointcuts.
     *
     * @param array $pointcuts The pointcuts
     * @return TDProject_Application The instance itself
     */
    public function setPointcuts(array $pointcuts)
    {
		$this->_pointcuts = $pointcuts;
		return $this;
    }

    /**
     * Returns the pointcuts.
     *
     * @return array The pointcuts
     */
    public function getPointcuts()
    {
        return $this->_pointcuts;
    }

    /**
     * Sets the observers.
     *
     * @param array $observers The observers
     * @return TDProject_Application The instance itself
     */
    public function setObservers(array $observers)
    {
		$this->_observers = $observers;
		return $this;
    }

    /**
     * Returns the observers.
     *
     * @return array The observers
     */
    public function getObservers()
    {
        return $this->_observers;
    }

    /**
     * Factory method for a new instance of the
     * class with the passed name.
     *
     * @param string Name of the class to create and return the oject for
     * @param array The arguments passed to the classes constructor
     * @return TechDivision_AOP_Interfaces_AspectContainer
     * 		The AspectContainer instance
     */
    public function newInstance($className, array $arguments = array())
    {
    	// log that instance using AOP will be created
    	$this->getLogger()->debug(
        	"Create instance of $className using AOP", __LINE__, __METHOD__
    	);
    	// create the Proxy
    	$proxy = $this->getObjectFactory()
    		->newInstance('TDProject_Proxy_Generator')
    		->setBaseDirectory($this->getSystemConfig()->get('cache_dir'))
    		->newProxy($className, $arguments)
        	->setProxyCache($this->getCache());
        // attach pointcuts and return the AspectContainer
        return $this->attachPointcuts($proxy);
    }

	/**
	 * Returns the object factory.
	 *
	 * @return TDProject_Factory_Object The object factory
	 */
	public function getObjectFactory()
	{
	    return $this->_objectFactory;
	}

	/**
	 * Event dispatcher that delegates the finally observer to be
	 * dispatch back to the observable (caller) instance.
	 *
	 * @param TechDivision_Lang_Object $observable The observable instance
	 * @param string $eventName Name of the event to dispatch
	 * @return TDProject_Application The application instance
	 */
	public function dispatchEvent(
		TDProject_Interfaces_Event_Observable $observable, $eventName)
	{
		// log a message that an event has to be dispatched
		$this->getLogger()->debug(
			'Now dispatching event ' . $eventName, __LINE__, __METHOD__
		);
		// load the observers
		$obs = $this->getObservers();
		// check if an observer is registered for the event name
		if (array_key_exists($eventName, $obs)) {
			// load the observer instances for the passed event name
			$observers = $obs[$eventName];
			// iterate over all observers and dispatch them
			for ($i = 0; $i < sizeof($observers); $i++) {
				// delegate observer initialization to the caller
				$observable->dispatch($observers[$i]->setApp($this));
			}
		}
		// return the instance itself
		return $this;
	}

	/**
	 * Deletes all cache entries from ZEND cache backend and
	 * clears APC cache if PECL extension is activated.
	 *
	 * @param string $cleanMode The Zend_Cache cleaning mode
	 * @param array $cleanTags The tags to clean from the cache
	 * @return boolean TRUE if cache has successfully been cleaned
	 */
	public function cleanCache(
		$cleanMode = Zend_Cache::CLEANING_MODE_ALL,
		array $cleanTags = array())
	{
		// log a message that cache will be cleared now
		$this->getLogger()->debug("Clean cache using mode $cleanMode", __LINE__, __METHOD__);
		// also check if APC is avaliable
		if (function_exists('apc_clear_cache')) {
			apc_clear_cache();
			apc_clear_cache('user');
		} else {
			$this->getLogger()->debug("APC not available", __LINE__, __METHOD__);
		}
		// clear the ZEND cache and return
		return $this->getCache()->clean($cleanMode, $cleanTags);
	}

	/**
	 * Calculate the actual memory usage.
	 *
	 * @return integer The memory usage in KB
	 */
	public function memoryUsage()
	{
		return memory_get_usage(true) / 1024;
	}

	/**
	 * Calculate the actual memory usage.
	 *
	 * @return integer The memory usage in KB
	 */
	public function timeUsage($startTime = 0)
	{
	    // if no start time has been passed, return the acutal time
	    if ($startTime == 0) {
		    return microtime(true);
	    }
        // else, return the result
	    return (microtime(true) - $startTime);
	}

	/**
	 * Logs the memory usage.
	 *
	 * @param string $method The method name to log from
	 * @param integer $line The line to log from
	 * @return void
	 */
	public function logMemoryUsage($method, $line = null)
	{
		$this->getLogger()
			->debug("Memory Usage {$this->memoryUsage()} KB", $line, $method);
	}

	/**
	 * Logs the time usage up from the passed start time.
	 *
	 * @param string $method The method name to log from
	 * @param integer $line The line to log from
	 * @param integer $startTime The calculation base in milliseconds
	 * @return void
	 */
	public function logTimeUsage($method, $line = null, $startTime)
	{
		$this->getLogger()
			->debug("Time Usage {$this->timeUsage($startTime)} s", $line, $method);
	}

	/**
	 * Sets the passed system configuration.
	 *
	 * @param PEAR_Config $systemConfig The system configuration to set
	 * @return TDProject_Application The application instance
	 */
	public function setSystemConfig(PEAR_Config $systemConfig)
	{
		$this->_systemConfig = $systemConfig;
		return $this;
	}

	/**
	 * Returns the system configuration.
	 *
	 * @return PEAR_Config The requested system configuration
	 */
	public function getSystemConfig()
	{
		return $this->_systemConfig;
	}

	/**
	 * Checks if the acutal system user is allowed to use the
	 * passed resource and returns TRUE if yes, else FALSE.
	 *
	 * @param string $resource The resource to check
	 * @param string $privilege The privilege to check
	 * @return boolean
	 * 		TRUE if the users is allowed to use the resource, else FALSE
	 */
	public function isAllowed($resource = null, $privilege = null)
	{
		// load the system user
		$systemUser = $this->getSession()->getAttribute(
			TDProject_Core_Controller_Util_WebSessionKeys::SYSTEM_USER
		);
		// load the session ID
		$sessionId = $this->getSessionId();
		// check if the user is allowed to access the passed resource
		foreach ($systemUser->getUserRoles() as $role) {
			$aclRole = new Zend_Acl_Role($role->getName()->__toString());
			if ($this->getAcl()->isAllowed($aclRole, $resource, $privilege)) {
				// return TRUE if the user has the necessary privileges
				return true;
			}
		}
		// return FALSE if not
		return false;
	}
}