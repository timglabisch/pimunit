<?php
class Pimcore_Test_Setup_FixtureTest extends Pimcore_Test_Case
{

    public function testGetClasses()
    {
        $fixture = new Pimcore_Test_Setup_Fixture();
        $classes = $fixture->getClasses($this->getFixture('yaml/php.yml'));

        $class = $classes[0];
        $this->assertEquals(get_class($class), 'Document_Page');
        $this->assertEquals($class->getTitle(), 'Testdokument');
        $this->assertEquals($class->getParentId(), 1);
    }

}