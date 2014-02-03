<?php

require_once 'TechDivision/Lang/Object.php';
require_once 'TechDivision/AOP/Interfaces/Aspectable.php';

class TDProject_Aspectable_Abstract 
    extends TechDivision_Lang_Object
    implements TechDivision_AOP_Interfaces_Aspectable
{
    
    protected $_useAspect = true;
    
    public function useAspect()
    {
        return $this->_useAspect;    
    }
}