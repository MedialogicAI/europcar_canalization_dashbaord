<?php

	function listautentipassword () {	
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
		$sql = "SELECT *  FROM `utenti_europcar`";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			
			
			
			
			// return $result->num_rows;
			
			//PRECEDENTEMENTE C'era il ciclo while // Funzione select copiata da aggiornament31x.php
           
		    
			
			$indice = 0;
		    // output data of each row
			while($row = $result->fetch_assoc()) {
				//echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
				
				$daritorno[$indice]["id"] = $row["id"];
				//$daritorno[$indice]["username"] = $row["username"];
				$daritorno[$indice]["password"] = $row["password"];
				$daritorno[$indice]["email"] = $row["email"];
				$daritorno[$indice]["privilegi"] = $row["privilegi"];
				$daritorno[$indice]["nome_op"] = $row["nome_op"];
				$daritorno[$indice]["cognome_op"] = $row["cognome_op"];

	
                
				$indice = $indice + 1;
			}
			return $daritorno;		
		} else {
			//echo "0 results";
			return 0;
		}
		$conn->close();	
			
		}  //selectarrfromonetable

		
		
		
		
		
		
	function privilegi_utente ($emailowner) {	
		global $servername; global $username; global $password; global $dbname;

		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 

		//$sql = "SELECT * FROM `riferimenti_fat_for` WHERE `402fattura` LIKE '$fatt402' AND `email` LIKE 'senzaemail@senzaemail.com' ";
		$sql = "SELECT *  FROM `utenti_europcar` WHERE `email` LIKE '$emailowner'";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			
			
			
			
			// return $result->num_rows;
			
			//PRECEDENTEMENTE C'era il ciclo while // Funzione select copiata da aggiornament31x.php
           
		    
			
			$indice = 0;
		    // output data of each row
			while($row = $result->fetch_assoc()) {
				//echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
				
				$daritorno["id"] = $row["id"];
				//$daritorno["password"] = $row["password"];
				$daritorno["email"] = $row["email"];
				$daritorno["privilegi"] = $row["privilegi"];
				$daritorno["abilitazioni"] = $row["abilitazione_accesso"];

			}
			return $daritorno;		
		} else {
			//echo "0 results";
			return 0;
		}
		$conn->close();	
			
		}  //selectarrfromonetable

		


		
		
		
		
		
		/*		
	function dati_reg_utente ($id_utenti_durc) {	
		global $servername; global $username; global $password; global $dbname;

		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 

		//$sql = "SELECT * FROM `riferimenti_fat_for` WHERE `402fattura` LIKE '$fatt402' AND `email` LIKE 'senzaemail@senzaemail.com' ";
		//$sql = "SELECT *  FROM `utenti_durc` WHERE `email` LIKE '$emailowner'";
		$sql = "SELECT * FROM `registrazione_web` where fk_utenti_durc = $id_utenti_durc";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			
			// return $result->num_rows;
			//PRECEDENTEMENTE C'era il ciclo while // Funzione select copiata da aggiornament31x.php
           
			$indice = 0;
		    // output data of each row
			while($row = $result->fetch_assoc()) {
				//echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
				
				$daritorno["nome"] = $row["nome"];
				$daritorno["cognome"] = $row["cognome"];
				$daritorno["indirizzo"] = $row["indirizzo"];
				$daritorno["nazione"] = $row["nazione"];
				$daritorno["provincia"] = $row["provincia"];
				$daritorno["citta"] = $row["citta"];
				$daritorno["cap"] = $row["cap"];
				$daritorno["numero_telefono"] = $row["numero_telefono"];
				$daritorno["email_referente"] = $row["email_referente"];
				$daritorno["cod_fiscale"] = $row["cod_fiscale"];
				$daritorno["p_iva"] = $row["p_iva"];
				$daritorno["ragione_sociale"] = $row["ragione_sociale"];
				
			}
			return $daritorno;		
		} else {
			//echo "0 results";
			return 0;
		}
		$conn->close();	
			
		}  //selectarrfromonetable

		*/
		
		










?>