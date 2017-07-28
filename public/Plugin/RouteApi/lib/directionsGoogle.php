<?php
namespace RouteApi\lib;

class directionsGoogle{



    public function getApiUrl($origins,$destinations,$waypoints){
	
        $destinations =$this->__pt2txt($destinations);
        $origins =$this->__pt2txt($origins);
        $waypoints = \RouteApi\lib\Polyline::encode($waypoints);
        $waypoints = "enc:{$waypoints}:";
        
        return $this->__getGoogleApiURL($origins,$destinations,$waypoints);
  

    }

   private function __getGoogleApiURL($origins,$destinations,$waypoints){
        
        
        $data = array(
            'origin' => $origins,
            'destination' => $destinations,
            'waypoints'=>'optimize:true|'.$waypoints,
            'mode' => 'driving',
            'key' => GOOGLE_API_KEY
        );
        $url='https://maps.googleapis.com/maps/api/directions/json?';
        return $url.http_build_query($data);
      // return @file_get_contents($url.http_build_query($data));


   }


   private function __pt2txt_recursive($pts){
       $tmpPt = array();
       foreach($pts as $v){
            $tmpPt[]=$this->__pt2txt($v);

       }
       
        return implode('|',$tmpPt);
   }
   private function __pt2txt($pt){
        return implode(',',$pt);
   }
}