<?php

class DrivingRouter{

    function getToken(){
            $isValid = $this->__checkDataValid();
            if($isValid!==true){
                $this->__response(array(
                'error'=>$isValid,
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
        return true;
    }
    function getResult($token){
        $output = array(
            'status'=>ROUTE_API_STATUS_FAILURE,
            'error' =>ROUTE_API_ERROR_MSG_TOKEN_NOT_EXIST
        );
        if(isset($_SESSION[$token])){
        
            $output = $_SESSION[$token];
           
        }
        if($output['status']==ROUTE_API_STATUS_PROGRESS &&
            time() > ($output['timeStamp'] + PROGRESS_TIMEOUT_SECOND)
         ){
            $output = array(
                'status'=>ROUTE_API_STATUS_FAILURE,
                'error' =>ROUTE_API_ERROR_MSG_TIMEOUT
            );
        }
        unset($output['timeStamp']);
        $this->__response($output);


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