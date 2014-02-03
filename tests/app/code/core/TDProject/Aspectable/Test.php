<?php

require_once 'TechDivision/Lang/Object.php';

class TDProject_Aspectable_Test
    extends TechDivision_Lang_Object {

    protected $_arg = 'test';

    public function somefunction($arg1)
    {
        return $this->_arg .= $arg1;
    }

    public function getArg()
    {
        return $this->_arg;
    }
}