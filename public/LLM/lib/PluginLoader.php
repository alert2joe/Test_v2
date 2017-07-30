<?php
namespace LLM\lib;

class PluginLoader{

    static $ClassLoader = null;
	static $pluginList = null;

    static function init($ClassLoader){
		$ps = self::$pluginList;
	
		foreach($ps as $k=>$v){
			$pluginPath 		= APP."Plugin".DS.$k;
			$pluginConfigPath 	= $pluginPath.DS."Config".DS;
			$paths = [
				'config' => $pluginConfigPath."config.php",
				'router' => $pluginConfigPath."router.php",
				'event'  => $pluginConfigPath."event.php"
			];
			foreach($paths as $key=>$path ){
				if(isset($v[$key]) && $v[$key]==true &&
					file_exists($path)
				){
				
					include($path);
				}
			}
	

			//$ClassLoader->addPrefix('', $pluginPath);
		}
        
    }
    static function load($plugiName,$config){
		
		self::$pluginList[$plugiName]=$config;
    }



}