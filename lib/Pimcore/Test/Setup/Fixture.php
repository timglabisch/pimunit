<?php

class Pimcore_Test_Setup_Fixture extends Pimcore_Test_Setup_Fixture_Abstract implements Pimcore_Test_Isetup {

    /** @var Pimcore_Test_Case $test */
    private $test;
    private $isEnable = null;

    public function setTest(Pimcore_Test_Case $test)
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

        if(!isset($annotations['method']['fixture']))
            return false;

        return true;
    }

    public function setUp()
    {
       $annotations = $this->getTest()->getAnnotations();

        foreach($annotations['method']['fixture'] as $val)
            $this->getClassesAndSave($this->getTest()->getFixture($val));

    }

    public function tearDown()
    {
        
    }

}
