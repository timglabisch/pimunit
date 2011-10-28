<?php

class Models_Object_Class_CustomClassesTest extends Pimcore_Test_Case {

    /**
     * @db
     */
    public function testSaveGetCustomObect() {

        copy($this->getFixture('website/var/classes/definition_1.psf'),PIMCORE_CLASS_DIRECTORY.'/definition_1.psf');

        copy($this->getFixture('website/var/classes/Test.php'), PIMCORE_CLASS_DIRECTORY.'/Object/Test.php');
        mkdir(PIMCORE_CLASS_DIRECTORY.'/Object/Test');
        copy($this->getFixture('website/var/classes/Test/List.php'), PIMCORE_CLASS_DIRECTORY.'/Object/Test/List.php');

        $o = new Object_Test();
        $o->setKey('test');
        $o->setParentId(1);
        $o->setInputField('testcontent');
        $o->save();

        $o = Object_Test::getById($o->getId());
        $this->assertEquals($o->getInputField(), 'testcontent');
    }

}
