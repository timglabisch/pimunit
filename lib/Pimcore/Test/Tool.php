<?

class Pimcore_Test_Tool
{
    public static function getSoapClient()
    {
        ini_set("soap.wsdl_cache_enabled", "0");
        $conf = Zend_Registry::get("pimcore_config_system");

        $user = User::getByName('soap');

        if(!$user)
        {
            $user = User::create(array(
                "parentId" => 0,
                "username" => "admin",
                "password" => Pimcore_Tool_Authentication::getPasswordHash("admin", "admin"),
                "hasCredentials" => true,
                "active" => true
            ));
            $user->setAdmin(true);
            $user->save();
        }

        if (!$user instanceof User) {
            throw new Exception("invalid user id");
        }

        $client = new Zend_Soap_Client($conf->webservice->wsdl . "&username=" . $user->getUsername() . "&apikey=" . $user->getPassword(), array(
                                                                                                                                               "cache_wsdl" => false,
                                                                                                                                               "soap_version" => SOAP_1_2,
                                                                                                                                               "classmap" => Webservice_Tool::createClassMappings()
                                                                                                                                          ));

        $client->setLocation($conf->webservice->serviceEndpoint . "?username=" . $user->getUsername() . "&apikey=" . $user->getPassword());
        return $client;
    }


    /**
     * @static
     * @param  array $properties
     * @return array
     */
    protected static function createPropertiesComparisonString($properties)
    {
        $propertiesStringArray = array();
        ksort($properties);
        if (is_array($properties)) {

            foreach ($properties as $key => $value) {

                if ($value->type == "asset" || $value->type == "object" || $value->type == "document") {
                    if ($value->data instanceof Element_Interface) {
                        $propertiesStringArray["property_" . $key . "_" . $value->type] = "property_" . $key . "_" . $value->type . ":" . $value->data->getId();
                    } else {
                        $propertiesStringArray["property_" . $key . "_" . $value->type] = "property_" . $key . "_" . $value->type . ": null";
                    }

                } else if ($value->type == 'date') {
                    if ($value->data instanceof Zend_Date) {
                        $propertiesStringArray["property_" . $key . "_" . $value->type] = "property_" . $key . "_" . $value->type . ":" . $value->data->getTimestamp();
                    }
                } else if ($value->type == "bool") {
                    $propertiesStringArray["property_" . $key . "_" . $value->type] = "property_" . $key . "_" . $value->type . ":" . (bool)$value->data;
                } else if ($value->type == "text" || $value->type == "select") {
                    $propertiesStringArray["property_" . $key . "_" . $value->type] = "property_" . $key . "_" . $value->type . ":" . $value->data;
                } else {
                    throw new Exception("Unknow property of type [ " . $value->type . " ]");
                }
            }

        }
        return $propertiesStringArray;
    }


    /**
     * @param  Asset $asset
     * @return string
     */
    protected static function createAssetComparisonString($asset, $ignoreCopyDifferences = false)
    {

        if ($asset instanceof Asset) {

            $a = array();

            //custom settings
            if(is_array($asset->getCustomSettings())){
                $a["customSettings"] = serialize($asset->getCustomSettings());
            }

            if ($asset->getData()) {
                $a["data"] = base64_encode($asset->getData());
            }

            if (!$ignoreCopyDifferences) {
                $a["filename"] = $asset->getFilename();
                $a["id"] = $asset->getId();
                $a["modification"] = $asset->getModificationDate();
                $a["creation"] = $asset->getCreationDate();
                $a["userModified"] = $asset->getUserModification();
                $a["parentId"] = $asset->getParentId();
                $a["path"] = $asset->getPath;
            }


            $a["userOwner"] = $asset->getUserOwner();




            $properties = $asset->getProperties();
            $a = array_merge($a, self::createPropertiesComparisonString($properties));

            return implode(",", $a);
        } else return null;
    }

    /**
     * @param  Asset $asset1
     * @param  Asset $asset2
     * @return bool
     */
    public static function assetsAreEqual($asset1, $asset2, $ignoreCopyDifferences = false)
    {

        if ($asset1 instanceof Asset and $asset2 instanceof Asset) {

            $a1Hash = self::createAssetComparisonString($asset1, $ignoreCopyDifferences);
            $a2Hash = self::createAssetComparisonString($asset2, $ignoreCopyDifferences);


            $id = uniqid();
            /*
            $myFile = TESTS_PATH . "/output/asset1-" . $id . ".txt";
            $fh = fopen($myFile, 'w');
            fwrite($fh, $a1Hash);
            fclose($fh);

            $myFile = TESTS_PATH . "/output/asset2-" . $id . ".txt";
            $fh = fopen($myFile, 'w');
            fwrite($fh, $a2Hash);
            fclose($fh);
       */

            return $a1Hash === $a2Hash ? true : false;

        } else return false;


    }

    /**
     * @param  Document $document
     * @return string
     */
    protected static function createDocumentComparisonString($document, $ignoreCopyDifferences = false)
    {
        if ($document instanceof Document) {

            $d = array();

            if ($document instanceof Document_PageSnippet) {
                $elements = $document->getElements();
                ksort($elements);
                foreach ($elements as $key => $value) {
                    if ($value instanceof Document_Tag_Video) {
                        //with video can't use frontend(), it includes random id
                        $d["element_" . $key] = $value->getName() . ":" . $value->type . "_" . $value->id;
                    } else if (!$value instanceof Document_Tag_Block) {
                        $d["element_" . $key] = $value->getName() . ":" . $value->frontend();
                    } else {
                        $d["element_" . $key] = $value->getName();
                    }
                }

                if ($document instanceof Document_Page) {
                    $d["name"] = $document->getName();
                    $d["keywords"] = $document->getKeywords();
                    $d["title"] = $document->getTitle();
                    $d["description"] = $document->getDescription();
                }

                $d["published"] = $document->isPublished();
            }

            if ($document instanceof Document_Link) {
                $d['link'] = $document->getHtml();
            }

            if (!$ignoreCopyDifferences) {
                $d["key"] = $document->getKey();
                $d["id"] = $document->getId();
                $d["modification"] = $document->getModificationDate();
                $d["creation"] = $document->getCreationDate();
                $d["userModified"] = $document->getUserModification();
                $d["parentId"] = $document->getParentId();
                $d["path"] = $document->getPath();
            }

           # $d["userOwner"] = $document->getUserOwner();

            $properties = $document->getProperties();
            $d = array_merge($d, self::createPropertiesComparisonString($properties));

            return implode(",", $d);
        } else return null;
    }

    /**
     * @param  Document $doc1
     * @param  Document $doc2
     * @return bool
     */
    public static function documentsAreEqual($doc1, $doc2, $ignoreCopyDifferences = false)
    {

        if ($doc1 instanceof Document and $doc2 instanceof Document) {

            $d1Hash = self::createDocumentComparisonString($doc1, $ignoreCopyDifferences);
            $d2Hash = self::createDocumentComparisonString($doc2, $ignoreCopyDifferences);

/*            $id = uniqid();


            $myFile = TESTS_PATH . "/output/document1-" . $id . ".txt";
            $fh = fopen($myFile, 'w');
            fwrite($fh, $d1Hash);
            fclose($fh);

            $myFile = TESTS_PATH . "/output/document2-" . $id . ".txt";
            $fh = fopen($myFile, 'w');
            fwrite($fh, $d2Hash);
            fclose($fh);
  */
            return $d1Hash === $d2Hash ? true : false;

        } else return false;


    }


    protected static function getComparisonDataForField($key, $value, $object)
    {

        // omit password, this one we don't get through WS,
        // omit non owner objects, they don't get through WS,
        // plus omit fields which don't have get method
        $getter = "get" . ucfirst($key);
        if (method_exists($object, $getter) and $value instanceof Object_Class_Data_Fieldcollections) {

            if ($object->$getter()) {

                $collection = $object->$getter();
                $items = $collection->getItems();
                if (is_array($items)) {

                    $returnValue = array();
                    $counter = 0;
                    foreach ($items as $item) {
                        $def = $item->getDefinition();

                        foreach ($def->getFieldDefinitions() as $k => $v) {
                            $getter = "get" . ucfirst($v->getName());
                            $fieldValue = $item->$getter();

                            if ($v instanceof Object_Class_Data_Link) {
                                $fieldValue = serialize($v);
                            } else if ($v instanceof Object_Class_Data_Password or $value instanceof Object_Class_Data_Nonownerobjects) {
                                $fieldValue=null;
                            } else {
                                $fieldValue = $v->getForCsvExport($item);
                            }

                            $returnValue[$counter][$k] = $fieldValue;
                        }
                        $counter++;


                    }
                    return serialize($returnValue);
                }

            }
        } else if (method_exists($object, $getter) and $value instanceof Object_Class_Data_Link) {
            return serialize($value);
        } else if (method_exists($object, $getter) and !$value instanceof Object_Class_Data_Password and !$value instanceof Object_Class_Data_Nonownerobjects) {
            return $value->getForCsvExport($object);
        }
    }

    /**
     * @param  Object_Abstract $object
     * @return string
     */
    protected static function createObjectComparisonString($object, $ignoreCopyDifferences)
    {

        if ($object instanceof Object_Abstract) {


            $o = array();

            if ($object instanceof Object_Concrete) {

                foreach ($object->getClass()->getFieldDefinitions() as $key => $value) {

                    $o[$key] = self::getComparisonDataForField($key, $value, $object);

                }


                $o["published"] = $object->isPublished();
            }
            if (!$ignoreCopyDifferences) {
                $o["id"] = $object->getId();
                $o["key"] = $object->getKey();
                $o["modification"] = $object->getModificationDate();
                $o["creation"] = $object->getCreationDate();
                $o["userModified"] = $object->getUserModification();
                $o["parentId"] = $object->getParentId();
                $o["path"] = $object->getPath;
            }


            $o["userOwner"] = $object->getUserOwner();


            $properties = $object->getProperties();
            $o = array_merge($o, self::createPropertiesComparisonString($properties));

            return implode(",", $o);
        } else return null;
    }

    public static function objectsAreEqual($object1, $object2, $ignoreCopyDifferences)
    {

        if ($object1 instanceof Object_Abstract and $object2 instanceof Object_Abstract) {


            $o1Hash = self::createObjectComparisonString($object1, $ignoreCopyDifferences);
            $o2Hash = self::createObjectComparisonString($object2, $ignoreCopyDifferences);

            $id = uniqid();

/*
            $myFile = TESTS_PATH . "/output/object1-" . $id . ".txt";
            $fh = fopen($myFile, 'w') or die("can't open file");
            fwrite($fh, $o1Hash);
            fclose($fh);

            $myFile = TESTS_PATH . "/output/object2-" . $id . ".txt";
            $fh = fopen($myFile, 'w') or die("can't open file");
            fwrite($fh, $o2Hash);
            fclose($fh);
*/

            return $o1Hash === $o2Hash ? true : false;

        } else return false;


    }

    /**
     * resets the registry
     * @static
     * @return void
     */
    public static function resetRegistry()
    {
        Zend_Registry::_unsetInstance();
        Pimcore::initConfiguration();
        Pimcore::initPlugins();
    }

}