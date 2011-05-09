<?
abstract class Pimcore_Test_Case extends Pimcore_Test_Case_Abstract {

    private $setups = null;

    public function flushIOCache()
    {
        Pimcore_Model_Cache::write();
    }

    public function getFixture($path)
    {
        if(file_exists(getcwd().'/tests/fixtures/'.$path))
            return getcwd().'/tests/fixtures/'.$path;

        return getcwd().'/fixtures/'.$path;
    }

    public function getSetups()
    {
        if($this->setups != null)
            return $this->setups;

        return array(
            new Pimcore_Test_Setup_Db(),
            new Pimcore_Test_Setup_Memory()
        );
    }

    public function setSetups(array $setups)
    {
        $this->setups = $setups;
    }
    
    /**
     * @return mixed|Zend_Db_Adapter_Abstract
     */
    protected function getDb()
    {
        return Pimcore_Resource_Mysql::get();
    }

    public function setUp()
    {
        foreach($this->getSetups() as $setup)
        {
            $setup->setTest($this);

            if($setup->getIsEnable())
                $setup->setUp();
        }

        parent::setUp();
    }

    public function tearDown()
    {
        foreach($this->getSetups() as $setup)
        {
            $setup->setTest($this);

            if($setup->getIsEnable())
                $setup->tearDown();
        }

        parent::tearDown();
    }

}
