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

        $mockMem = $this->getMock('Pimcore_Test_Setup_Memory');
        $mockMem->expects($this->once())
               ->method('GetIsEnable')
               ->will($this->returnValue(true));

        $mockMem->expects($this->once())
               ->method('setUp');

        $this->setSetups(
            array(
                $mockDb,
                $mockMem
                )
        );

        $this->setUp();
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

        $mockMem = $this->getMock('Pimcore_Test_Setup_Memory');
        $mockMem->expects($this->once())
               ->method('GetIsEnable')
               ->will($this->returnValue(true));

        $mockMem2 = $this->getMock('Pimcore_Test_Setup_Memory');
        $mockMem2->expects($this->once())
               ->method('GetIsEnable')
               ->will($this->returnValue(false));

        $mockMem2->expects($this->never())
               ->method('setUp');

        $this->setSetups(
            array(
                $mockDb,
                $mockMem,
                $mockMem2
                )
        );

        $this->setUp();
    }
}