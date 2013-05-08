    <?php
    	require_once("gSpreadsheet.php");
		$spread = new gSpreadsheet(spreadsheet);
		$worksheets = $spread->getWorksheets();
		
		if (cache)
		{
			if (!file_exists(sqlite))
			{
				//Create db structure
				$db = new SQLite3(sqlite);
				$db->exec("CREATE TABLE cells (worksheetID STRING, name STRING, value STRING)");
				$db->exec("CREATE TABLE worksheets (kurz STRING, url STRING, id STRING, date STRING)");
				$db->close();
			}
			//$db = new SQLite3(sqlite);
			
		}
		
		$db = new SQLite3(sqlite);
		//array with all lat
		$lat = array();
		//array with all lng
		$lng = array();
		//array with all description text
		$text = array();
		
		foreach ($worksheets as $worksheet)
		{
			$db->exec("INSERT INTO worksheets(kurz, url, id, date) VALUES ('$worksheet[0]', '$worksheet[1]', '$worksheet[2]','$worksheet[3]')");
			
			$cellsarray = $spread->loadCells($worksheet[2]);
			
			foreach($cellsarray as $cells)
			{
				$coord = explode(",", $cells[4]);
				$bez = $cells[0];
				$str = $cells[1];
				$plz = $cells[2];
				$ort = $cells[3];
				
				$desc = "<b>$bez</b><br>$str<br>$plz $ort";
				array_push($text, $desc);
				
				array_push($lat, $coord[1]);
				array_push($lng, $coord[0]);
				
			}
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
		
	?>
    
    <script type="text/javascript">
      
	var map;

	function initialize() {
	  var mapOptions = { center: new google.maps.LatLng(50.978056,11.029167), 
	                     zoom: 6,
	                     mapTypeId: google.maps.MapTypeId.ROADMAP };
	  map = new google.maps.Map(document.getElementById("gmap"), mapOptions);
	  

	  
	  var markers = [];
	
	  var infowindow = new google.maps.InfoWindow();
	
	  for (var i = 0; i < arraylng.length; i++) {
	    var marker = new google.maps.Marker({
	      position: new google.maps.LatLng(arraylng[i], arraylat[i]),
	      map: map
	    });
	
	    makeInfoWindowEvent(map, infowindow, arrayText[i], marker);
	    
	    markers.push(marker);
	  }
	}
	
	function makeInfoWindowEvent(map, infowindow, contentString, marker) {
	  google.maps.event.addListener(marker, 'click', function() {
	    infowindow.setContent(arrayText[i]);
	    infowindow.open(map, marker);
	  });
	  
	  $("#status").html("Karte geladen");
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