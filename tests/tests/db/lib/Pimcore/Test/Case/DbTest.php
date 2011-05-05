<?php

class Pimcore_Test_Case_DbTest extends Pimcore_Test_Case_Db {

    /**
     * @return Pimcore_Test_Case_Db
     */
    private function getObject()
    {
        return $this->getMockForAbstractClass('Pimcore_Test_Case_Db');
    }

    protected function countDocuments()
    {
        $res = $this->getDb()->fetchAll('SELECT * FROM documents');
        return count($res);
    }

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

}