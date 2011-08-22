<?php

interface Pimcore_Test_Isetup {

    public function setTest(Pimcore_Test_Case $test);
    public function getTest();
    public function setIsEnable($enable);
    public function getIsEnable();
    public function setUp();
    public function tearDown();


}
