<?php
    /**
	 * Copyright 2013 Alexander Grüßung

	   Licensed under the Apache License, Version 2.0 (the "License");
	   you may not use this file except in compliance with the License.
	   You may obtain a copy of the License at
	
	       http://www.apache.org/licenses/LICENSE-2.0
	
	   Unless required by applicable law or agreed to in writing, software
	   distributed under the License is distributed on an "AS IS" BASIS,
	   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	   See the License for the specific language governing permissions and
	   limitations under the License.
	 */
	 
	 //include config
	 require_once("config.php");
	 
	 //show header
	 require_once("style/header.php");
	 
	 //show action
	 
	 //if $_GET["action"] is given
	 if (isset($_GET["action"]))
	 {
	 	$p = $_GET["action"];
		
	 	//look !if file exists
	 	if (!file_exists("action/$p.php"))
		{
			$p = "404";
		}
	 }
	 //else include default home
	 else 
	 {
	 	$p = "index";
	 }
	 
	 //include page
	 require_once("action/$p.php");
	 
	 //show footer
	 require_once("style/footer.php");
?>