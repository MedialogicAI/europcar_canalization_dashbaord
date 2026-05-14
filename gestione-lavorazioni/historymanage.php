<?php

require_once('../dbconf.php');




echo $inlavorazione = pendingselection_id();
echo "<br>"; 
echo $neldb = pendingselectionhistoryzed ();

if (($neldb != $inlavorazione) && ($inlavorazione != false))
{
	if ($neldb != 0){
	spostalavorato($neldb);    //Prende il vecchio e lo mette nella colonna oldwordid
	}
	inseriscinuovaid($inlavorazione);  //Prende il vecchio e lo mette nella colonna actualworkid
	
	echo "<br>Faccio Update";
}
else {
	
	if ($inlavorazione == false){
		if ($neldb != 0){
	   spostalavorato($neldb);
	   inseriscinuovaid($inlavorazione);
	   echo "<br>Nontocco";
		}
	}
}

?>


{
      "value": "<?php echo pendingselectionhistoryzed (); ?>"
}










<?php
// Parte funzioni PHP








	function pendingselectionhistoryzed () {	
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
		//pendingselection_id();
		$sql = "SELECT actualworkid FROM workhistory where id_wohi = 1;";
		//$sql = "SELECT id_wohi, actualworkid, oldwordid FROM snai.workhistory;";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			
			
			$row = $result->fetch_row();
			return $row[0];
			
			//PRECEDENTEMENTE C'era il ciclo while // Funzione select copiata da aggiornament31x.php

			
			
			
			
		} else {
			//echo "0 results";
			return false;
		}
		$conn->close();	
			
		}  //pendingselectionhistoryzed


		
		
		
		
		
		
		
		
		
		
		
		
		function spostalavorato($neldb) {	
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
		$sql = "UPDATE `workhistory` SET `oldwordid` = '$neldb' WHERE `workhistory`.`id_wohi` = 1;";
		//$sql = "UPDATE oldwordid set '".$neldb."' where id_wohi = 1";
		$result = $conn->query($sql);

		/*if ($result->num_rows > 0) {
			
			
			
			$row = $result->fetch_row();
			
			
			
			return $row[0];
			
			//PRECEDENTEMENTE C'era il ciclo while // Funzione select copiata da aggiornament31x.php

			
			
			
			
		} else {
			//echo "0 results";
			return "Nessuna Lavorazione";
		}*/
		
		$conn->close();	
			
		}  //pendingselection402
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		function pendingselection_id () {	
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
		$sql = "SELECT `id_cpff` FROM `clienti_pre_fat_for` WHERE `ricevuto` = 1 AND (registratosufatfor = 0 OR registratosufatfor = 1) AND `SM35OK` = 0 AND `pdfgenerato` = 0 LIMIT 1";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			
			
			
			$row = $result->fetch_row();
			
			
			
			return $row[0];
			
			//PRECEDENTEMENTE C'era il ciclo while // Funzione select copiata da aggiornament31x.php

			
			
			
			
		} else {
			//echo "0 results";
			return "0";
		}
		$conn->close();	
			
		}  //pendingselection_id
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		function inseriscinuovaid ($inlavorazione) {	
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
		$sql = "UPDATE `workhistory` SET `actualworkid` = '$inlavorazione' WHERE `workhistory`.`id_wohi` = 1;";
		//$sql = "UPDATE actualworkid set '".$inlavorazione."' where id_wohi = 1";
		$result = $conn->query($sql);

		/*if ($result->num_rows > 0) {
			
			
			
			$row = $result->fetch_row();
			
			
			
			return $row[0];
			
			//PRECEDENTEMENTE C'era il ciclo while // Funzione select copiata da aggiornament31x.php

			
			
			
			
		} else {
			//echo "0 results";
			return "Nessuna Lavorazione";
		}*/
		
		$conn->close();	
			
		}  //pendingselection402
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		

		
		

?>