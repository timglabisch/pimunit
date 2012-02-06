<?php

class Models_Object_Class_CustomClassesTest extends Pimcore_Test_Case {

    /**
     * @db
     */
    public function testSaveGetCustomObect() {
        $object = Object_Class::create();
        $object->setName('test');

        $objectlayout = new Object_Class_Layout();

        $inputfield = new Object_Class_Data_Textarea();
        $inputfield->setTitle('title');
        $inputfield->setName('InputField');

        $objectlayout->addChild($inputfield);

        $object->setLayoutDefinitions($objectlayout);
        $object->setUserOwner(1);
        $object->save();

        $resource = $object->getResource();

        if(is_callable(array($resource, '__destruct')))
            $resource->__destruct();

        $o = new Object_Test();
        $o->setKey('test');
        $o->setParentId(1);
        $o->setInputField('testcontent');
        $o->save();

        $o = Object_Test::getById($o->getId());
        $this->assertEquals($o->getInputField(), 'testcontent');
    }

}
