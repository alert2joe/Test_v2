<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>DEMO</title>

 <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
       <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 500px;
         width: 500px;
         float:left;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #penal{
          background:#eee;
        float:left;
        width:500px;
         height: 500px;
         padding:10px;

      }
      textarea{
          font-size:12;
      }
    </style>
  </head>
  <body>

  <div id="map"></div>
<div id="penal">
Max marker :<?php echo WAYPOINT_MAX;?>
  <p>
  <button type="button" onclick ='clearOverlays()' class="btn btn-primary btn-xs">Remove all marker</button>
</p>

<textarea class="ajaxOutTa form-control" rows="8" placeholder='Click the map to create waypoint...' ></textarea>
  <p><br>
<button type="button" onclick ='getToken()' class="btn btn-primary btn-xs">Get token and start tracking</button>
<span class='getTokenResult'></span>
</p>
Auto track per second (<span class='autoTrackCount'>0</span>)
<textarea class="autoTrack form-control"  rows="8"></textarea>
</div>





<script>
    var markersArray=[];
    var ajaxOutArray=[];
    var counter =false;
    var autoTrackCount = 0;


   function initMap() {
        var uluru = {lat: 22.3468492, lng: 114.182174};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 13,
          center: uluru
        });

       


         map.addListener('click', function(e) {
            var marker = new google.maps.Marker({
          position: {lat:e.latLng.lat(), lng: e.latLng.lng()},
          map: map,
          label: (markersArray.length+1).toString(),
        });
         marker.addListener('click', function(e) {
            this.remove();
        });
        markersArray.push(marker);
        ajaxOutArray.push([e.latLng.lat(),e.latLng.lng()]);
        reflashTa();
            // map.setZoom(8);
            // map.setCenter(marker.getPosition());
            console.log(e.latLng.lat());
            console.log(e.latLng.lng());
        });
      }


      function reflashTa() {
        $('.ajaxOutTa').val(JSON.stringify(ajaxOutArray));


      }
  function clearOverlays() {
  for (var i = 0; i < markersArray.length; i++ ) {
    markersArray[i].setMap(null);
  }
  markersArray.length = 0;
  ajaxOutArray.length = 0;
  reflashTa();
}

 function getToken(){
   $('.getTokenResult').empty();
   $('.autoTrack').val('');
   
   var postData = JSON.parse($.trim($('.ajaxOutTa').val()));


$.ajax({
    dataType :'json',
  method: "POST",
  url: "/route",
  //data: '[[22.32308300997969,114.18691635131836],[22.317683823893706,114.17146682739258]]'
  data: {paths:postData}
})
  .done(function( msg ) {
    
    if(typeof msg.token != 'undefined' ){
        $('.getTokenResult').text('Token:'+msg.token);
        if(counter){
            clearInterval(counter);
        }
        autoTrackCount=0;
        autoTrackToken(msg.token);
        counter = setInterval(function(){ autoTrackToken(msg.token); }, 1000);
        return true
    }
    if(typeof msg.error != 'undefined' ){
        $('.getTokenResult').text('Error:'+msg.error);
        return true
    }
    
  });

 }

 function autoTrackToken(token){
        autoTrackCount++;
        $('.autoTrackCount').text(autoTrackCount);
        $.ajax({
            dataType :'json',
        method: "GET",
        url: "/route/"+token
        })
        .done(function( msg ) {
            $('.autoTrack').val(JSON.stringify(msg));
            if(typeof msg.status != 'undefined' &&  msg.status !='in progress'){
                clearInterval(counter);
            }
        });

 }


    </script>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->

<!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
     <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB4tnG1Bf4c27Yjp9FgvWy637-wU5NW0nc&callback=initMap">
    </script>
  </body>
</html>