<?php

if (0) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

require_once('../dbconf.php');
require_once('../funzioni-dash.php');
//require_once('../login-lista-user-db.php');
//require_once('../login-err-non-valido.php');
//require_once('../global_var.php');
					
					
global $servername; global $username; global $password; global $dbname;
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 


//$sql = "SELECT * FROM `riferimenti_fat_for` WHERE `402fattura` LIKE '$fatt402' AND `email` LIKE 'senzaemail@senzaemail.com' ";
//$sql = "SELECT targa_veicolo, end_processing, status,id FROM `ataraxia_main` WHERE NOW() BETWEEN (end_processing + INTERVAL 2 DAY) AND NOW() AND status = 400";
$sql = "SELECT targa_veicolo, end_processing, status,id FROM `ataraxia_main` WHERE status = 0 AND riparazioni_multiple like 'SI'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	//return $result->num_rows;						
	while($row = $result->fetch_assoc()) {
		
		//echo $row['targa_veicolo'] . '<br>';
		/*$sqlMovement = "SELECT * FROM `movement` WHERE REGISTRATION_NUMBER like '".$row['targa_veicolo']."'";
		//$resultMovement = $conn->query($sqlMovement);
		
		
		if ($resultMovement->num_rows > 0){
			
			echo 'targa trovata non fare nulla<br><hr>';
			
		}
		
		else{
			
			//$rowmov = $resultMovement->fetch_assoc();
			//print_r($rowmov);
			
			echo 'targa non trovata  - aggiorna record<br/><hr>';
			$sqlUpdate = "UPDATE ataraxia_main SET status='590' WHERE id='".$row['id']."'";
			
			if ($conn->query($sqlUpdate) === TRUE) {
				echo "Record updated successfully";
			} else {
				echo "Error updating record: " . $conn->error;
			}
			
		}
		
		//$row[] = $rows;
	}*/
		echo $sqlUpdate = "UPDATE ataraxia_main SET status='590' WHERE id='".$row['id']."'";
		$conn->query($sqlUpdate);
	}
}	

else { 

	echo 'non ci sono risultati';
}
$conn->close();	
	

?>		
			
