<?php

class Pimcore_Test_Setup_Db extends Pimcore_Test_Setup_Db_Abstract implements Pimcore_Test_Isetup {

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

        if(in_array('db',$annotations['method']['group']))
            return true;

        return false;
    }

    public function setUp()
    {
        $this->setUpDatabase();

        // clear cache
        $this->setUpFiles();
    }

    public function tearDown()
    {
        $this->setUp();
    }

}
