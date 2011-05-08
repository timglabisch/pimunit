<?php

class Pimcore_Test_Setup_Memory extends Pimcore_Test_Setup_Memory_Abstract implements Pimcore_Test_Isetup {

    /** @var PHPUnit_Framework_TestCase $test */
    private $test;
    private $isEnable = null;

    public function setTest(PHPUnit_Framework_TestCase $test)
    {
        $this->test = $test;
        return $this;
    }

    public function getTest()
    {
        return $this->test;
    }

    public function setIsEnable($enable)
    {
        $this->isEnable = $enable;
        return $this;
    }

    public function getIsEnable()
    {
        if($this->isEnable != null)
            return $this->isEnable;

        $annotations = $this->getTest()->getAnnotations();

        if(!isset($annotations['method']['group']))
            return true;

        if(in_array('memory',$annotations['method']['group']))
            return true;

        return false;
    }

    public function setUp()
    {
       
    }

    public function tearDown()
    {
        
    }

}
