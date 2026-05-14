<?php

require_once('../dbconf.php');




if(isset($_GET['id'])) {
	echo "ID: ".$_GET['id'];
	$ident = $_GET['id'];
	cambiastato ($ident);
	cambiastatoprior ($ident);
	
}
else{
	echo "non è settato l\'ID!";
}



/*
$stato = pendingselection ();

if ($stato == 1) {$stato = 0;}
else {$stato = 1;}
cambiastato ($stato);
*/



?>









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

		//$sql = "SELECT * FROM `riferimenti_fat_for` WHERE `402fattura` LIKE '$fatt402' AND `email` LIKE 'senzaemail@senzaemail.com' ";
		$sql = "SELECT flagstopalltask FROM `stop-extremis` where idstop = 1";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			
			
			//return $result->num_rows;
			
			 $row = $result->fetch_row();
			  return $row[0];
			
			//PRECEDENTEMENTE C'era il ciclo while // Funzione select copiata da aggiornament31x.php

			
			
			
			
		} else {
			//echo "0 results";
			return 0;
		}
		$conn->close();	
			
		}  //selectarrfromonetable


		
		
		
		
		
		
	    
		
		function cambiastato ($idop) {	
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
		$sql = "UPDATE `clienti_pre_fat_for` SET `rifiutato` = '2' WHERE `clienti_pre_fat_for`.`id_cpff` = $idop;";
		$result = $conn->query($sql);

		/*
		if ($result->num_rows > 0) {
			
			
			//return $result->num_rows;
			
			 $row = $result->fetch_row();
			  return $row[0];
			
			//PRECEDENTEMENTE C'era il ciclo while // Funzione select copiata da aggiornament31x.php

			
			
			
			
		} else {
			//echo "0 results";
			return 0;
		}
		*/
		$conn->close();	
			
		}  //selectarrfromonetable
		
		
		
		
		
		
		
		
		
		function cambiastatoprior ($idop) {	
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
		//$sql = "UPDATE `priorityorder` SET `end_lavorazione` = '1' WHERE `priorityorder`.`ext_id_queue` = $idop;";
		
		$sql = "UPDATE `priorityorder` SET `start_lavorazione` = '1', `end_lavorazione` = '1' WHERE `priorityorder`.`id_pr` = $idop;";
		
		$result = $conn->query($sql);

		/*
		if ($result->num_rows > 0) {
			
			
			//return $result->num_rows;
			
			 $row = $result->fetch_row();
			  return $row[0];
			
			//PRECEDENTEMENTE C'era il ciclo while // Funzione select copiata da aggiornament31x.php

			
			
			
			
		} else {
			//echo "0 results";
			return 0;
		}
		*/
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