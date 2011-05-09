<?php

class YamlTest extends Pimcore_Test_Case {

    /**
     * @group db
     * @fixture yaml/php.yml
     * @fixture yaml/php2.yml
     */
    public function testParseArray()
    {
        $yaml = new sfYamlParser();
        $value = $yaml->parse(file_get_contents($this->getFixture('yaml/basic.yaml')));
        $this->assertEquals($value, array('key'=>'value', 'arr' => array('key1'=> 'value1', 'key2'=> 'value2')));
    }

}
