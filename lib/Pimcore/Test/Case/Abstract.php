<?
abstract class Pimcore_Test_Case_Abstract extends PHPUnit_Framework_TestCase {

    public function getFixture($path)
    {
        return PIMUNIT_ROOT.'/fixtures/'.$path;
    }

}
