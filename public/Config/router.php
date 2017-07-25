<?php

 appRouter::add('/^\/route$/',function($uri){

        $api = new DrivingRouter();
        $api->getToken();
        exit();
  
 });



 appRouter::add('/^\/route\/.+$/',function($uri){
    
        $api = new DrivingRouter();
        $api->getResult($uri[1]);
        exit();
    
 });


 appRouter::add('/^\/$/',function($uri){
        header("HTTP/1.0 404 Not Found");
        exit();
        
 });


 appRouter::add('/^\/demo$/',function($uri){
        include("demo.php");
 });




  appRouter::addCli('/^\/RouterEngine$/',function($uri){

    $prarms = common::$request['cli'];
 
    $api = new RouterEngine();

    $api->getResult($prarms);

    exit();
    
 });

  appRouter::add('/^\/testapi$/',function($uri){
    $api = new RouterEngine();
    $api->testapi();
    exit();
 });