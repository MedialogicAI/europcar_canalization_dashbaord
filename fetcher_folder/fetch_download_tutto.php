

<?php

require_once('../dbconf.php');





$filestodel = glob('download_temp/*'); // get all file names
foreach($filestodel as $file){ // iterate files
  if(is_file($file))
    unlink($file); // delete file
}



date_default_timezone_set('Europe/Rome'); 

$fscdata =  date("d-m-Y_H-i-s"); 

$cartellafileexport = "download_temp/all_practices".$fscdata.".csv";


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

		//$sql = "select codice_stazione, DATE_FORMAT(ts_in_dt,'%d/%m/%Y %T') as ts_in_dt_loc, DATE_FORMAT(start_processing,'%d/%m/%Y %T') as start_processing_loc, targa_veicolo, atar_file, email_inviata_autofficina, status  FROM `ataraxia_main` WHERE dt_insert BETWEEN ('2019-04-01') AND NOW() ORDER BY `ts_in_dt` DESC";
		$sql="SELECT 
          codice_stazione, 
          DATE_FORMAT(t1.ts_in_dt,'%d/%m/%Y %T') as tsindtloc, 
          DATE_FORMAT(t1.start_processing,'%d/%m/%Y %T') as start_processing_loc, 
          t1.targa_veicolo as targa_veicolo, 
          t1.atar_file as atar_file, 
          t2.long_description as long_description, 
          t1.email_inviata_autofficina as email,
          t3.ragione_sociale as supplier,
	      t1.observations
        FROM
	      ataraxia_main as t1 
        inner JOIN tab_status as t2 ON t1.status = t2.actual_status
        left JOIN canaliz_rete as t3 ON t3.EMAIL  = t1.email_inviata_autofficina
        WHERE t1.dt_insert BETWEEN ('2019-04-01') AND NOW()
        ORDER BY t1.dt_insert DESC";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			
			global $fscdata; 
			  
			  $ogniriga[0][] = "STATION CODE";
			  $ogniriga[0][] = "REQUESTED DATE";
			  $ogniriga[0][] = "START PROCESS DATE";
			  $ogniriga[0][] = "REGISTRATION NUMBER";
			  $ogniriga[0][] = "ATAR FILE";
			  $ogniriga[0][] = "CAUSE";
			  $ogniriga[0][] = "SUPPLIER";
			  $ogniriga[0][] = "SUPPLIER E-MAIL";
			
			
			$indekfsc = 1;
			while($row = $result->fetch_assoc()) {
            
				$ogniriga[$indekfsc][] = $row["codice_stazione"];
				$ogniriga[$indekfsc][] = $row["tsindtloc"];
				$ogniriga[$indekfsc][] = $row["start_processing_loc"];
				$ogniriga[$indekfsc][] = $row["targa_veicolo"];
				$ogniriga[$indekfsc][] = (string)$row["atar_file"];
				//$ogniriga[$indekfsc][] = $row["observations"];
                $ogniriga[$indekfsc][] = $row["long_description"];
				$ogniriga[$indekfsc][] = $row["supplier"];
				$ogniriga[$indekfsc][] = $row["email"];
				
				$indekfsc = $indekfsc +1;
			}
				
				return $ogniriga;
			

			
		
			
		} else {
	
			return 0;
		}
		$conn->close();	
			
		} 


		
		
		
	function give_the_status_icon ($give_me_status,$qualetabella,$qualecolonna,$cheritorno) {	
		global $servername;
		global $username;
		global $password;
		global $dbname;

		$conn = new mysqli($servername, $username, $password, $dbname);
     	if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error);  } 

		//$sql = "SELECT html_stato FROM `tab_stati` where stato_lavorazione = ".$give_me_status;
		//$sql = "SELECT $cheritorno FROM `ataraxia`.`$qualetabella` where '$qualecolonna' = ".$give_me_status;
		$sql = "SELECT $cheritorno FROM `$qualetabella` where $qualecolonna = $give_me_status";
		
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			// output data of each row
			$row = $result->fetch_assoc();
			$daritorno = $row[$cheritorno];
			return $daritorno;
			/*
			while($row = $result->fetch_assoc()) {
				$daritorno = $row["html_stato"];
			}  //Chiusura While
			return $daritorno;
			*/	

		}
		else {
			return 0;
		}
		$conn->close();	
	}  //selectarrfromonetable	
		
		
	function get_ragione_sociale($emailcanalizzata)
{
 		global $servername; global $username; global $password; global $dbname;
         $conn = new mysqli($servername, $username, $password, $dbname);
     	  if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error);  } 

		  $sql = "select ragione_sociale FROM `canaliz_rete` where EMAIL like '%".$emailcanalizzata."%' ";
		  //$sql = "SELECT `id_univoco` FROM `$tab_main_pratiche` where stato like 'Chiusa' and stato_pratica_web = 440";
		  
		  if (trim($emailcanalizzata) == "") { return ""; }
		  else {
		  
		  $result = $conn->query($sql);

		if ($result->num_rows > 0) {
			
			//$row = $result->fetch_assoc();
			//$daritorno = $row["id_univoco"];
			//return $daritorno;
			
			// output data of each row
			$indic = 0;
			while($row = $result->fetch_assoc()) {
				
		                $daritorno = $row["ragione_sociale"];
						$indic++;
			 } 
			  //Chiusura While
			   return $daritorno;	
			} else { return "CRISTALLI"; }
		    
		    }
			
			$conn->close();
			
}	
		
		
		
	
		

?>
