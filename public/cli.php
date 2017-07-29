<?php
use LLM\lib\common;
if(php_sapi_name() !=='cli'){
    header("HTTP/1.0 404 Not Found");
    exit();
}
if(isset($_SERVER['argv'][1]) == false){
    header("HTTP/1.0 404 Not Found");
    exit();
}



include(dirname(__FILE__).DIRECTORY_SEPARATOR."Config".DIRECTORY_SEPARATOR."core.php");


common::Dispatcher();
