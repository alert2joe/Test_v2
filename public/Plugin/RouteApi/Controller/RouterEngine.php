<?php
namespace RouteApi\Controller;
use RouteApi\lib\directionsGoogle;
use LLM\lib\common;
use LLM\lib\curlAsync;

class RouterEngine{

    public $sid = null;
    public $UUID = null;

    function getResult($params){
		
        $this->__initParams($params);//return err msg if params invalid
		
        $points = $params['data'];
    
        list($urls,$destinationSet) = $this->__getAllUrl($points);
        
        $jsonResult = curlAsync::async_get_url($urls);
		
		$phpResult = $this->__json2phpResult($jsonResult,$points);//return err msg if params invalid
		
        $shotestRoute = $this->__getShotestRoute($phpResult);

        $shotestDestinationSet = $destinationSet[$shotestRoute['index']];
		
        $output = array(
            'status'=>ROUTE_API_STATUS_SUCCESS,
            'path'=>$this->__getPath($shotestDestinationSet,$shotestRoute['waypoint_order']),
            'total_distance'=>$shotestRoute['distance'],
            'total_time'=>($shotestRoute['duration'])*DRIVING_TIME_GOOGLE_OFFSET_RATE,
        );

        $this->__updateTokenResult($output);

    }
	private function __json2phpResult($jsonResult,$points){
		$phpArray = [];
		
		if(count($jsonResult) != count($points)-1){
            $this->__updateTokenResult(array(
                'status'=>ROUTE_API_STATUS_FAILURE,
                'error' =>ROUTE_API_ERROR_MSG_RETURN_ERROR,
                ),$jsonResult);
        }
		
		foreach($jsonResult as $k=>$v){
			$v = $this->__getParseJson($v); //return err msg if params invalid
			if($k==0){
                $this->__checkWaypointStatus($v['geocoded_waypoints']); //return err msg if params invalid
            }
			$phpArray[$k]=$v;
		}
		return $phpArray;
	}
	
    private function __getShotestRoute($result){
            $shotestRoute = [];
            $tmpDuration = 9999999999999;
            foreach($result as $k=>$v){
             
                list($duration,$distance) = $this->__getTotalDurationDistance($v['routes'][0]['legs']);
				//find shotest
                if($duration < $tmpDuration){
                    $shotestRoute['waypoint_order'] = $v['routes'][0]['waypoint_order'];
                    $shotestRoute['distance'] = $distance;
                    $shotestRoute['duration'] = $duration;
                    $shotestRoute['index']  = $k;
                }
                $tmpDuration = $duration;
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
            if(isset($wp['geocoder_status'])==false || $wp['geocoder_status'] !='OK'){
                $this->__updateTokenResult(array(
                        'status'=>ROUTE_API_STATUS_FAILURE,
                        'error' =>ROUTE_API_ERROR_MSG_WAYPOINT_INVAILD,
                ),$wp);
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
        $destinationSet = [];
        $directionsGoogle = new directionsGoogle();
        foreach($points as $k=>$v){
			//loop every wayPoints as destination
           $destination = $v;
           $wayPoints = $points;
           array_splice($wayPoints, $k, 1);
           $destinationSet[]=array(
               'origin'=>$origin,
               'destination'=>$destination,
               'wayPoints'=>$wayPoints,
           );

           $curlList[] = $directionsGoogle->getApiUrl($origin,$destination,$wayPoints);
        }
       
        return  array($curlList,$destinationSet) ;

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
			EventEmitter::emit('RouteApi.track_failure', [$data,$errorLog]);
        }
        common::sessionStartById($this->sid);
        $_SESSION[$this->UUID] = $data;
        exit();
    }





  

}