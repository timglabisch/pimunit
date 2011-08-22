<?php

abstract class Pimcore_Test_Setup_Fixture_Abstract {

    public function getClasses($filename)
    {
        $yaml = new sfYamlParser();
        $yaml = $yaml->parse(file_get_contents($filename));


        $buffer = array();

        foreach($yaml as $classes)
            foreach($classes as $classname => $value)
            {
                $tmpClass =  new $classname;

                foreach($value as $method => $value)
                {
                    $setter = 'set'.ucfirst($method);
                    $tmpClass->$setter($value);
                }

                $buffer[] = $tmpClass;
            }

        return $buffer;
    }

    public function getClassesAndSave($filename)
    {
        $classes = $this->getClasses($filename);

        if(!count($classes))
            return;

        foreach($classes as $class)
            $class->save();
    }

}
