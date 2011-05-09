<?php

class YamlTest extends Pimcore_Test_Case {

    public function testParseArray()
    {
        $yaml = new sfYamlParser();
        $value = $yaml->parse(file_get_contents($this->getFixture('yaml/basic.yaml')));
        $this->assertEquals($value, array('key'=>'value', 'arr' => array('key1'=> 'value1', 'key2'=> 'value2')));
    }

    public function testParsePhp()
    {
        $this->markTestIncomplete();
        $yaml = new sfYamlParser();
        $value = $yaml->parse(file_get_contents($this->getFixture('yaml/php.yml')));
        $this->assertEquals($value, array('key'=>'value'));
    }

}
