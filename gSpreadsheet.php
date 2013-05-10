<?php
/**
 * Class to read google spreadsheet
 */
 
 class gSpreadsheet
 {
	/**
	 * id of spreadsheet
	 */
	private $spreadsheetID = "";
	
	/**
	 * array of all worksheets
	 */
	private $worksheets = array();
	 
	 
	/**
	 * 
	 */
	function __construct($id)
	{
		$this->setSpreadSheetKey($id);
		$this->loadWorksheets();
	}
	
	/**
	 * 
	 */
	function getSpreadsheetKey()
	{
		return $this->spreadsheetID;
	}
	
	/**
	 * 
	 */
	 function setSpreadSheetKey($id)
	 {
	 	$this->spreadsheetID = $id;
	 }
	 /**
	  * 
	  */
	 private function fetchXML($xmlURL)
	 {
	 	$userAgent = 'gBooking';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $xmlURL);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
		$data = curl_exec($ch);
		return $data;
	 }
	 /**
	  * 
	  */
	 function loadWorksheets()
	 {
	 	$xmlURL = "https://spreadsheets.google.com/feeds/worksheets/".$this->getSpreadsheetKey()."/public/values";
		$data = $this->fetchXML($xmlURL);

		$xml = simplexml_load_string($data);
		
		for ($i = 0; $i < count ($xml->entry); $i++)
		{
			$id = explode("/", $xml->entry[$i]->id);
			$id = $id[count($id)-1];
			
			array_push($this->worksheets, array(
				$xml->entry[$i]->title, //name
				$xml->entry[$i]->id, //url
				$id, //id
				$xml->updated //date
			));
		}
		

		
	 }
	 
	 /**
	  * 
	  */
	 function getWorksheets()
	 {
	 	return $this->worksheets;
	 }
	 
	 /**
	  * 
	  */
	 function loadCells($id)
	 {
	 	$xmlURL = "https://spreadsheets.google.com/feeds/cells/".$this->getSpreadsheetKey()."/$id/public/values";
		$data = $this->fetchXML($xmlURL);
		$xml = simplexml_load_string($data);
		$return = array();
		array_push($return, 
							array(
							$xml->entry[1]->content, //bezeichnung
							
							$xml->entry[3]->content, //str
							
							$xml->entry[5]->content, //plz
							
							$xml->entry[7]->content, //ort
							
							$xml->entry[9]->content //Koordinaten
							));
		return $xml;
	 }
 }
