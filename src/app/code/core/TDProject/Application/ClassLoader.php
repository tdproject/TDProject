<?php

/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision GmbH - Connect.
 *
 * faett.net is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * faett.net is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 *
 * @package TechDivision_VFS
 */

require_once 'TechDivision/VFS/ClassLoader.php';

/**
 * @package TechDivision_VFS
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TDProject_Application_ClassLoader 
	extends TechDivision_VFS_ClassLoader
{
    
	/**
	 * Array with the include path elements.
	 * @var array
	 */
    protected $_includePaths = '';
    
	/**
	 * Array with the classes already have been loaded.
	 * @var array
	 */
    protected $_classesLoaded = array();
    
	/**
	 * The reference to the application.
	 * @var TDProject_Application
	 */
    protected $_app = null;

    /**
     * Initialize the instance with the class path.
     * 
     * @return void
     */
    protected function __construct()
    {
        parent::__construct();
        // initialize the class path elements
        $this->_includePaths = explode(PATH_SEPARATOR, get_include_path());
    }

    /**
     * Returns the class loader instance as singleton.
     * 
     * @return TDProject_Application_ClassLoader
     * 		The class loader instance as singleton
     */
    public static function get()
    {
        if (self::$_instance == null) {
            self::$_instance = new TDProject_Application_ClassLoader();
        }
        return self::$_instance;
    }

    /**
     * Registers the class loader for system autoloading functionality.
     * 
     * @return TDProject_Application_ClassLoader The instance itself
     */
    public static function register()
    {
        spl_autoload_register(array($instance = self::get(), 'autoload'));
        return $instance;
    }
    
    /**
     * Reference to the application.
     * 
     * @param TDProject_Application $app The application instance
     */
    public function setApp(TDProject_Application $app)
    {
    	$this->_app = $app;
    }
    
    /**
     * Returns the application reference.
     * 
     * @return TDProject_Application The application instance
     */
    public function getApp()
    {
    	return $this->_app;
    }
    
    /**
     * Returns a reference to the system cache.
     * 
     * @return Zend_Cache_Core The system cache instance
     */
    public function getCache()
    {
    	return $this->getApp()->getCache();
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision_VFS_Interfaces_ClassLoader::import()
     */
    public function import($filename)
    {
        // create the virtual file
        $file = new TechDivision_VFS_File($filename);
        // check if the has already been imported
        foreach($this->_vfs as $vfs) {
            if ($vfs->exists($file)) {
                // include the file and return
                return $this->requireOnce($vfs->get($file));
            }
        }
        // if not, attach the file to the filesystem, include it and return
        return $this->requireOnce($this->_attach($file));
    }
    
    /**
     * Returns the array with the class path elements.
     * 
     * @return array The class path elements
     */
    public function getIncludePaths()
    {
    	return $this->_includePaths;
    }
    
    /**
     * Replacement for the standard require_once method that has 
     * performance problems because of using several codepools.
     * 
     * The increase performance the complete path to every class 
     * that has been loaded successfully is stored to the system
     * cache und will be used next time.
     * 
     * @return void
     * @see https://bugs.php.net/bug.php?id=55475 Quickfix for PHP 5.3.8 bug
     */
    protected function requireOnce($file)
    {
    	try {
    		// first check if class has already been included	
	    	if (in_array($file, $this->_classesLoaded)) {
	    		return;
	    	}
	    	// create a cache key
	    	$cacheKey = str_replace('.php', '', str_replace(DIRECTORY_SEPARATOR, '_', $file));
	    	// try to load the complete filename from the cache
	    	if ($filename = $this->getCache()->load($cacheKey)) {
	    		return require_once $filename;
	    	}
	    	// load the configured include paths
	    	$includePaths = $this->getIncludePaths();
	    	// iterate over the include paths
	    	for ($i = 0; $i < sizeof($includePaths); $i++) {
	    		// concatenate the filename
	    		$filename = $includePaths[$i] . DIRECTORY_SEPARATOR . $file;
	    		// check if the file is available
	    		if (file_exists($filename)) {
	    			// and if the file as already been included
	    			if (in_array($file, $this->_classesLoaded)) {
	    				return;
	    			}
	    			// add the file to the array with the included ones
	    			$this->_classesLoaded[] = $file;
	    			// save the complete path to the cache
	    			$this->getCache()->save($filename, $cacheKey);
	    			// include the file
	    			return require_once $filename;
	    		}
	    	}
    	}
    	/*
    	 * This is a quickfix for a PHP 5.3.8 bug.
    	 * @see https://bugs.php.net/bug.php?id=55475
    	 */
    	catch (Zend_Cache_Exception $zce) {
    		return;
    	}
    }
}