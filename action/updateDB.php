<?php
	//import spreadsheet class
    require_once("gSpreadsheet.php");
	$spread = new gSpreadsheet(spreadsheet);
	
	//load all worksheets
	$worksheets = $spread->getWorksheets();
	
	//connect to db
	$db = new SQLite3(sqlite);
	
	if (!isset($_GET["next"]))
	{
		$next = 0;	
	}
	else 
	{
		$next = $_GET["next"];
	}
	if ($next >= count($worksheets) -1 )
	{
		echo '<meta http-equiv="refresh" content="1; URL=index.php">';
		die();
	}
	
	echo "<br><br><center>
			<h2>Bitte warten...</h2><small>Es gab eine &Auml;nderung in der ODS Datei.<br>
			Bef&uuml;lle Datenbank mit neuen Daten<br>
			". round((100/count($worksheets))*($next+1)) ."&#37;</small>";
	
	$sheet = $worksheets[$next];

	//get cells
	$cell = $spread->loadCells($sheet[2]);

	$db->exec("DELETE FROM cells WHERE `worksheetID` = '$sheet[2]'");
	for ($i = 0; $i < count($cell->entry)-1; $i++)
	{
		//cell id
		$title = $cell->entry[$i]->title;
		$titleNext = $cell->entry[$i+1]->title;
		
		//look if next cellID begin with B, ifnot -> not booking for this day
		if ( (strcmp(substr($title,0,1), "A") == 0) && strcmp(substr($title,0,1),"B"))
		{
			$db->exec("INSERT INTO cells(worksheetID, name, value) VALUES('$sheet[2]', '".$cell->entry[$i]->content."','".$cell->entry[$i+1]->content."')");	
		}
		else if ( (strcmp(substr($title,0,1), "A") == 0) && strcmp(substr($title,0,1),"A"))
		{
			$db->exec("INSERT INTO cells(worksheetID, name, value) VALUES('$sheet[2]', '".$cell->entry[$i]->content."',null)");					
		}

	}		
	
	// meta refresh
	echo '<meta http-equiv="refresh" content="2; URL=index.php?action=updateDB&next='.($next+1)	.'">';