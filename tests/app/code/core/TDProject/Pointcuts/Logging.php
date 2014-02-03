<?php

require_once 'TechDivision/Lang/Object.php';
require_once 'TechDivision/AOP/Interfaces/Aspect.php';
require_once 'TechDivision/AOP/Interfaces/JoinPoint.php';
require_once 'TDProject/ApplicationTest.php';

class TDProject_Pointcuts_Logging
    extends TechDivision_Lang_Object
    implements TechDivision_AOP_Interfaces_Aspect {

    public function log(TechDivision_AOP_Interfaces_JoinPoint $joinPoint)
    {
		$aspectable = $joinPoint
    		->getMethodInterceptor()
            ->getAspectContainer()
            ->getAspectable();

         TDProject_ApplicationTest::$logged = $aspectable->getArg();
    }
}