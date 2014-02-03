<?php

/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision GmbH - Connect.
 *
 * TechDivision_Generator is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * TechDivision_Generator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 *
 * @package TechDivision_Generator
 */

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('BP', '${php-target.dir}');

$paths[] = '${pear.lib.dir}';
$paths[] = BP . DS . 'var' . DS . 'cache';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'local';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'community';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'core';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'lib';

// set the new include path
set_include_path(implode(PS, $paths) . PS . get_include_path());

require_once 'TechDivision/XHProfPHPUnit/TestSuite.php';
require_once 'TDProject/ApplicationTest.php';
require_once 'TDProject/Factory/ObjectTest.php';
require_once 'TDProject/Proxy/GeneratorTest.php';

/**
 * The TestSuite that initializes all PHPUnit tests.
 *
 * @package TDProject
 * @author Tim Wagner <tw@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class TDProject_AllTests
{

    /**
     * Initializes the TestSuite.
     *
     * @return TechDivision_XHProfPHPUnit_TestSuite The initialized TestSuite
     */
    public static function suite()
    {
        // initialize the TestSuite
        $suite = new TechDivision_XHProfPHPUnit_TestSuite(
            '${ant.project.name}',
            '',
            '${release.version}'
        );
        // add a test
        $suite->addTestSuite('TDProject_ApplicationTest');
        $suite->addTestSuite('TDProject_Factory_ObjectTest');
        $suite->addTestSuite('TDProject_Proxy_GeneratorTest');
        // return the TestSuite itself
        return $suite;
    }
}