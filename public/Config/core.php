<?php
use LLM\lib\PluginLoader;
use Evenement\EventEmitter;
define("DS",DIRECTORY_SEPARATOR);

define("APP",dirname(dirname(__FILE__)).DS);
define("APPLLM",APP.'LLM'.DS);

include(APPLLM."lib".DS."ClassLoader.php");

$loader = new Symfony\Component\ClassLoader\ClassLoader();

$loader->addPrefix('', APP.'Plugin');
$loader->addPrefix('', APP.'LLM'.DS.'lib');
$loader->addPrefix('', APP);
$loader->register();

include(APP."Config".DS."config.php");

PluginLoader::init($loader);


	
	
// some global function 
 if (!function_exists('pr')) {
 function pr($t){
    echo '<pre>';
    print_r($t);
    echo '</pre>';
 }
}

 if (!function_exists('stripslashes_deep')) {
	function stripslashes_deep($values) {
		if (is_array($values)) {
			foreach ($values as $key => $value) {
				$values[$key] = stripslashes_deep($value);
			}
		} else {
			$values = stripslashes($values);
		}
		return $values;
	}

}
if (!function_exists('isJson')) {
	function isJson($string) {
	json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE);
	}
}

