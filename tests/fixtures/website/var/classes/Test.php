<?php 

class Object_Test extends Object_Concrete {

public $o_classId = 1;
public $o_className = "test";
public $inputField;


/**
* @param array $values
* @return Object_Test
*/
public static function create($values = array()) {
	$object = new self();
	$object->setValues($values);
	return $object;
}

/**
* @return string
*/
public function getInputField () {
	$preValue = $this->preGetValue("inputField"); 
	if($preValue !== null && !Pimcore::inAdmin()) { return $preValue;}
	$data = $this->inputField;
	 return $data;
}

/**
* @param string $inputField
* @return void
*/
public function setInputField ($inputField) {
	$this->inputField = $inputField;
}

protected static $_relationFields = array (
);

public $lazyLoadedFields = NULL;

}

