<?php

class Pimcore_Test_CaseTest extends Pimcore_Test_Case {

    /**
     * @return Pimcore_Test_Case_Abstract
     */
    private function getObject()
    {
        return $this->getMockForAbstractClass('Pimcore_Test_Case');
    }

    /**
     * @group memory
     */
    public function testGetFixture()
    {
        $content = file_get_contents($this->getObject()->getFixture('where_i_am'));
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

        $this->setUpDatabase();

        $this->assertEquals($this->countDocuments(), 1);
    }

    protected function countCache()
    {
        $files = array();
        $ignoreFiles = array('.dummy', '..', '.');

        $cacheFiles = scandir($this->getFixture('website/var/cache/'));

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
        $this->setUpFiles();

        $this->assertEquals($this->countCache(), 0);
        $this->setUpFiles();

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

        $this->setUpFiles();

        $this->assertEquals($this->countCache(), 0);

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

    /**
     * @group memory
     */
    public function testGroupMemory()
    {
        $this->assertTrue($this->isMemoryTest());
        $this->assertFalse($this->isDbTest());
    }

    /**
     * @group db
     */
    public function testGroupDb()
    {
        $this->assertFalse($this->isMemoryTest());
        $this->assertTrue($this->isDbTest());
    }

    public function testGroupAll()
    {
        $this->assertTrue($this->isMemoryTest());
        $this->assertTrue($this->isDbTest());
    }

}