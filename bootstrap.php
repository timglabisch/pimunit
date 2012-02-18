<?
use de\any\di;
use de\any\di\binder\parser\xml;

// load dependency injection
require_once __DIR__.'/lib/Any/di.php';
require_once __DIR__.'/lib/Any/DI/binder/parser/xml.php';

require_once __DIR__.'/lib/Pimunit/iStartup.php';
require_once __DIR__.'/lib/Pimunit/Startup/Standard.php';
require_once __DIR__.'/lib/Pimunit/Startup/iConstants.php';
require_once __DIR__.'/lib/Pimunit/Startup/Constants/Standard.php';

$xml = new xml(file_get_contents(__DIR__.'/dependencies.xml'));

$repository = new \de\any\di\binder\repository();
$repository->addBindings($xml->getBindings());

/*
it is very important to unset the $xml global, if
PHPUnit is configured to backup Globals, you will
have trouble by serializing the SimpleXmlElement
*/
unset($xml);

$di = new di();

$di->setBinderRepository($repository);
$di->bind('\de\any\iDi')->to($di);

// init Pimcore
require_once __DIR__.'/lib/Pimunit/iBootstrap.php';
require_once __DIR__.'/lib/Pimunit/Bootstrap/Bootstrap.php';
$di->get('Pimunit_iBootstrap')->run(__DIR__);

Pimcore_Test_Case::$di = $di->get('\de\any\iDi');