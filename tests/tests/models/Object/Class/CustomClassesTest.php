<?php

class Models_Object_Class_CustomClassesTest extends Pimcore_Test_Case {

    /**
     * @db
     */
    public function testSaveGetCustomObect() {

        copy($this->getFixture('website/var/classes/definition_1.psf'),PIMCORE_CLASS_DIRECTORY.'/definition_1.psf');
        $this->getDb()->exec(file_get_contents($this->getFixture('website/var/classes/testclass.sql')));

        copy($this->getFixture('website/var/classes/Test.php'), PIMCORE_CLASS_DIRECTORY.'/Test.php');
        mkdir(PIMCORE_CLASS_DIRECTORY.'/Test');
        copy($this->getFixture('website/var/classes/Test/List.php'), PIMCORE_CLASS_DIRECTORY.'/Test/List.php');

        $o = new Object_Test();
        $o->setKey('test');
        $o->setParentId(1);
        $o->setInputField('testcontent');
        $o->save();

        $o = Object_Test::getById($o->getId());
        $this->assertEquals($o->getInputField(), 'testcontent');
    }

}
