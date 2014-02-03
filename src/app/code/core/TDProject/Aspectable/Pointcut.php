<?php


require_once 'TechDivision/Lang/Object.php';
require_once 'TechDivision/AOP/Pointcut.php';
require_once 'TDProject/Factory/Object.php';
require_once 'TechDivision/AOP/Advice/Before.php';
require_once 'TechDivision/AOP/Advice/Around.php';
require_once 'TechDivision/AOP/Advice/After.php';

class TDProject_Aspectable_Pointcut
    extends TechDivision_Lang_Object
{

    protected $_className = '';

    protected $_includeFile = '';

    protected $_interceptWithMethod = '';

    protected $_methodToIntercept = '';

    protected $_advice = 1;

    protected $_adviceMap = array(
        'before' => TechDivision_AOP_Advice_Before::IDENTIFIER,
        'around' => TechDivision_AOP_Advice_Around::IDENTIFIER,
        'after' => TechDivision_AOP_Advice_After::IDENTIFIER,
    );

    public static function create()
    {
        return new TDProject_Aspectable_Pointcut();
    }

    public function setClassName($className)
    {
        $this->_className = $className;
        return $this;
    }

    public function setIncludeFile($includeFile)
    {
        $this->_includeFile = $includeFile;
        return $this;
    }

    public function setInterceptWithMethod($interceptWithMethod)
    {
        $this->_interceptWithMethod = $interceptWithMethod;
        return $this;
    }

    public function setMethodToIntercept($methodToIntercept)
    {
        $this->_methodToIntercept = $methodToIntercept;
        return $this;
    }

    public function setAdvice($advice)
    {
        $this->_advice = $this->_adviceMap[$advice];
        return $this;
    }

    public function getClassName()
    {
        return $this->_className;
    }

    public function getIncludeFile()
    {
        return $this->_includeFile;
    }

    public function getInterceptWithMethod()
    {
        return $this->_interceptWithMethod;
    }

    public function getMethodToIntercept()
    {
        return $this->_methodToIntercept;
    }

    public function getAdvice()
    {
        return $this->_advice;
    }

    /**
     * Creates, initializes and returns a new AOP Pointcut
     * instance.
     *
     * @param TDProject_Application $app The Application with the ObjectFactory
     * @return TechDivision_AOP_Pointcut The initialized AOP Pointcut
     * @todo Implement Advice ordering and arguments for Aspect
     */
    public function getInstance(TDProject_Application $app)
    {
        // initialize the new Aspect
        $aspect = $app->getObjectFactory()
            ->newInstance($this->getClassName(), array());
        // create, initialize and return the AOP Pointcut
        return TechDivision_AOP_Pointcut::create()
	        ->aspect($aspect)
	        ->intercept($this->getMethodToIntercept())
	        ->setAdvice($this->getAdvice(), 0)
	        ->withMethod($this->getInterceptWithMethod());
    }
}