

<?php

require_once('../dbconf.php');





$filestodel = glob('download_temp/*'); // get all file names
foreach($filestodel as $file){ // iterate files
  if(is_file($file))
    unlink($file); // delete file
}



date_default_timezone_set('Europe/Rome'); 

$fscdata =  date("d-m-Y_H-i-s"); 

$cartellafileexport = "download_temp/lista_pending".$fscdata.".csv";


$fp = fopen($cartellafileexport, 'w');

$inlavorazione = pendingselection ();

foreach ($inlavorazione as $fields) {
    fputcsv($fp, $fields, ";");
}

fclose($fp);



sleep(5);

header('Location: '.$cartellafileexport);
exit();

?>






<?php


echo "<pre>";
print_r($inlavorazione);
echo "</pre>";

?>


{
      "value": "<?php /*echo  '<font color=\'green\'>'. $inlavorazione.'</font> / <font color=\'orange\'>'.$attended.'</font> / <font color=\'red\'>'.pendingselectionrifiutati ().'</font>'; */ ?>"
}







<?php
// Parte funzioni PHP

	function pendingselection () {	
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

		$sql = "SELECT codice_stazione, DATE_FORMAT(t1.ts_in_dt,'%d/%m/%Y %T') as tsindtloc, DATE_FORMAT(t1.start_processing,'%d/%m/%Y %T') as start_processing_loc, t1.targa_veicolo, t1.atar_file,t2.long_description FROM ataraxia_main as t1 LEFT JOIN tab_status as t2 ON t1.status = t2.actual_status WHERE status` = 400 AND wsm_bsm_status like '%Reminder Sent%' order by ts_in_dt desc";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			
			global $fscdata; 
			  
			  $ogniriga[0][] = "STATION CODE";
			  $ogniriga[0][] = "REQUESTED DATE";
			  $ogniriga[0][] = "START PROCESS DATE";
			  $ogniriga[0][] = "REGISTRATION NUMBER";
			  $ogniriga[0][] = "ATAR FILE";
			  $ogniriga[0][] = "PROCESS RESULT";
			
			
			$indekfsc = 1;
			while($row = $result->fetch_assoc()) {
            
				$ogniriga[$indekfsc][] = $row["codice_stazione"];
				$ogniriga[$indekfsc][] = $row["tsindtloc"];
				$ogniriga[$indekfsc][] = $row["start_processing_loc"];
				$ogniriga[$indekfsc][] = $row["targa_veicolo"];
				$ogniriga[$indekfsc][] = (string)$row["atar_file"];
				$ogniriga[$indekfsc][] = 'Registration number still in IRM after 12hrs';
				  
			
				$indekfsc = $indekfsc +1;
			}
				
				return $ogniriga;
			

			
		
			
		} else {
	
			return 0;
		}
		$conn->close();	
			
		} 


		
		
		
		
		
		
		
		
		
		
	
		

?>
