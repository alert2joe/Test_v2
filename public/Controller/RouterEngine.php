<?php

class RouterEngine{

    public $sid = null;
    public $UUID = null;

    function getResult($params){

        $this->__initParams($params);
        $points = $params['data'];
    
        list($urls,$routePoints) = $this->__getAllUrl($points);
        
        $jsonResult = curlAsync::async_get_url($urls);
        //include(APP."dummy.php");
        //$jsonResult = $dummy;
        if(count($jsonResult) != count($points)-1){
            $this->__updateTokenResult(array(
                'status'=>ROUTE_API_STATUS_FAILURE,
                'error' =>ROUTE_API_ERROR_MSG_RETURN_ERROR,
                ),$firstRow);
        }
        //common::log($jsonResult);
    
        $shotestRoute = $this->__handleResult($jsonResult,$routePoints);

       
        $shotestRoutePoints = $routePoints[$shotestRoute['index']];
        $output = array(
            'status'=>ROUTE_API_STATUS_SUCCESS,
            'path'=>$this->__getPath($shotestRoutePoints,$shotestRoute['waypoint_order']),
            'total_distance'=>$shotestRoute['distance'],
            'total_time'=>($shotestRoute['duration'])*DRIVING_TIME_GOOGLE_OFFSET_RATE,
        );

        $this->__updateTokenResult($output);

    }
    private function __handleResult($jsonResult){
            $shotestRoute = [];
            $tmpDuration = 9999999999999;
            foreach($jsonResult as $k=>$v){
                $v = $this->__getParseJson($v);
                if($k==0){
                    // return error msg if not ok
                    $this->__checkWaypointStatus($v['geocoded_waypoints']);
                }
                list($duration,$distance) = $this->__getTotalDurationDistance($v['routes'][0]['legs']);
                if($duration < $tmpDuration){
                    $shotestRoute['waypoint_order'] = $v['routes'][0]['waypoint_order'];
                    $shotestRoute['distance'] = $distance;
                    $shotestRoute['duration'] = $duration;
                    $shotestRoute['index']  = $k;
                }
                $tmpDuration = $totalValue['duration'];
            }

            return $shotestRoute;
            
    }

    private function __getTotalDurationDistance($legs){

        $distance = 0;
        $duration = 0;
        foreach($legs as $leg){
           $duration +=$leg["duration"]["value"];
           $distance +=$leg["distance"]["value"];
        } 
        return array($duration,$distance);
    }

    private function __getParseJson($v){
        if(isJson($v)==false){
            $this->__updateTokenResult(array(
            'status'=>ROUTE_API_STATUS_FAILURE,
            'error' =>ROUTE_API_ERROR_MSG_RETURN_ERROR,
            ));
        }
        return json_decode($v,1);
    }

    private function __checkWaypointStatus($geocodedWaypoints){
        foreach($geocodedWaypoints as $wp){
            if($wp['geocoder_status'] !='OK'){
                $this->__updateTokenResult(array(
                        'status'=>ROUTE_API_STATUS_FAILURE,
                        'error' =>ROUTE_API_ERROR_MSG_WAYPOINT_INVAILD,
                ));
            }
        } 
    }
    private function __getPath($routePoints,$waypoint_order){
       
        $path =[$routePoints['origin']];
        
        foreach($waypoint_order as $v){
            $path[]= $routePoints['wayPoints'][$v];
        }

        $path[]=$routePoints['destination'];
        return $path;

    }

    private function __getAllUrl($points){
        $origin = $points[0];
        array_shift($points);

        $curlList = [];
        $pointList = [];
        $directionsGoogle = new directionsGoogle();
        foreach($points as $k=>$v){
           $destination = $v;
           $wayPoints = $points;
           array_splice($wayPoints, $k, 1);
           $pointList[]=array(
               'origin'=>$origin,
               'destination'=>$destination,
               'wayPoints'=>$wayPoints,
           );

           $curlList[] = $directionsGoogle->getApiUrl($origin,$destination,$wayPoints);
        }
       
        return  array($curlList,$pointList) ;

    }

    private function __initParams($params){
        if(isset($params['UUID'])==false ||
            isset($params['session_id'])==false ||
            isset($params['data'])==false ){
             
            $this->__updateTokenResult(array(
                'status'=>ROUTE_API_STATUS_FAILURE,
                'error' =>ROUTE_API_ERROR_MSG_PARAMS_INVAILD
            ),$params);    
        }
        
        $this->UUID = $params['UUID'];
        $this->sid = $params['session_id'];
    }


    private function __updateTokenResult($data,$errorLog = false){
 
        if($errorLog){
            common::log($data);
            common::log($errorLog);
        }
        common::sessionStartById($this->sid);
        $_SESSION[$this->UUID] = $data;
        exit();
    }





  

}