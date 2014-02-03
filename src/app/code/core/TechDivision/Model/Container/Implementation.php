<?php

/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision GmbH - Connect.
 *
 * TechDivision_Model is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * TechDivision_Model is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 *
 * @package TechDivision_Model
 */

/**
 * This is the container that manages the epbs.
 *
 * The DataSource for the session management MUST have
 * the autocommit flag set to true, else the session
 * management won't work correctly.
 *
 * @package TechDivision_Model
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TechDivision_Model_Container_Implementation
    extends TechDivision_Lang_Object
    implements TechDivision_Model_Interfaces_Container
{

	/**
	 * The prefix for the session cache key.
	 * @var string
	 */
	const CACHE_KEY = 'session_';

	/**
	 * The singleton container instance.
	 * @var TechDivision_Model_Container_Implementation
	 */
	protected static $_instance = null;

    /**
     * Holds a reference to the session database manager needed for
     * storing the session data in the database.
     * @var TechDivision_Model_Interfaces_Manager
     */
    protected $_sessionManager = null;

    /**
     * Holds a reference to the master database manager needed for
     * read/write access to the database.
     * @var TechDivision_Model_Interfaces_Manager
     */
    protected $_masterManager = null;

    /**
     * Holds an array with database managers that only have
     * read access to the database.
     * @var array
     */
    protected $_slaveManagers = array();

    /**
     * Holds an array with the dedicated database managers that only
     * have read access to the database.
     * @var array
     */
    protected $_dedicatedManagers = array();

    /**
     * Holds a hash map needed for the management of the entities.
     * @var TechDivision_Collections_HashMap
     */
    protected $_beans = null;

    /**
	 * Holds an array with plugins executed by the container.
	 * @var array
	 */
    protected $_plugins = array();

    /**
     * Holds the container configuration.
     * @var TechDivision_Model_Configuration_Interfaces_Container
     */
    protected $_containerConfiguration = null;

	/**
	 * Holds the flag to use the master database manager instead of a slave
	 * @var boolean
	 */
	protected $_forceMaster = false;

	/**
	 * Holds the key of the slave to use.
	 * @var integer
	 */
	protected $_activeSlave = 0;

	/**
	 * Holds the actual session id.
	 * @var string
	 */
	protected $_sessionId = null;

    /**
     * The application instance.
     * @var TDProject_Application
     */
    protected $_app = null;

    /**
     * The constructor initializes the configuration, checks
     * if the necessary tables exists and creates it if not.
     *
     * @param TDProject_Application $app The application instance
     * @param string $sessionId Holds the id of the session to use
     * @param TechDivision_Model_Configuration_Interfaces_Container $containerConfiguration
     * 		Holds the container configuration
     * @return void
     */
    public function __construct(
    	TDProject_Application $app = null,
        $sessionId = null,
        TechDivision_Model_Configuration_Interfaces_Container $containerConfiguration = null)
    {
    	// set the Application instance
    	$this->_app = $app;
    	// set the container configuration
    	$this->_containerConfiguration = $containerConfiguration;
    	// initialize the session id
    	if (!empty($sessionId)) {
    		// set the passed session id
			$this->_sessionId = $sessionId;
    		// log that the session id of the web application is used
    		$this->getLogger()->debug(
    			'Use passed session id ' . $this->_sessionId,
    		    __LINE__
    		);
    	} else {
    		// use the session id of the web application
    		$this->_sessionId = session_id();
    		// log a warning that the session id of the web application is used
    		$this->getLogger()->warning(
    			'No session id passed, instead try to use session id ' .
    		    $this->_sessionId .
    		    ' of the web application',
    		    __LINE__
    		);
    	}
		// initialize the container with the data from the
		// container configuration
    	$this->_initialize();
		// initialize the data sources
		$this->_initializeDataSources();
		// initialize the plugins
    	$this->_initializePlugins();
    }

    /**
     * Singleton method to create a new container instance.
     *
     * @param TDProject_Application $app
     * @return TechDivision_Model_Container_Implementation
     * 		The singleton container instance itself
     */
    public static function getContainer(TDProject_Application $app = null)
    {
	    // check if a container instance is available
	    if (self::$_instance == null) {
	    	// if not, intialize the container
	    	self::$_instance = $app->newInstance(
	    		'TechDivision_Model_Container_Implementation',
		    	array(
		    		$app,
		    		$app->getRequest()->getSession()->getId(),
		    		TechDivision_Model_Configuration_XML::getConfiguration(
		    			'TDProject/META-INF/deployment-descriptor.xml'
		    		)
		    	)
	    	);
	    }
	    // return the instance
	    return self::$_instance;
	}

    /**
     * Returns the application instance.
     *
     * @return TDProject_Application The application instance
     */
    public function getApp()
    {
    	return $this->_app;
    }

	/**
	 * Returns the container configuration.
	 *
	 * @return TechDivision_Model_Configuration_Interfaces_Container
	 * 		The container configuration
	 */
	public function getContainerConfiguration()
	{
		return $this->_containerConfiguration;
	}

	/**
	 * Returns the path to the container's configuration directory
	 * folder META-INF.
	 *
	 * @return string The path to the container configuration directory
	 */
	public function getConfigurationDirectory()
	{
		return $this->getContainerConfiguration()->getConfigurationDirectory();
	}

	/**
	 * returns the container cache directory.
	 *
	 * @return string The cache directory
	 */
	public function getCacheDirectory()
	{
		return $this->getSystemConfig()->get('cache_dir');
	}

    /**
     * This method initializes the internal structure
     * with the information found in the deployment
     * descriptor.
     *
     * @return void
     */
    protected function _initialize()
    {
    	// initialize the container configuration
    	$this->_containerConfiguration->initialize($this->getCache());
    	// load the beans and the plugins from the configuration
    	$this->_setBeans($this->_containerConfiguration->getBeans());
    	$this->_setPlugins($this->_containerConfiguration->getPlugins());
    }

    /**
     * This method looks in the internal structure if an entity
     * with the passed name is registered. If yes, it checks if
     * the entity with the passed key is already initialized.
     * If yes it returns the initialized entity, if not, a new
     * entity is initialized, the data is loaded and the new
     * entity is returned.
     *
     * @param string $name The name of the beand that should be returned
     * @param $integer $key The key of the bean that should be initialized
     * @return TechDivision_Model_Interfaces_Bean Holds the initialized bean
     * @throws TechDivision_Model_Exceptions_FindException
     * 		Is thrown if the entity could not be loaded
     * @throws TechDivision_Model_Exceptions_ContainerConfigurationException
     * 		Is thrown if the entity is not registered in the container
     * @see TechDivision_Model_Interfaces_Container::lookup($name, $key = null, $refresh = false)
     */
    public function lookup($name, $key)
    {
		// initialize a new bean
		$bean = null;
        // search for the entity
        if (!$this->_getBeans()->exists($name)) {
			throw new TechDivision_Model_Exceptions_ContainerConfigurationException(
				'Entity ' . $name . ' is not registered with deployment descriptor'
			);
		}
		// try to load the bean from the container
        $found = $this->_getBeans()->get($name);
        // log a message
		$this->getLogger()->debug(
			"Taking lookup for bean with name $name", __LINE__, __METHOD__
		);
        // make a lookup if maybe a cached version exists
		$bean = $found->lookup($key);
        // check if an instance of the bean exists
        if ($bean == null) {
        	// instanciate a new entity
            $className = $found->getName();
			// create a new instance and connect it to the container
            $bean = $this->newInstance($className);
			// load the bean data
            $bean->connect($this)->load($key);
			// add the bean to the container
			$this->_getBeans()->get($name)->add($bean);
			// log the successfully bean creation
			$this->getLogger()->debug(
				"Successfully created new instance of $className and key $key", __LINE__, __METHOD__
			);
        }
        // if yes, return the cached instance
        else {
        	// reconnect the bean
        	$bean->connect($this);
        	// log that a cached version of the been has been available
        	$this->getLogger()->debug(
        		"Found cached bean $name with key $key", __LINE__, __METHOD__
        	);
        }
        // return the entity/session bean instance
        return $bean;
    }

    /**
     * Removes the passed bean (entity/session) from the container.
     *
     * @param string $name Name of the bean
     * @param mixed $key Key of the bean to remove (only entity)
     * @throws TechDivision_Model_Exceptions_ContainerConfigurationException
     * 		Is thrown if the requested bean is not registered
     */
    public function removeBean($name, $key)
    {
        // search for the bean
        if (!$this->_getBeans()->exists($name)) {
			throw new TechDivision_Model_Exceptions_ContainerConfigurationException(
				'Entity ' . $name . ' is not registered with deployment descriptor'
			);
		}
		// load the entity to be removed
        $found = $this->_getBeans()->get($name);
        // make a lookup if maybe a cached version exists
		$bean = $found->lookup($key);
        // check if an instance of the bean exists
        if ($bean != null) {
			// remove the bean from the container
			$this->_getBeans()->get($name)->remove($bean);
            // log a message
			$this->getLogger()->debug(
				"Successfully remove $name with key {$bean->getPrimaryKey()}", __LINE__, __METHOD__
			);
        }
    }

    /**
     * This method initializes a new bean identified
     * by the passed name registers the bean in the
     * internal structure and returns a reference to it.
     *
     * @param string $name The name of the bean to be registered
     * @return TechDivision_Model_Interfaces_Bean
     * 		Holds a reference of the bean
     * @see TechDivision_Model_Interfaces_Container::register($name)
     */
    public function register($name)
    {
        // search for the epb
        $found = $this->_getBeans()->get($name);
        // instanciate a new entity
        $className = $found->getName();
		// create a new instance of the requested bean
        $bean = $this->newInstance($className)->connect($this);
        // log that the bean was successfully initialized
		$this->getLogger()->debug(
			"Successfully registered bean $name", __LINE__, __METHOD__
		);
        // return the bean instance
		return $bean;
    }

    /**
     * This method returns the database manager used
     * by the entities with read/write access.
     *
     * @return TechDivision_Model_Interfaces_Manager Returns the database manager
     * @see TechDivision_Model_Interfaces_Container::getMasterManager()
     */
    public function getMasterManager()
    {
    	return $this->_masterManager;
    }

    /**
     * This method returns one of the database managers used
     * by the entities with read only access.
     *
     * @param string $name Holds the name of the slave manager to use
     * @return TechDivision_Model_Configuration_Interfaces_Manager
     * 		Returns one the database managers with read only access
     * @return TechDivision_Model_Interfaces_Container::getSlaveManager($name = '')
     */
    public function getSlaveManager($name = '')
    {
    	// if no slave is defined, make the master to a slave
		if (sizeof($this->_slaveManagers) == 0) {
    		$this->_slaveManagers[] = $this->getMasterManager();
    	}
    	// if the flag forced to use the master manager is not set, return a slave
    	if (!$this->_forceMaster) {
			// define a random number for the active slave to use
			if ($this->_activeSlave === null) {
				if ((empty($name))) {
					// get the key of the slave manager to use
					$this->_activeSlave = rand(0, sizeof($this->_slaveManagers) - 1);
	        		// log the key of the slave manager to use
					$this->getLogger()->debug(
						'Now randomizing slave manager ' . $this->_activeSlave .
						' because of empty name' . $name,
					    __LINE__
					);
				}
				else {
					// check if the slave with the passed name exists
	        		if (($this->_activeSlave = TechDivision_Collections_CollectionUtils::findKey(
	        		    new TechDivision_Collections_ArrayList($this->_slaveManagers),
	        		    new TechDivision_Model_Predicates_FindSlaveByNamePredicate($name))) == null) {
						// get the key of the slave manager to use
						$this->_activeSlave = rand(
						    0,
						    sizeof($this->_slaveManagers) - 1
						);
	        			// log the key of the slave manager to use
						$this->getLogger()->debug(
							'Now randomizing slave manager ' . $this->_activeSlave .
							' because of missing name' . $name,
						    __LINE__
						);
	        		}
	        		else {
	        			// log the name of the slave manager to use
						$this->getLogger()->debug(
							'Now using named slave manager ' . $name,
						    __LINE__
						);
	        		}
				}
			}
			// load the used data source name
			$dataSourceName = $this->_slaveManagers[$this->_activeSlave]
				->getDataSourceName();
			// log the name of the slave manager to use
			$this->getLogger()
				->debug("Application is using slave $dataSourceName", __LINE__);
			// else return one of the slaves by random
			return $this->_slaveManagers[$this->_activeSlave];
    	}
		// else return the master
   		return $this->getMasterManager();
    }

    /**
	 * This method returns all slave managers.
	 *
	 * @return array Holds all slave database managers
	 * @see TechDivision_Model_Interfaces_Container::getSlaveManagers()
	 */
    public function getSlaveManagers()
    {
    	return $this->_slaveManagers;
    }

    /**
     * This method returns the dedicated database manager used
     * by the entities with the passed name and with read only access.
     *
     * @return TechDivision_Model_Interfaces_Manager
     * 		Returns the dedicated database manager with the requested name
     * @see TechDivision_Model_Interfaces_Container::getDedicatedManager($name)
     */
    public function getDedicatedManager($name)
    {
    	// if no dedicated database managers are defined throw an exception
		if (sizeof($this->_dedicatedManagers) == 0) {
    		throw new Exception(
    			'No dedicated database managers are defined for container'
    		);
    	}
    	// if the requested dedicated database managers is
    	// not defined throw an exception
    	if (!array_key_exists($name, $this->_dedicatedManagers)) {
			throw new Exception(
				'Dedicated database manager with name ' .
			    $name . 'is not defined in container'
			);
    	}
    	// log that a dedicated database manager was returned
    	$this->getLogger()->debug(
    		'Now returning dedicated database manager ' . $name,
    	    __LINE__
    	);
		// else return the requested dedicated database manager
		return $this->_dedicatedManagers[$name];
    }

    /**
     * This method returns the internal HashMap
     * with the beans.
     *
     * @return TechDivision_Collections_HashMap
     * 		The HashMap with the beans
     */
    protected function _getBeans()
    {
        return $this->_beans;
    }

    /**
     * This method sets the passed HashMap with beans.
     *
     * @param TechDivision_Collections_HashMap $beans
     * 		The HashMap with the beans
     */
    protected function _setBeans(TechDivision_Collections_HashMap $beans)
    {
        $this->_beans = $beans;
    }

    /**
     * This method returns the internal hash map
     * with container plugins.
     *
     * @return TechDivision_Collections_HashMap Holds the HashMap with plugins
     */
    protected function _getPlugins()
    {
        return $this->_plugins;
    }

    /**
     * This method sets the passed HashMap with
     * plugins.
     *
     * @param TechDivision_Collections_HashMap $plugins Holds the HashMap with plugins
     */
    protected function _setPlugins(TechDivision_Collections_HashMap $plugins)
    {
        // iterate over the plugin configuration
    	foreach ($plugins as $plugin) {
        	// instanciate the plugin
    		$instance = $this->newInstance($plugin->getType());
        	// add and initialize the plugin to the internal list
    		$this->_plugins[] = $instance;
        }
    }

    /**
     * This methods parses the passed xml element,
     * creates a new data source from the found
     * information and adds it to the available
     * data sources.
     *
     * @param SimpleXMLElement $sxe
     * 		Holds the xml with the data source information
     * @return void
     * @throws Exception Is thrown if the specified data source type is invalid
     */
    protected function _addDataSource(SimpleXMLElement $sxe)
    {
		// create a data source from the xml content
		$ds = TechDivision_Util_XMLDataSource::create($sxe);
		// add it to the internal datasources
		switch ($ds->getType()) {
			case TechDivision_Util_AbstractDataSource::MASTER:
				$this->_addMasterManager(
					$this->newInstance('TechDivision_Model_Manager_MySQLi', array($this, $ds))
				);
				break;
			case TechDivision_Util_AbstractDataSource::SLAVE:
				$this->_addSlaveManager(
					$this->newInstance('TechDivision_Model_Manager_MySQLi', array($this, $ds))
				);
				break;
			case TechDivision_Util_AbstractDataSource::DEDICATED:
				$this->_addDedicatedManager(
					$this->newInstance('TechDivision_Model_Manager_MySQLi', array($this, $ds))
				);
				break;
			default:
				throw new TechDivision_Model_Exceptions_InvalidDataSourceTypeException(
					'Invalid data source type ' . $ds->getType() . ' specified'
				);
				break;
		}
    }

    /**
     * This method adds the master database manager
     * for read/write access.
     *
     * @param TechDivision_Model_Interfaces_Manager $master
     * 		Holds the master database manager for read/write access
     */
    protected function _addMasterManager(
        TechDivision_Model_Interfaces_Manager $masterManager) {
		// set the master manager
		$this->_masterManager = $masterManager;
		// add the callback function to the master manager
		$this->_masterManager
			->addCallbackFunction('afterExecute', $this, 'useMasterOnly');
    }

    /**
     * This method adds a slave database manager
     * for read only access.
     *
     * @param TechDivision_Model_Interfaces_Manager $slaveManager
     * 		Holds a slave database manager for read only access
     */
    protected function _addSlaveManager(
        TechDivision_Model_Interfaces_Manager $slaveManager) {
		// add the slave manager to the array with all slave managers
		$this->_slaveManagers[] = $slaveManager;
    }

    /**
     * This method adds a dedicated database manager
     * for read only access.
     *
     * @param TechDivision_Model_Interfaces_Manager $dedicatedManager
     * 		Holds a dedicated database manager for read only access
     */
    protected function _addDedicatedManager(
        TechDivision_Model_Interfaces_Manager $dedicatedManager) {
		// add the dedicated manager to the array with all dedicated managers
		$this->_dedicatedManagers[
		    $dedicatedManager->getDataSourceName()
	    ] = $dedicatedManager;
    }

    /**
     * This method returns the logger of the requested
     * type for logging purposes.
     *
     * @param string The log type to use
     * @return TechDivision_Logger_Logger Holds the Logger object
     * @since 0.6.25 - 2011/12/16
     */
    public function getLogger(
    	$logType = TechDivision_Logger_System::LOG_TYPE_SYSTEM)
    {
    	return $this->getApp()->getLogger();
    }

    /**
     * This method initializes the plugins described
     * in the deployment descriptor.
     *
     * @return void
     */
    protected function _initializePlugins()
    {
    	foreach($this->_plugins as $plugin) {
    		$plugin->initialize($this);
    	}
    }

    /**
     * @throws TechDivision_Model_Exceptions_ContainerConfigurationException
     * 		Is thrown if no master data source was defined
     */
    protected function _initializeDataSources()
    {
		// create the filename to load the datasources from    	
    	$filename = $this->_containerConfiguration
    		->getConfigurationDirectory() . DIRECTORY_SEPARATOR . "ds.xml";
    	// check if the file is available
    	if (($data = file_get_contents($filename, true)) === false) {
    		return;
    	}
		// create a new xml element from the datasource
		$sxe = new SimpleXMLElement($data);
		// iterate over the data sources and add them
		foreach ($sxe->xpath("//datasources/datasource") as $sxe) {
			// initialize the data source
			$this->_addDataSource($sxe);
		}
		// check that at least a master datasource is defined
		if($this->_masterManager == null) {
			throw new TechDivision_Model_Exceptions_ContainerConfigurationException(
				'No master datasource was defined in the ' .
				' datasource configuration file'
			);
		}
    }

    /**
     * Returns the session cache key.
     *
     * @return string The session cache key
     */
    public function getCacheKey()
    {
    	return 'session_' . $this->getSessionId();
    }

    /**
     * This method forces the container to use the master
     * database manager only when requesting a slave.
     *
     * @return void
     * @see TechDivision_Model_Interfaces_Container::useMasterOnly()
     */
    public function useMasterOnly()
    {
		// check if the flag, force using the master database
		// manager, is already set
		if (!$this->_forceMaster) {
			// if not, set it
        	$this->_forceMaster = true;
        	// log setting the flag
        	$this->getLogger()->debug(
        		'Successfully set flag to force container using the master ' .
        	    ' database manager only',
        	    __LINE__
        	);
		}
    }

    /**
	 * This method returns the session id of the
	 * actual request.
	 *
	 * @return string Holds the requested session id
	 * @see TechDivision_Model_Interfaces_Container::getSessionId()
	 */
    public function getSessionId()
    {
    	return $this->_sessionId;
    }

    /**
     * Returns the cache instance.
     *
     * @return Zend_Cache_Core The cache instance
     */
    public function getCache()
    {
        return $this->getApp()->getCache();
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
    	return $this->getApp()->newInstance($className, $arguments);
    }

	/**
	 * Returns the system configuration.
	 *
	 * @return PEAR_Config The requested system configuration
	 */
	public function getSystemConfig()
	{
		return $this->getApp()->getSystemConfig();
	}

    /**
     * Creates a unique cache key for the methods with the
     * params passed as array.
     *
     * @param string $method Method name to create the cache key for
     * @param array $params The method params
     * @return string The unique cache key
     */
    public function createCacheKey($method, array $params = array())
    {
    	return strtolower(str_replace('::', '_', $method)) . '_' . md5(implode('', $params));
    }
}