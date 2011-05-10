<?php

class YamlTest extends Pimcore_Test_Case {

    /**
     * @group db
     */
    public function testParseArray()
    {
        $yaml = new sfYamlParser();
        $value = $yaml->parse(file_get_contents($this->getFixture('yaml/basic.yaml')));
        $this->assertEquals($value, array('key'=>'value', 'arr' => array('key1'=> 'value1', 'key2'=> 'value2')));
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
