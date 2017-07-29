<?php
use LLM\lib\PluginLoader;

//debugLog.txt
define("ENABLE_LOG",true);


PluginLoader::load('RouteApi',array('config'=>true,'router'=>true,'event'=>true));



