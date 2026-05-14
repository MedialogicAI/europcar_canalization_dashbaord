<?php

require_once('../dbconf.php');


$atar_file = trim($_POST['dataAtar']);

global $servername;
		global $username;
		global $password;
		global $dbname;



		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 

		$sql = "UPDATE ataraxia_main SET status = '560',start_processing = now(),end_processing = now(), observations = 'Processed Manually by the operator' WHERE atar_file like '$atar_file'";
		$result = $conn->query($sql);
		$conn->close();	
		$risposta = 'record aggiornati';
		
		die(json_encode($risposta));



?>