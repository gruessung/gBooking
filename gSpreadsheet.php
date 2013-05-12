<?php
/**
 * Class to read google spreadsheet
 * 
 * This class provide functionality to read spreadsheets from google docs/dtive
 * 
 * Read-Only is supported
 * 
 * @author Alexander Gruessung
 * @since 06-05-2013
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
	 * public constructor
	 * 
	 * @param id of spreadsheet
	 */
	function __construct($id)
	{
		$this->setSpreadSheetKey($id);
		$this->loadWorksheets();
	}
	
	/**
	 * function getSpreadsheetKey
	 * 
	 * @return key of current spreadsheet
	 */
	function getSpreadsheetKey()
	{
		return $this->spreadsheetID;
	}
	
	/**
	 * function setSpreadsheetKey
	 * 
	 * @param id of current spreadsheet
	 */
	 function setSpreadSheetKey($id)
	 {
	 	$this->spreadsheetID = $id;
	 }
	 
	 /**
	  * private function fetchXML
	  * 
	  * get XML and return it
	  * 
	  * @param xmlURL
	  * @return curl object
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
	  *	function loadWorksheets
	  * 
	  * function to load all worksheets and save this in an array 
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
	  * function getWorksheets
	  * 
	  * @return array with all worksheets of current spreadsheet
	  */
	 function getWorksheets()
	 {
	 	return $this->worksheets;
	 }
	 
	 /**
	  * function loadCells
	  * 
	  * load all cells with value of worksheet $id
	  * 
	  * @param id of worksheet
	  * @return array with cells
	  */
	 function loadCells($id)
	 {
	 	$xmlURL = "https://spreadsheets.google.com/feeds/cells/".$this->getSpreadsheetKey()."/$id/public/values";
		$data = $this->fetchXML($xmlURL);
		$xml = simplexml_load_string($data);
		return $xml;
	 }
 }
