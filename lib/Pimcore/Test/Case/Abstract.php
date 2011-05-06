<?
abstract class Pimcore_Test_Case_Abstract extends PHPUnit_Framework_TestCase {

    public function getFixture($path)
    {
        if(file_exists(getcwd().'/tests/fixtures/'.$path))
            return getcwd().'/tests/fixtures/'.$path;

        return getcwd().'/fixtures/'.$path;
    }

}
