<?php

require_once('../dbconf.php');


$ilmioid = dammiultimoidlavorato ();

?>


{
      "value": "<?php echo pendingselection ($ilmioid); ?>"
}







<?php
// Parte funzioni PHP

	function pendingselection ($idstoricizzato) {	
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

		//$sql = "SELECT * FROM `riferimenti_fat_for` WHERE `402fattura` LIKE '$fatt402' AND `email` LIKE 'senzaemail@senzaemail.com' ";
		//$sql = "SELECT numprotocollo FROM `clienti_pre_fat_for`  where sm35ok = 1 order by id_cpff desc Limit 1";
		$sql = "SELECT numprotocollo FROM `clienti_pre_fat_for` where id_cpff = $idstoricizzato";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			
			
			$row = $result->fetch_row();
			return $row[0];
			
			//PRECEDENTEMENTE C'era il ciclo while // Funzione select copiata da aggiornament31x.php

			
			
			
			
		} else {
			//echo "0 results";
			return 0;
		}
		$conn->close();	
			
		}  //selectarrfromonetable


		
		
		
		
		
    function dammiultimoidlavorato () {	
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

		//$sql = "SELECT * FROM `riferimenti_fat_for` WHERE `402fattura` LIKE '$fatt402' AND `email` LIKE 'senzaemail@senzaemail.com' ";
		//UPDATE `workhistory` SET `actualworkid` = '$inlavorazione' WHERE `workhistory`.`id_wohi` = 1;
		//$sql = "SELECT `oldwordid` FROM `workhistory`  where id_wohi = 1;";
		$sql = "SELECT `ext_id_queue` FROM `priorityorder` where start_lavorazione = 1 and end_lavorazione = 1 and `ext_id_queue` <> 0 order by id_pr desc limit 1";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			
			
			$row = $result->fetch_row();
			return $row[0];
			
			
			//PRECEDENTEMENTE C'era il ciclo while // Funzione select copiata da aggiornament31x.php

			
			
			
			
		} else {
			//echo "0 results";
			return 0;
		}
		$conn->close();	
			
		}  //selectarrfromonetable	
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		/*
					$indice = 0;
			// output data of each row
			while($row = $result->fetch_assoc()) {
				//echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
				$daritorno[0] = $row["402fattura"];
				$daritorno[1] = $row["89cliente"];
				$daritorno[2] = $row["85sede"];
				$daritorno[3] = $row["email"];
				$daritorno[4] = $coddin1 = randomNumber(9);
				$daritorno[5] = $coddin2 = randomNumber(9);
				

	
                $time  = date( "Y_m", strtotime( "now -1 month" ));
                //$time  = strtotime("2018.01.19");
                //$final = date("Y-m-d", strtotime("-1 month", $time));
				
			    $daritorno[6] = $time;
				
				//$indice = $indice + 1;
			}
			
			
			
			return $daritorno;	
			
        */
		
		

?>