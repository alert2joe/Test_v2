<?php

if(php_sapi_name() !=='cli'){
    header("HTTP/1.0 404 Not Found");
    exit();
}
if(isset($_SERVER['argv'][1]) == false){
    header("HTTP/1.0 404 Not Found");
    exit();
}


include("/application/public/Config/core.php");

 

 common::Dispatcher();
