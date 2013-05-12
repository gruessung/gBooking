    <?php
    	require_once("gSpreadsheet.php");
		$spread = new gSpreadsheet(spreadsheet);
		$worksheets = $spread->getWorksheets();
		
		

		if (!file_exists(sqlite))
		{
			//Create db structure
			$db = new SQLite3(sqlite);
			$db->exec("CREATE TABLE cells (worksheetID STRING, name STRING, value STRING)");
			$db->exec("CREATE TABLE worksheets (kurz STRING, url STRING, id STRING, date STRING)");
			$db->close();
		}
		
		$db = new SQLite3(sqlite);
		//array with all lat
		$lat = array();
		//array with all lng
		$lng = array();
		//array with all description text
		$text = array();
		//array marker image
		$marker = array();
		
		$update = false;
		
		//create date string
		if (!isset($_GET["date"]))
		{
			//today
			$date = date("n/j/Y",time());
		}
		else
		{
			$date = $_GET["date"];
			$expl = explode("/", $date);
			
			//if given date isn't numeric, fetch current date
			if (!is_numeric($expl[0]) OR !is_numeric($expl[1]) OR !is_numeric($expl[2])) 
			{
				$date = date("n/j/Y", time());
			}
			else
			{
				//delete 0 in day and month
				$expl[0] = preg_replace("%0%", "", $expl[0]);
				$expl[1] = preg_replace("%0%", "", $expl[1]);
			
				$date = "$expl[0]/$expl[1]/$expl[2]";
			}
		}
		
		foreach ($worksheets as $worksheet)
		{
			//current worksheet id
			$wid = $worksheet[2];
			
			//if worksheet already exists in db?
			$result = $db->querySingle("SELECT kurz FROM worksheets WHERE `id` = '$worksheet[2]'");
			if ($result == null)
			{
				//insert new worksheet
				$db->exec("INSERT INTO worksheets(kurz, url, id, date) VALUES ('$worksheet[0]', '$worksheet[1]', '$worksheet[2]','$worksheet[3]')");
				$update = true;
			}
			else
			{
				$result = $db->querySingle("SELECT date FROM worksheets WHERE `id` = '$worksheet[2]'");
				
				//worksheet date is older than last updated date in ods
				if (strcmp($result, $worksheet[3]) != 0)
				{
					$db->exec("UPDATE worksheets SET `date`='$worksheet[3]' WHERE `id` = '$worksheet[2]'");
					$update = true;
				}	
			}
			
			
		
			//get coordinates, name, street, postal code and town of all entries
			$coord = explode(",",$db->querySingle("SELECT value FROM cells WHERE `worksheetID` = '$worksheet[2]' AND `name`='Lage:' "));
			$bez = $db->querySingle("SELECT value FROM cells WHERE `worksheetID` = '$worksheet[2]' AND `name`='Bezeichnung:' ");
			$str = $db->querySingle("SELECT value FROM cells WHERE `worksheetID` = '$worksheet[2]' AND `name`='Straße:' ");
			$plz = $db->querySingle("SELECT value FROM cells WHERE `worksheetID` = '$worksheet[2]' AND `name`='PLZ:' ");
			$ort = $db->querySingle("SELECT value FROM cells WHERE `worksheetID` = '$worksheet[2]' AND `name`='Ort:' ");
			
			//set description
			$desc = "<b>$bez</b><br>$str<br>$plz $ort";
			
			//push in arrays
			array_push($text, $desc);
			array_push($lat, @$coord[1]);
			array_push($lng, @$coord[0]);
			
			//check allocation
			$belegung = $db->querySingle("SELECT value FROM cells WHERE `worksheetID` = '$worksheet[2]' AND `name`='$date' ");
			//select marker color
			if (strcmp($belegung, "x") == 0)
				array_push($marker,"green");
			else
				array_push($marker,"red");

			
		}

		//update database
		if ($update)
		{
			echo '<br><br><center><h2>Bitte warten...</h2><small>Es gab eine &Auml;nderung in der ODS Datei.<br>Bef&uuml;lle Datenbank mit neuen Daten</small><meta http-equiv="refresh" content="2; URL=index.php?action=updateDB"></center>';
			die();
		}
		
		//wrote all lat, lng, text in javascript
		
		//LAT
		echo "<script>var arraylat = [";
		for ($i = 0; $i < count($lat) -1 ; $i++)
		{
			echo $lat[$i].",";
		}
		echo "]; </script>";
		
		//LNG
		echo "<script>var arraylng = [";
		for ($i = 0; $i < count($lng) -1 ; $i++)
		{
			echo $lng[$i].",";
		}
		echo "]; </script>";
		
		//TEXT
		echo "<script>var arrayText = [";
		for ($i = 0; $i < count($text) -1 ; $i++)
		{
			echo "'".$text[$i]."',";
		}
		echo "]; </script>";
		
		//Marker
		echo "<script>var arrayMarker = [";
		for ($i = 0; $i < count($marker) -1 ; $i++)
		{
			echo "'".$marker[$i]."',";
		}
		echo "]; </script>";
	?>
    
    <script type="text/javascript">  
    
	var map;
	
	var pinColor = "01DF01";
    var pinImageGreen = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
        new google.maps.Size(21, 34),
        new google.maps.Point(0,0),
        new google.maps.Point(10, 34));
    var pinShadowGreen = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_shadow",
        new google.maps.Size(40, 37),
        new google.maps.Point(0, 0),
        new google.maps.Point(12, 35));
        
	var pinColor = "FE2E2E";
    var pinImageRed = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
        new google.maps.Size(21, 34),
        new google.maps.Point(0,0),
        new google.maps.Point(10, 34));
    var pinShadowRed = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_shadow",
        new google.maps.Size(40, 37),
        new google.maps.Point(0, 0),
        new google.maps.Point(12, 35));

	function initialize() {
	  var mapOptions = { center: new google.maps.LatLng(50.978056,11.029167), 
	                     zoom: 6,
	                     mapTypeId: google.maps.MapTypeId.ROADMAP };
	  map = new google.maps.Map(document.getElementById("gmap"), mapOptions);
	  

	  
	  var markers = [];
	
	  var infowindow = new google.maps.InfoWindow();
	
	  for (var i = 0; i < arraylng.length; i++) {
	  	if (arrayMarker[i] =="green")
	  	{
	  		makertmp = pinImageGreen;
	  	}
	  	else
	  	{
	  		makertmp = pinImageRed;
	  	}
	    var marker = new google.maps.Marker({
	      position: new google.maps.LatLng(arraylng[i], arraylat[i]),
	      icon: makertmp,
	      map: map
	    });
	
	    makeInfoWindowEvent(map, infowindow, arrayText[i], marker);
	    
	    markers.push(marker);
	  }
	}
	
	function makeInfoWindowEvent(map, infowindow, contentString, marker) {
	  google.maps.event.addListener(marker, 'click', function() {
	    infowindow.setContent(contentString);
	    infowindow.open(map, marker);
	  });
	}
	google.maps.event.addDomListener(window, 'load', initialize);
    </script>


	<style>
	#gmap 
	 	{
	 		margin: 0;
	        padding: 0;
	        height: 500px;
	    }
	 </style>
	 <div id="gmap">Loading map...</div>