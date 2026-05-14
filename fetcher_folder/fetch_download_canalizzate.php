

<?php

require_once('../dbconf.php');





$filestodel = glob('download_temp/*'); // get all file names
foreach($filestodel as $file){ // iterate files
  if(is_file($file))
    unlink($file); // delete file
}



date_default_timezone_set('Europe/Rome'); 

$fscdata =  date("d-m-Y_H-i-s"); 

$cartellafileexport = "download_temp/canalizzate".$fscdata.".csv";


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

		$sql = "SELECT t1.codice_stazione, DATE_FORMAT(t1.ts_in_dt,'%d/%m/%Y %T') as ts_in_dt_loc, DATE_FORMAT(t1.start_processing,'%d/%m/%Y %T') as start_processing_loc, t1.targa_veicolo, t1.atar_file, t1.defect_code ,t1.email_inviata_autofficina, t2.RAGIONE_SOCIALE FROM ataraxia_main as t1 LEFT JOIN canaliz_rete as t2 ON t1.email_inviata_autofficina = t2.EMAIL WHERE t1.status in (400,401,402,403) AND dt_insert BETWEEN ('2019-04-01') AND NOW() ORDER BY `ts_in_dt` DESC";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			
			global $fscdata; 
			  
			  $ogniriga[0][] = "STATION CODE";
			  $ogniriga[0][] = "REQUESTED DATE";
			  $ogniriga[0][] = "START PROCESS DATE";
			  $ogniriga[0][] = "REGISTRATION NUMBER";
			  $ogniriga[0][] = "ATAR FILE";
			  $ogniriga[0][] = "DEFECT CODE";
			  $ogniriga[0][] = "SUPPLIER";
			  $ogniriga[0][] = "SUPPLIER E-MAIL";
			  
			
			$indekfsc = 1;
			while($row = $result->fetch_assoc()) {
            
				$ogniriga[$indekfsc][] = $row["codice_stazione"];
				$ogniriga[$indekfsc][] = $row["ts_in_dt_loc"];
				$ogniriga[$indekfsc][] = $row["start_processing_loc"];
				$ogniriga[$indekfsc][] = $row["targa_veicolo"];
				$ogniriga[$indekfsc][] = (string)$row["atar_file"];
				$ogniriga[$indekfsc][] = $row["defect_code"];
				  
				if($row["RAGIONE_SOCIALE"] == ''){
					$ogniriga[$indekfsc][] = $row["defect_code"];
				}
				else{
					$ogniriga[$indekfsc][] = $row["RAGIONE_SOCIALE"];
				}
				
				$ogniriga[$indekfsc][] = $row["email_inviata_autofficina"];

				$indekfsc = $indekfsc +1;
			}
				
				return $ogniriga;
			

			
		
			
		} else {
	
			return 0;
		}
		$conn->close();	
			
		} 


		
		
		
		
		
		
		
		
		
		
	
		

?>
