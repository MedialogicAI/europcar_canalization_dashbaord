<?php

if (0) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

require_once('/home/ataraxia/public_html/dbconf.php');
require_once('/home/ataraxia/public_html/funzioni-dash.php');
//require_once('../login-lista-user-db.php');
//require_once('../login-err-non-valido.php');
//require_once('../global_var.php');
					
					
global $servername; global $username; global $password; global $dbname;
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 


//$sql = "SELECT * FROM `riferimenti_fat_for` WHERE `402fattura` LIKE '$fatt402' AND `email` LIKE 'senzaemail@senzaemail.com' ";
$sql = "SELECT id,status,retrybricked,oldstatusbricked FROM `ataraxia_main` where retrybricked = 2 and status = 90";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	//return $result->num_rows;						
	while($row = $result->fetch_assoc()) {
		
		$sqlUpdate = "UPDATE `ataraxia_main` SET status = 540 ,oldstatusbricked = 90 WHERE id =".$row['id'];
	
		if ($conn->query($sqlUpdate) === TRUE) { 
			echo "Record updated successfully";
		} else {
			echo "Error updating record: " . $conn->error;
		}
				
	}
	
} 

else { 

	echo 'non ci sono risultati';
}
$conn->close();	
	

?>		
			
