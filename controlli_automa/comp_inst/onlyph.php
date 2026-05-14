<?php 
require_once('../../dbconf.php');
require_once('../../funzioni-dash.php');
//require_once('../../global_var.php');
global $servername; 
global $username; 
global $password; 
global $dbname;

/* Preparo un array contenente gli orari lavorativi */

$storeSchedule = [
    'Sun' => ['00:00' => '00:00'],
    'Mon' => ['07:00' => '17:30'],
    'Tue' => ['07:00' => '17:30'],
    'Wed' => ['07:00' => '17:30'],
    'Thu' => ['07:00' => '17:30'],
    'Fri' => ['07:00' => '17:30'],
    'Sat' => ['00:00' => '00:00'],
];


/*Seleziono tutte le pratiche con stato 400 dove il campo calc_12_24_h è = 0*/
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 


$sql = "SELECT id as ataraxia_id,atar_file,targa_veicolo,end_processing,calc_12_24_h FROM `ataraxia_main` WHERE status=400 AND calc_12_24_h = 0 AND dt_insert BETWEEN ('2019-02-01') AND NOW()";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while($rows = $result->fetch_assoc()) {
		
		$row_ataraxia_main[] = $rows;
	}

/*inserisco le righe nella tabella dt_reminder e aggiorno il campo calc_12_24h = 1*/
	for ($i=0;$i<count($row_ataraxia_main);$i++){
		
		$sql_insert = "INSERT INTO `dt_reminder` (`id`,`ataraxia_id`, `atar_file`, `targa_veicolo`, `end_processing`, `dt_12h`, `dt_24h`)
		VALUES (NULL,
		'".$row_ataraxia_main[$i]['ataraxia_id']."',
		'".$row_ataraxia_main[$i]['atar_file']."',
		'".$row_ataraxia_main[$i]['targa_veicolo']."',
		'".$row_ataraxia_main[$i]['end_processing']."',
		NULL,NULL)";
		$conn->query($sql_insert);
		
		$sql_update_calc = "UPDATE `ataraxia_main` SET `calc_12_24_h` = '1' WHERE `ataraxia_main`.`id` =".$row_ataraxia_main[$i]['ataraxia_id'];
		
		if ($conn->query($sql_update_calc) === TRUE) { 
			echo "Record updated successfully <br>";
		} else {
			echo "Error updating record: " . $conn->error . "<br>";
		}
		
	}	
}

else{
	echo 'Tutti i reminder sono gia stati calcolati <br> ';
}
	
/*Seleziono tutte le righe della tabella dt_reminder a cui non è assagnata nessuna data di reminder per 12 e 24 ore*/	
	$sql_reminder = "SELECT * FROM `dt_reminder` WHERE dt_12h is null and dt_24h is null";
	$result = $conn->query($sql_reminder);
	
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			
			$idRow = $row['id'];
			$endDate = $row['end_processing'];
			
			dodiciOre($storeSchedule,$idRow,$endDate);
			ventiquattroOre($storeSchedule,$idRow,$endDate);
		}
	}
	
	else {
		
		echo 'Non ci sono righe a cui non sono state assegnate le date di remineder';
	}
	

function dodiciOre($storeSchedule,$id,$dbDate){
	
	$dbDate = new DateTime($dbDate);
	$dbTimestamp = $dbDate->getTimestamp();
	$currentTime = $dbDate->format('H:i');

	$status = 'closed';

	$dodiciOre = 0;
	while ($dodiciOre <= 11) {
		
		$dbDate = $dbDate->modify('+ 1 hour');
		$currentTime = $dbDate->format('H:i');
		$dbTimestamp = $dbDate->getTimestamp();
		$currentDate = $dbDate->format('m-d');
		
		foreach ($storeSchedule[date('D', $dbTimestamp)] as $startTime => $endTime) {

			if (($startTime < $currentTime) && ($currentTime < $endTime) && ($currentDate != '01-01') && ($currentDate != '01-06') && ($currentDate != '04-21') && ($currentDate != '04-22') && ($currentDate != '04-25') && ($currentDate != '05-01') && ($currentDate != '06-02') && ($currentDate != '07-15') && ($currentDate != '11-01') && ($currentDate != '12-08') && ($currentDate != '12-25') && ($currentDate != '12-26')) {
				$status = 'open';
				$dodiciOre++;
				//echo $currentTime = $dbDate->format('Y-m-d H:i:s') . ' lavorativo - contatore aumentato a ' .$dodiciOre. '<br>';
			}
			else{
				
				//echo $currentTime = $dbDate->format('Y-m-d H:i:s') . ' NON lavorativo - contatore fermo a ' .$dodiciOre. '<br>';
			}
		}
		
	}

	echo 'fine ciclo - le 12 ore lavorative terminano il ' . $currentTime = $dbDate->format('Y-m-d H:i:s') . '<br>';
	$currentTime = $dbDate->format('Y-m-d H:i:s');

	global $servername; global $username; global $password; global $dbname;
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 
	
	$sqlUpdate = "UPDATE `dt_reminder` SET dt_12h = '$currentTime' WHERE id = $id";
	
	if ($conn->query($sqlUpdate) === TRUE) { 
		echo "Record updated successfully <br>";
	} else {
		echo "Error updating record: " . $conn->error . "<br>";
	}
}


function ventiquattroOre($storeSchedule,$id,$dbDate){
	
	$dbDate = new DateTime($dbDate);
	$dbTimestamp = $dbDate->getTimestamp();
	$currentTime = $dbDate->format('H:i');

	$status = 'closed';

	$ventiquattroore = 0;
	while ($ventiquattroore <= 23) {
		
		$dbDate = $dbDate->modify('+ 1 hour');
		$currentTime = $dbDate->format('H:i');
		$dbTimestamp = $dbDate->getTimestamp();
		
		foreach ($storeSchedule[date('D', $dbTimestamp)] as $startTime => $endTime) {

			if (($startTime < $currentTime) && ($currentTime < $endTime) && ($currentDate != '01-01') && ($currentDate != '01-06') && ($currentDate != '04-21') && ($currentDate != '04-22') && ($currentDate != '04-25') && ($currentDate != '05-01') && ($currentDate != '06-02') && ($currentDate != '07-15') && ($currentDate != '11-01') && ($currentDate != '12-08') && ($currentDate != '12-25') && ($currentDate != '12-26')) {
				$status = 'open';
				$ventiquattroore++;
				//echo $currentTime = $dbDate->format('Y-m-d H:i:s') . ' lavorativo - contatore aumentato a ' .$ventiquattroore. '<br>';
			}
			else{
				
				//echo $currentTime = $dbDate->format('Y-m-d H:i:s') . ' NON lavorativo - contatore fermo a ' .$ventiquattroore. '<br>';
			}
		}
		
	}

	echo 'fine ciclo - le 24 ore lavorative terminano il ' . $currentTime = $dbDate->format('Y-m-d H:i:s') . '<br>';
	$currentTime = $dbDate->format('Y-m-d H:i:s');

	global $servername; global $username; global $password; global $dbname;
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 
	
	$sqlUpdate = "UPDATE `dt_reminder` SET dt_24h = '$currentTime' WHERE id = $id";
	
	if ($conn->query($sqlUpdate) === TRUE) { 
		echo "Record updated successfully <br>";
	} else {
		echo "Error updating record: " . $conn->error . "<br>";
	}
}



?>