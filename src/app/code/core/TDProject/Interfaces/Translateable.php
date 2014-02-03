<?php

/**
 * TDProject_Interfaces_Translateable
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

require_once 'TechDivision/Lang/Object.php';

/**
 * @category    TDProject
 * @package     TDProject
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
interface TDProject_Interfaces_Translateable {

    /**
     * Returns the Translator instance implementing
     * the translation functionality.
     *
     * @return TDProject_Interfaces_Translator
     * 		The Translator instance
     */
    public function getTranslator();

    /**
     * Performs the translation of the implementing object by using
     * the passed translator.
     *
     * @param TDProject_Core_Interfaces_Block $block
     * 		The key to return the translation for
     * @return TechDivision_Lang_Object
     * 		The instance itself
     */
    public function trsl();

    /**
     * The resource key of the resource to translate.
     *
     * @return TechDivision_Lang_String
     * 		The resource key to translate
     */
    public function getResourceKey();

    /**
     * The resource package of the resource to translate.
     *
     * @return TechDivision_Lang_String
     * 		The resource package to translate
     */
    public function getResourcePackage();
}