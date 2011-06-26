<?php

class Models_Object_Class_CustomClassesTest extends Pimcore_Test_Case {

    /**
     * @db
     */
    public function testSaveGetCustomObect() {

        $this->getDb()->exec(file_get_contents($this->getFixture('website/var/classes/testclass.sql')));

        $o = new Object_Test();
        $o->setKey('test');
        $o->setParentId(1);
        $o->setInputField('testcontent');
        $o->save();

        $o = Object_Test::getById($o->getId());
        $this->assertEquals($o->getInputField(), 'testcontent');
    }

}
