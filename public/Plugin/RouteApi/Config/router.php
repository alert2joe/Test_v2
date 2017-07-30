<?php
use LLM\lib\appRouter;
use LLM\lib\common;
 appRouter::add('/^\/route$/',function($uri){

        $api = new RouteApi\Controller\DrivingRouter();
        $api->getToken();
        exit();
  
 });



 appRouter::add('/^\/route\/.+$/',function($uri){
    
        $api = new RouteApi\Controller\DrivingRouter();
        $api->getResult($uri[1]);
        exit();
    
 });



  appRouter::addCli('/^\/RouterEngine$/',function($uri){

    $prarms = common::$request['cli'];
 
    $api = new RouteApi\Controller\RouterEngine();

    $api->getResult($prarms);

    exit();
    
 });

