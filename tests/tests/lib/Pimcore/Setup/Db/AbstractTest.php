<?php
class Pimcore_Test_Setup_DbTest extends Pimcore_Test_Case
{

    /**
     * @return Pimcore_Test_Setup_Db_Abstract
     */
    public function getObject()
    {
        return $this->getMockForAbstractClass('Pimcore_Test_Setup_Db_Abstract');
    }
    
    /**
     * @expectedException Pimcore_Test_Case_Db_Exception
     * @group memory
     */
    public function testSetDbNameWrongSuffix()
    {
        $this->getObject()->setDbName('wayne');
    }

    /**
     * @group memory
     */
    public function testSetDbName()
    {
        $o =  $this->getObject();
        $o->setDbName('wayne_test');
        $this->assertEquals($o->getDbName(), 'wayne_test');
    }

}