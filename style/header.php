<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <title>gBooking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="gBooking for CodingContest 5">
    <meta name="author" content="Alexander Gruessung">

	<!-- JavaScript files -->
	<script src="style/js/jquery.js"></script>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=<?=apikey?>&sensor=false"></script>
    <script type="text/javascript">
      
	var map;

	function initialize() {
	  var mapOptions = { center: new google.maps.LatLng(0.0, 0.0), 
	                     zoom: 2,
	                     mapTypeId: google.maps.MapTypeId.ROADMAP };
	  map = new google.maps.Map(document.getElementById("gmap"), mapOptions);
	  
	  var arraylng = [0, 0, 0, 0, 0];
	  var arraylat = [0, 10, 20, 30, 40];
	
	  var markers = [];
	
	  var infowindow = new google.maps.InfoWindow();
	
	  for (var i = 0; i < arraylng.length; i++) {
	    var marker = new google.maps.Marker({
	      position: new google.maps.LatLng(arraylng[i], arraylat[i]),
	      map: map
	    });
	
	    makeInfoWindowEvent(map, infowindow, "test" + i, marker);
	    
	    markers.push(marker);
	  }
	}
	
	function makeInfoWindowEvent(map, infowindow, contentString, marker) {
	  google.maps.event.addListener(marker, 'click', function() {
	    infowindow.setContent(contentString);
	    infowindow.open(map, marker);
	  });
	  
	  $("#status").html("Karte geladen");
	}
	google.maps.event.addDomListener(window, 'load', initialize);
    </script>


    <!-- CSS files -->
   <link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
   <style>
   	body
   	{
   		font-family: Roboto, Arial;
   	}
   </style>



  
  </head>

  <body>
   	<div id="header" style="height:50px;">
   		<center>
   			<span style="font-size: 20pt;">gBooking</span>
   			<span style="float:right;" id="status">Lade Daten...</span>	
   		</center>
   		
   	</div>  
   	<div id="content">
                                 
