<?php

/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision GmbH - Connect.
 *
 * TechDivision_Utilis free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * TechDivision_Utilis distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 *
 * @package TechDivision_Util
 */

require_once 'TechDivision/Lang/Boolean.php';
require_once 'TechDivision/Collections/HashMap.php';
require_once 'TechDivision/Util/AbstractDataSource.php';

/**
 * This is the data source implementation used for
 * holding the information from the XML file.
 *
 * @package TechDivision_Util
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TechDivision_Util_XMLDataSource
    extends TechDivision_Util_AbstractDataSource
{

   	/**
	 * This is the factory method to create a new instance of the
	 * DataSource from the information passed as a parameter.
	 *
	 * @param SimpleXMLElement $sxe
	 * 		Holds the information to initialize the DataSource with
	 * @return TechDivision_Util_XMLDataSource Holds the initialized DataSource
	 */
    public static function create(SimpleXMLElement $sxe)
    {
    	// initialize the autocommit value
    	try {
    		$autocommit = new TechDivision_Lang_Boolean($sxe->autocommit);
    	}
    	catch(TechDivision_Lang_Exceptions_ClassCastException $cce) {
    		$autocommit = new TechDivision_Lang_Boolean(false);
    	}
        // initialize and return the data source
		return new TechDivision_Util_XMLDataSource(
		    (string)  $sxe->type,
		  	(string)  $sxe->name,
		  	(string)  $sxe->host,
		  	(integer) $sxe->port,
		 	(string)  $sxe->database,
		  	(string)  $sxe->driver,
		  	(string)  $sxe->user,
		  	(string)  $sxe->password,
		  	(string)  $sxe->encoding,
		  	$autocommit->booleanValue()
        );
    }

    /**
     * This method is the factory method to create a new instance
     * of the DataSource from a descriptor file with at least one
     * <datasource> element.
     *
     * The method instanciates and returns the DataSource with the
     * passed name, if found, or null optional.
     *
     * @param string $name
     * 		Holds the name of the DataSource to instanciate and return
     * @param string $descriptor
     * 		Holds the path and filename of the descriptor with the
     * 		datasource definitions
     * @return TechDivision_Util_XMLDataSource
     * 		The initialized DataSource instance
     * @throws Exception
     * 		Is thrown if the descriptor can not be opened or the reqeusted
     * 		<datasource> element is not defined in the descriptor
     */
    public static function createByName($name, $descriptor)
    {
    	// read the descriptors content
    	if (($content = file_get_contents($descriptor, true)) === false) {
    		throw new Exception(
    			'The descriptor file ' . $descriptor . ' can not be opened'
    		);
    	}
		// create a new xml element from the datasource
    	$sxe = new SimpleXMLElement($content);
		// iterate over the data sources and add them
		foreach ($sxe->xpath('//datasources/datasource') as $element) {
			// initialize the data source
			if ((string) $element->name == $name) {
				return TechDivision_Util_XMLDataSource::create($element);
			}
		}
		// throw an exception if the descriptor does not contain
		// a <datasource> element with the passed name
		throw new Exception(
			'The datasource ' . $name . ' is not defined in descriptor file '
		    . $descriptor
		);
    }

    /**
     * This method is the factory method to create a HashMap with
     * all DataSources from a descriptor file with the passed type.
     *
     * The method instanciates and returns a HashMap with the
     * DataSource instances with the passed type, if found, or
	 * an empty HashMap if no datasources with the passed type
	 * are defined in the desriptor.
     *
     * @param string $type 	Holds the type of the DataSource instances to return
     * @param string $descriptor 	Holds the path and filename of the
     * 								descriptor with the datasource definitions
     * @return TechDivision_Collections_HashMap 	A HashMap with all
     * 												initialized DataSource
     * 												instances of the passed type
     * @throws Exception Is thrown if the passed type is not available
     * @throws Exception 	Is thrown if the descriptor can not be opened or the
     * 						requested <datasource> element is not defined in the
     * 						descriptor
     */
    public static function createByType($type, $descriptor)
    {
		// check if the passed type is available and valid
    	if (!TechDivision_Util_AbstractDataSource::isValidType($type)) {
    		throw new Exception(
    			'Invalid type ' . $type . ' for DataSource requested'
    		);
    	}
    	// read the descriptors content
    	if (($content = file_get_contents($descriptor, true)) === false) {
    		throw new Exception(
    			'The descriptor file ' . $descriptor . ' can not be opened'
    		);
    	}
    	// initialize the HashMap for the DataSource instances
    	$map = new TechDivision_Collections_HashMap();
		// create a new xml element from the datasource
    	$sxe = new SimpleXMLElement($content);
		// iterate over the data sources and add them
		foreach($sxe->xpath('//datasources/datasource') as $element) {
			// initialize the data source
			if ((string) $element->type == $type) {
				// initialize the datasource and add it to the HashMap
				$map->add(
				    (string) $element->name,
				    TechDivision_Util_XMLDataSource::create($element)
				);
			}
		}
		// returns the HashMap with the initialized DataSource instances
		return $map;
    }
    
    /**
     * Stores the data source information into the
     * file with the passed name.
     * 
     * The following structure will be saved in a file
     * with the passed filename.
     * 
     * <datasources 
     * 	   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	 *     xsi:noNamespaceSchemaLocation="http://pear.struts4php.org/schema/datasource-1.0.0.xsd">
	 *     <datasource>
	 *         <type>master</type>
	 *         <name>master</name>
	 *         <driver>mysqli</driver>
	 *         <host>localhost</host>
	 *         <port>3306</port>
	 *         <database>database</database>
	 *         <user>username</user>
	 *         <password>somesecret</password>
	 *         <encoding>utf8</encoding>
	 *	       <autocommit>false</autocommit>
	 *     </datasource>
	 * </datasources>
     * 
     * @param string $filename Name of the file to store the datasource to
     * @return DOMDocument The data source information as DOMDocument
     */
    public function toXML()
    {
    	// create a new DOMDocument instance
    	$doc = new DOMDocument();
    	// initialize the root node and the first child
    	$datasources = $doc->createElement('datasources');
    	$datasource = $doc->createElement('datasource');
    	// append the datasource values
    	$datasource->appendChild(
    		$doc->createElement('type', $this->getType())
    	);
    	$datasource->appendChild(
    		$doc->createElement('name', $this->getName())
    	);
    	$datasource->appendChild(
    		$doc->createElement('driver', $this->getDriver())
    	);
    	$datasource->appendChild(
    		$doc->createElement('host', $this->getHost())
    	);
    	$datasource->appendChild(
    		$doc->createElement('port', $this->getPort())
    	);
    	$datasource->appendChild(
    		$doc->createElement('database', $this->getDatabase())
    	);
    	$datasource->appendChild(
    		$doc->createElement('user', $this->getUser())
    	);
    	$datasource->appendChild(
    		$doc->createElement('password', $this->getPassword())
    	);
    	$datasource->appendChild(
    		$doc->createElement('encoding', $this->getEncoding())
    	);
    	if ($this->getAutocommit()) {
    		$autocommit = 'true';
    	}
    	else {
    		$autocommit = 'false';
    	}
    	$datasource->appendChild(
    		$doc->createElement('autocommit', $autocommit)
    	);
    	// append the datasources node
    	$datasources->appendChild($datasource);
    	$doc->appendChild($datasources);
    	// return the DOMDocument
    	return $doc;
    }
}