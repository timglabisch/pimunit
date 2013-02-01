<?
class Pimcore_Test_Case extends Pimcore_Test_Case_Abstract {

    /** @var \de\any\iDi */
    public static $di;

    /** @var Pimcore_Test_Isetup[] */
    public $setupRepository;

    private $setups = null;

    public function flushIOCache()
    {
        Pimcore_Model_Cache::write();
    }

    public function getFixture($path)
    {
        if(file_exists(getcwd().'/tests/fixtures/'.$path))
            return getcwd().'/tests/fixtures/'.$path;

        if(file_exists(getcwd().'/fixtures/'.$path))
            return getcwd().'/fixtures/'.$path;

        return getcwd().'/'.$path;
    }

    /**
     * @return Pimcore_Test_Isetup[]
     */
    public function getAndInitializedSetupRepository()
    {
        $this->setupRepository = self::$di->get('Pimcore_Test_Isetup[]');

        $this->setupRepository->offsetSet('db', self::$di->get('Pimcore_Test_Isetup', 'db'));
        $this->setupRepository->offsetSet('memory', self::$di->get('Pimcore_Test_Isetup', 'memory'));
        $this->setupRepository->offsetSet('fixture', self::$di->get('Pimcore_Test_Isetup', 'fixture'));

        return $this->setupRepository;
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
        foreach($this->getAndInitializedSetupRepository() as $setup)
        {
            $setup->setTest($this);

            if($setup->getIsEnable())
                $setup->setUp();
        }

        parent::setUp();
    }

    public function tearDown()
    {
        foreach($this->getAndInitializedSetupRepository() as $setup)
        {
            $setup->setTest($this);

            if($setup->getIsEnable())
                $setup->tearDown();
        }

        parent::tearDown();
    }

}
