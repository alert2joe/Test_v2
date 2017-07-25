<?php

class common{

    static $request = null;
    static function log($t){
        if(!ENABLE_LOG){
            return false;
        }
        $fp = fopen(DS."application".DS."debugLog.txt", "a");
        $a=print_r($t,1);
        fwrite($fp, "Start ".date("Y-m-d H:i:s")." \n");
        fwrite($fp,$a." \n");
        fclose($fp);
    }
    static function genUUID(){
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
                mt_rand( 0, 0xffff ),
                mt_rand( 0, 0x0fff ) | 0x4000,
                mt_rand( 0, 0x3fff ) | 0x8000,
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
            );
    }

    static function callPhpAsynchronous($path,$params=array()){
         $paramsJson = urlencode(json_encode($params));
         
         $path = urlencode($path);
         $fp= popen("php ".APP."cli.php $path $paramsJson > /dev/null &","r");


    }

    static function Dispatcher(){
       self::_processRequest();
       appRouter::apply();
    }  
 


    static private function _processRequest(){
        $getData = $_GET;
        $cliData = null;
        $postData = null;
        if(php_sapi_name() ==='cli' && isset($_SERVER['argv'])){
            $cliData = json_decode(urldecode($_SERVER['argv'][2]),1);
        }
        if ($_POST) {
            $postData = $_POST;
        }
        if (ini_get('magic_quotes_gpc') === '1') {
			$postData = stripslashes_deep($PostData);
            $getData = stripslashes_deep($_GET);
            $cliData =  stripslashes_deep($cliData);
		}
        self::$request = array(
            'post'=>$postData,
            'get'=>$getData,
            'cli'=>$cliData,
        );
      
    }


    static function sessionStartById($session_id){ 
        session_id($session_id);
        session_start();
    }  
    
    static function isValidLongitude($longitude){
        if(preg_match("/^-?([1]?[1-7][1-9]|[1]?[1-8][0]|[1-9]?[0-9])\.{1}\d{1,20}$/",
        $longitude)) {
        return true;
        } else {
        return false;
        }
    }

    static function isValidLatitude($latitude){
        if (preg_match("/^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,20}$/", $latitude)) {
            return true;
        } else {
            return false;
        }
    }
}