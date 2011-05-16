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
        $this->assertEquals($document->getKey(), 'Testdokument');

        unset($document);

        $document = Document_Page::getByPath('/Testdokument');
        $this->assertEquals($document->getKey(), 'Testdokument');
    }

     /**
     * @group db
     * @fixture yaml/php.yml
      *@fixture yaml/php2.yml
     */
    public function testLoadMultiple()
    {
        $document = Document_Page::getByPath('/Testdokument');
        $this->assertEquals($document->getKey(), 'Testdokument');

        $document = Document_Page::getByPath('/Testdokument4');
        $this->assertEquals($document->getKey(), 'Testdokument4');
    }

}