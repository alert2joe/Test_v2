<?php
namespace RouteApi\Controller;
use LLM\lib\common;
use Evenement\EventEmitter;
class DrivingRouter{

    function getToken(){

            $errorMsg = $this->__checkDataValid();
            if($errorMsg!==false){
                $this->__response(array(
                'error'=>$errorMsg,
                ));
            }

            $data = common::$request['post']['paths'];
            $token = common::genUUID();
            $path = "/RouterEngine";
            $params = array(
                'session_id'=>session_id(),
                'UUID'=>$token,
                'data'=>$data,
            );
			
			EventEmitter::emit('RouteApi.getToken', [$params]);

            $_SESSION[$params['UUID']] = array('status'=>ROUTE_API_STATUS_PROGRESS,'timeStamp'=>time());

            common::callPhpAsynchronous($path,$params);
             $this->__response(array(
                'token'=>$token,
            ));

    }
    private function __checkDataValid(){

        if($this->robotPrevent()==false){
            return GET_TOKEN_ERROR_MSG_ROBOT_CHECK;
                
        }
		$php_input = file_get_contents('php://input');
		
		if($php_input && isJson($php_input)){
			common::$request['post']['paths'] = json_decode($php_input,1);
		}
		
        $r= common::$request;

        if(isset($r['post'])==false ||
             isset($r['post']['paths'])==false ||
             is_array($r['post']['paths']) == false
         ){
             return GET_TOKEN_ERROR_MSG_NO_WAYPOINT;
        }
        if(count($r['post']['paths'])<2){
                 return GET_TOKEN_ERROR_MSG_WAYPOINT_MIN;
        }
        if(count($r['post']['paths'])>WAYPOINT_MAX){
                 return GET_TOKEN_ERROR_MSG_OVER_WAYPOINT_MAX;
        }
 
        foreach($r['post']['paths'] as $v){
            if(is_array($v)==false || count($v)!=2){
                return GET_TOKEN_ERROR_MSG_WAYPOINT_FORMAT_ERROR;  
            }
            if(common::isValidLatitude($v[0]) == false){
                return GET_TOKEN_ERROR_MSG_WAYPOINT_FORMAT_ERROR;
            }
            if(common::isValidLongitude($v[1]) == false){
                return GET_TOKEN_ERROR_MSG_WAYPOINT_FORMAT_ERROR;
            }
        }
        return false;
    }
    function getResult($token){
		$this->__getResultCheckValue($token);//return err msg if params invalid
        $output = $_SESSION[$token];
        unset($output['timeStamp']);
        $this->__response($output);
    }
	
	private function __getResultCheckValue($token){
		
		if($this->robotPrevent()==false){
			$output = array(
				'status'=>ROUTE_API_STATUS_FAILURE,
				'error' =>GET_TOKEN_ERROR_MSG_ROBOT_CHECK
			);
			$this->__response($output);
		}
		
        if(isset($_SESSION[$token])==false){
			$output = array(
				'status'=>ROUTE_API_STATUS_FAILURE,
				'error' =>ROUTE_API_ERROR_MSG_TOKEN_NOT_EXIST
			);
			$this->__response($output);
           
        }
        if($_SESSION[$token]['status']==ROUTE_API_STATUS_PROGRESS &&
            time() > ($_SESSION[$token]['timeStamp'] + PROGRESS_TIMEOUT_SECOND)
         ){
            $output = array(
                'status'=>ROUTE_API_STATUS_FAILURE,
                'error' =>ROUTE_API_ERROR_MSG_TIMEOUT
            );
			$this->__response($output);
        }
		
		return true;
    }
	
	
	

    private function __response($output){

       header('Content-Type: application/json');
       echo json_encode($output);
       exit();
    }


    function robotPrevent(){
        return true;
    }

}