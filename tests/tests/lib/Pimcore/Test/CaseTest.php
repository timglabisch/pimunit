<?php

class Pimcore_Test_CaseTest extends Pimcore_Test_Case {


    /**
     * @group memory
     */
    public function testGetFixture()
    {
        $content = file_get_contents($this->getFixture('where_i_am'));
        $this->assertEquals($content, 'pimunit');
    }

    protected function countDocuments()
    {
        $res = $this->getDb()->fetchAll('SELECT * FROM documents');
        return count($res);
    }

    /**
     * @group db
     */
    public function testSetUpDatabaseByDocument()
    {
        $this->assertEquals($this->countDocuments(), 1);

        $document = new Document_Page();
        $document->setKey('test');
        $document->setParentId(1);
        $document->setPublished(1);
        $document->save();

        unset($document);

        $this->assertEquals($this->countDocuments(), 2);

        $this->setUp();

        $this->assertEquals($this->countDocuments(), 1);
    }

    protected function countCache()
    {
        $files = array();
        $ignoreFiles = array('.dummy', '..', '.');

        $cacheFiles = scandir(PIMCORE_CACHE_DIRECTORY);

        foreach($cacheFiles as $file)
        {
            if(!in_array(basename($file), $ignoreFiles))
                $files[] = $file;
        }

        return count($files);
    }

    /**
     * @group db
     */
    public function testSetUpFilesByDocument()
    {
        $this->setUp();

        $this->assertEquals($this->countCache(), 0);
        $this->setUp();

        // create a document
        $document = new Document_Page();
        $document->setKey('test');
        $document->setParentId(1);
        $document->setPublished(1);
        $document->save();

        // reload the document (pimcore creates a cache entry)
        $id = $document->getId();
        $document = Document_Page::getById($id);
        $document->getDescription();
        $this->flushIOCache();

        $this->assertTrue($this->countCache() > 0);

        $this->setUp();

        $this->assertEquals($this->countCache(), 0);

    }

    /**
     * @group memory
     */
    public function testGroupMemory()
    {

        $mockDb = $this->getMock('Pimcore_Test_Setup_Db');
        $mockDb->expects($this->once())
               ->method('GetIsEnable')
               ->will($this->returnValue(false));
        $mockDb->expects($this->never())
               ->method('setUp');

        $mockFixture = $this->getMock('Pimcore_Test_Setup_Db');
        $mockFixture->expects($this->once())
            ->method('GetIsEnable')
            ->will($this->returnValue(false));
        $mockFixture->expects($this->never())
            ->method('setUp');

        $mockMem = $this->getMock('Pimcore_Test_Setup_Memory');
        $mockMem->expects($this->once())
               ->method('GetIsEnable')
               ->will($this->returnValue(true));
        $mockMem->expects($this->once())
               ->method('setUp');

        $backupDi = self::$di;

        self::$di = new de\any\di();
        self::$di->bind('Pimcore_Test_Isetup')->to($mockDb)->concern('db');
        self::$di->bind('Pimcore_Test_Isetup')->to($mockMem)->concern('memory');
        self::$di->bind('Pimcore_Test_Isetup')->to($mockFixture)->concern('fixture');

        $this->setUp();

        self::$di = $backupDi;
    }

    /**
     * @group memory
     */
    public function testGroupMultiple()
    {

        $mockDb = $this->getMock('Pimcore_Test_Setup_Db');
        $mockDb->expects($this->once())
            ->method('GetIsEnable')
            ->will($this->returnValue(true));
        $mockDb->expects($this->once())
            ->method('setUp');

        $mockFixture = $this->getMock('Pimcore_Test_Setup_Db');
        $mockFixture->expects($this->once())
            ->method('GetIsEnable')
            ->will($this->returnValue(true));
        $mockFixture->expects($this->once())
            ->method('setUp');

        $mockMem = $this->getMock('Pimcore_Test_Setup_Memory');
        $mockMem->expects($this->once())
            ->method('GetIsEnable')
            ->will($this->returnValue(true));
        $mockMem->expects($this->once())
            ->method('setUp');

        // custom ...
        $mockCustom = $this->getMock('Pimcore_Test_Setup_Memory');
        $mockCustom->expects($this->once())
            ->method('GetIsEnable')
            ->will($this->returnValue(true));
        $mockCustom->expects($this->once())
            ->method('setUp');

        // custom ...
        $mockCustom2 = $this->getMock('Pimcore_Test_Setup_Memory');
        $mockCustom2->expects($this->once())
            ->method('GetIsEnable')
            ->will($this->returnValue(false));
        $mockCustom2->expects($this->never())
            ->method('setUp');

        $backupDi = self::$di;

        self::$di = new de\any\di();
        self::$di->bind('Pimcore_Test_Isetup[]')->shared(true);
        self::$di->bind('Pimcore_Test_Isetup')->to($mockDb)->concern('db');
        self::$di->bind('Pimcore_Test_Isetup')->to($mockMem)->concern('memory');
        self::$di->bind('Pimcore_Test_Isetup')->to($mockFixture)->concern('fixture');

        self::$di->get('Pimcore_Test_Isetup[]')->append($mockCustom);
        self::$di->get('Pimcore_Test_Isetup[]')->append($mockCustom2);

        $this->setUp();

        self::$di = $backupDi;
    }

   /**
     * @group db
     */
    function testDatabaseDriver() {
        $this->assertTrue(Pimcore_Resource_Mysql::getConnection()->getResource() instanceof Pimunit_Db_Adapter_Standard_Mysqli || Pimcore_Resource_Mysql::getConnection()->getResource() instanceof Pimunit_Db_Adapter_Standard_Pdo_Mysql);
        $this->assertTrue(Pimcore_Resource_Mysql::get()->getResource() instanceof Pimunit_Db_Adapter_Standard_Mysqli || Pimcore_Resource_Mysql::getConnection()->getResource() instanceof Pimunit_Db_Adapter_Standard_Pdo_Mysql);
    }
}