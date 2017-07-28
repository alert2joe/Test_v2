<?php
namespace LLM\lib;
class appRouter{

    static $routerList = array();
    static $cliRouterList = array();
    static function add($regCond,$fn){
    
        self::$routerList[]=array(
            'regCond'=>$regCond,
            'callback'=>$fn
        );
    }
    static function addCli($regCond,$fn){
    
        self::$cliRouterList[]=array(
            'regCond'=>$regCond,
            'callback'=>$fn
        );
    }
    static function apply(){
        if(php_sapi_name() =='cli'){
            $uri = trim(urldecode($_SERVER['argv'][1]));
            $routerList = self::$cliRouterList;
        }else{
           $uri = $_SERVER['REQUEST_URI'];
           $routerList = self::$routerList;
        }
        
        $paths = explode('/',$uri);
        $realPath = array();
        foreach($paths as $k=>$v){
            if($v){
            $realPath[] = $v;
            }
        }
		$routerFunction = null;
        foreach($routerList as $v){
            $preg =$v['regCond'];
     
            if(preg_match($preg,$uri)){
                $routerFunction = $v['callback'];
            }
        }
		if(is_null($routerFunction)){
			header("HTTP/1.0 404 Not Found");
			exit();
		}
		
		$routerFunction($realPath);
		exit();
       
    }



}