<?php

/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision GmbH - Connect.
 *
 * TechDivision_Lang is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * TechDivision_Lang is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 *
 * @package TechDivision_AOP
 */

require_once 'TechDivision/AOP/Proxy/Generator.php';

/**
 * This class is a wrapper for all classes an Aspect relies on.
 *
 * The class intercepts the original method call using PHP's
 * magic __call method.
 *
 * The actual version only works for non static methods.
 *
 * @package TechDivision_AOP
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TDProject_Proxy_Generator extends TechDivision_AOP_Proxy_Generator
{

	/**
	 * The base directory for proxy generation.
	 * @var string
	 */
	protected $_baseDirectory = '';
	
	/**
	 * Sets the base directory for proxy generation.
	 * 
	 * @param string $baseDirectory The directory to generate the proxies in
	 * @return TDProject_Proxy_Generator The instance itself
	 */
	public function setBaseDirectory($baseDirectory)
	{
		$this->_baseDirectory = $baseDirectory;
		return $this;
	}
	
	/**
	 * Returns the base directory for proxy generation.
	 * 
	 * @return string The directory to generate the proxies in
	 */
	public function getBaseDirectory()
	{
		return $this->_baseDirectory;
	}

    /**
     * (non-PHPdoc)
     * @see TechDivision_AOP_Proxy_Generator::newProxy()
     */
    public function newProxy($className, array $arguments = array())
    {
        // create and set the ReflectionClass to create the Proxy for
        $this->setReflectionClass(new ReflectionClass($className));
        // create a new instance of the Proxy
        return TDProject_Factory_Object::get()
            ->newInstance($this->load(), $arguments)->initProxy();
    }

    /**
     * (non-PHPdoc)
     * @see TechDivision_AOP_Proxy_Generator::load()
     */
    public function load()
    {
        // load the filename of the proxy to generate
        $proxyFileName = $this->getBaseDirectory() . '/' . $this->getProxyFileName();
        // check if the file has already been generated
        if (!file_exists($proxyFileName)) {
            // initialize the directory for the proxy to be stored
            $dir = dirname($proxyFileName);
            // check if the directory exists
            if (!is_dir($dir)) {
                // if not, create it
                mkdir($dir, 0755, true);
            }
            // store the source code of the proxy
            file_put_contents($proxyFileName, $this->create());
        }
        // return the proxy's class name
        return $this->getProxyClass();
    }
}