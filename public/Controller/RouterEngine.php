<?php

class RouterEngine{

    public $sid = null;
    public $UUID = null;

    function getResult($params){
        // $params = array(
        //     'UUID'=>1,
        //     'session_id'=>1,
        //     'data'=>common::$request['post']['paths']
        // );
        $this->__initParams($params);
        $points = $params['data'];
    
        $urls = $this->__getAllUrl($points);
        
        $jsonResult = curlAsync::async_get_url($urls['urls']);
        //include(APP."dummy.php");
        //$jsonResult = $dummy;
        if(count($jsonResult) != count($points)-1){
            $this->__updateTokenResult(array(
                'status'=>ROUTE_API_STATUS_FAILURE,
                'error' =>ROUTE_API_ERROR_MSG_RETURN_ERROR,
                ),$firstRow);
        }
        common::log($jsonResult);
        $routePoints = $urls['points'];
        $shotestRoute = $this->__handleResult($jsonResult,$routePoints);

        $second_of_min = 60;
        $shotestRoutePoints = $routePoints[$shotestRoute['index']];
        $output = array(
            'status'=>ROUTE_API_STATUS_SUCCESS,
            'path'=>$this->__getPath($shotestRoutePoints,$shotestRoute['waypoint_order']),
            'total_distance'=>$shotestRoute['distance'],
            'total_time'=>($shotestRoute['duration']*$second_of_min)*DRIVING_TIME_GOOGLE_OFFSET_RATE,
        );

        $this->__updateTokenResult($output);

    }
    private function __handleResult($jsonResult){
            $shotestRouteResult = [];
            $tmpDuration = 99999999999999999999;
            foreach($jsonResult as $k=>$v){
                $v = $this->__getParseJson($v);
                if($k==0){
                    // not ok return error msg
                    $this->__checkWaypointStatus($v['geocoded_waypoints']);
                }
                $totalValue = $this->__getTotalDurationDistance($v['routes'][0]['legs']);
                if($totalValue['duration'] < $tmpDuration){
                    $shotestRouteResult = $v;
                    $shotestRouteResult['distance']=$totalValue['distance'];
                    $shotestRouteResult['duration']=$totalValue['duration'];
                    $shotestRouteResult['index']  = $k;
                }
                $tmpDuration = $totalValue['duration'];
            }

            $shotestRoute['waypoint_order']  = $shotestRouteResult['routes'][0]['waypoint_order'];
            $shotestRoute['distance']  =  $shotestRouteResult['distance'];
            $shotestRoute['duration']  =  $shotestRouteResult['duration'];
            $shotestRoute['index']  = $shotestRouteResult['index'];
            return $shotestRoute;
            
    }

    private function __getTotalDurationDistance($legs){
        $res = array(
            'distance'=>0,
            'duration'=>0,
        );
        foreach($legs as $leg){
           $res['duration'] +=$leg["duration"]["value"];
           $res['distance'] +=$leg["distance"]["value"];
        } 
        return $res;
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
                        'error' =>ROUTE_API_ERROR_MSG_RETURN_ERROR,
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
        $curlListA = [];
        $directionsGoogle = new directionsGoogle();
        foreach($points as $k=>$v){
           $destination = $v;
           $wayPoints = $points;
           array_splice($wayPoints, $k, 1);
           $curlListA[]=array(
               'origin'=>$origin,
               'destination'=>$destination,
               'wayPoints'=>$wayPoints,
           );

           $curlList[] = $directionsGoogle->getApiUrl($origin,$destination,$wayPoints);
        }
       
        return  array('urls'=>$curlList,'points'=>$curlListA) ;

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