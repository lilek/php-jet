<?php
namespace Jet;
require "bootstrap_cli.php";

if(!isset($argv[1])) {
    die("Usage: {$argv[0]} ModuleName".PHP_EOL );
}

$module_name = $argv[1];

if(!Application_Modules::checkModuleNameFormat($module_name)) {
        echo "'{$module_name}' is not valid module name ([a-zA-Z0-9]{3,50}) ".PHP_EOL.PHP_EOL;
	exit(10);
}

if (!Application_Modules::getModuleExists($module_name) ) {
        echo "Module '{$module_name}' doesn't exist ".PHP_EOL.PHP_EOL;
	exit(20);
}

function handleException( Exception $e, $error_code=100 ) {
	echo "ERROR".PHP_EOL;
	echo $e->getMessage();
	echo PHP_EOL.PHP_EOL;
	exit($error_code);
}


function ok() {
	echo "OK".PHP_EOL.PHP_EOL;
	exit(0);
}

return $module_name;