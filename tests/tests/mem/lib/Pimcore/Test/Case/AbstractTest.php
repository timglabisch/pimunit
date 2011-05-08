<?php

class Pimcore_Test_Case_AbstractTest extends Pimcore_Test_Case_Mem {

    /**
     * @return Pimcore_Test_Case_Abstract
     */
    private function getObject()
    {
        return $this->getMockForAbstractClass('Pimcore_Test_Case_Abstract');
    }

    /**
     * @group memory
     */
    public function testGetFixture()
    {
        $content = file_get_contents($this->getObject()->getFixture('where_i_am'));
        $this->assertEquals($content, 'pimunit');
    }

}