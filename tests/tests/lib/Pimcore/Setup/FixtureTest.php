<?php
class Pimcore_Test_Setup_FixtureTest extends Pimcore_Test_Case
{
    /**
     * works because if you dont implement a group, you'll load all
     * @fixture yaml/php.yml
     */
    public function testGetLoadDbDefault()
    {
      
    }


    /**
     * @group db
     * @fixture yaml/php.yml
     */
    public function testLoadFixture()
    {
        $document = Document_Page::getById(2);
        $this->assertEquals($document->getKey(), 'testdokument');

        unset($document);

        $document = Document_Page::getByPath('/testdokument');
        $this->assertEquals($document->getKey(), 'testdokument');
    }

     /**
     * @group db
     * @fixture yaml/php.yml
      *@fixture yaml/php2.yml
     */
    public function testLoadMultiple()
    {
        $document = Document_Page::getByPath('/testdokument');
        $this->assertEquals($document->getKey(), 'testdokument');

        $document = Document_Page::getByPath('/testdokument4');
        $this->assertEquals($document->getKey(), 'testdokument4');
    }

}