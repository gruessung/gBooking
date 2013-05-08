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
                                 
