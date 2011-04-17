<?
class Element_LazyLoadingTest extends Pimcore_Test_Case_Db {


    public function testLazyLoading() {

         //reset registry
        Pimcore_Test_Tool::resetRegistry();

        //find an object with data
        $objectList = new Object_List();
        $objectList->setCondition("o_key like '%_data' and o_type = 'object'");
        $objects = $objectList->load();

        $this->assertTrue($objects[0] instanceof Object_Abstract);

        //check for lazy loading elements
        $this->assertTrue($objects[0]->lazyObjects === null);
        $this->assertTrue(is_array($objects[0]->getLazyObjects()));

        $this->assertTrue($objects[0]->lazyMultihref === null);
        $this->assertTrue(is_array($objects[0]->getLazyMultihref()));

        $this->assertTrue($objects[0]->lazyHref === null);
        $this->assertTrue(is_object($objects[0]->getLazyHref()));

    }

}