<?
class EasyTests_Db_helloTest extends Pimcore_Test_Case_Db  {

    public function testHelloWorld()
    {
        $this->assertGreaterThan(
            0,
            count($this->getDb()->fetchAll('Show Tables from '. $this->getDbName()))
        );
    }

}
