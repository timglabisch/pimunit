<?

class Element_ClassTest extends Pimcore_Test_Case_Db {


    /**
     * creates a field collection "collectionA" containing all available data types
     * @return void
     */
    public function testFieldCollectionCreate(){

        $fieldCollection = new Object_Fieldcollection_Definition();
        $fieldCollection->setKey("collectionA");

        $conf = new Zend_Config_Xml($this->getFixture('/pimcore/objects/field-collection-import.xml'));
        $importData = $conf->toArray();

        $layout = Object_Class_Service::generateLayoutTreeFromArray($importData["layoutDefinitions"]);
        $fieldCollection->setLayoutDefinitions($layout);
        $fieldCollection->save();

    }


    /**
     * creates a class called "unittest" containing all Object_Class_Data Types currently available.
     * @return void
     * @depends testFieldCollectionCreate
     */
    public function testClassCreate() {

        $conf = new Zend_Config_Xml($this->getFixture('/pimcore/objects/class-import.xml'));
        $importData = $conf->toArray();

        $layout = Object_Class_Service::generateLayoutTreeFromArray($importData["layoutDefinitions"]);

        $class = Object_Class::create();
        $class->setName("unittest");
        $class->setUserOwner(1);
        $class->save();

        $id = $class->getId();
        $this->assertTrue($id > 0);

        $class = Object_Class::getById($id);

        $class->setLayoutDefinitions($layout);

        $class->setUserModification(1);
        $class->setModificationDate(time());

        $class->save();

    }

    /**
     * makes sure, that the creation of objects with duplicate paths is not possible
     * @expectedException Zend_Db_Statement_Exception
     */
    public function testDuplicateClassName() {

        $class = Object_Class::create();
        $class->setName("testDuplicateClassName");
        $class->setUserOwner(1);
        $class->save();

        $class = Object_Class::create();
        $class->setName("testDuplicateClassName");
        $class->setUserOwner(1);
        $class->save();

    }




}
