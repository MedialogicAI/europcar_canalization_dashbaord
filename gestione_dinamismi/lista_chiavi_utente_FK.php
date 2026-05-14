<?php


if (0) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

require_once('../dbconf.php');
require_once('../funzioni-dash.php');
require_once('../login-lista-user-db.php');
require_once('../login-err-non-valido.php');
require_once('../global_var.php');






if ($utenepriv["abilitazioni"] == 0) { 	exit ("User not enabled"); }
 else if ($utenepriv["abilitazioni"] == 1){  
 
         //echo "ID Utente: ".$utenepriv["id"];  
         //echo "<br>";
         //echo "Email Owner utente: ".$utenepriv["email"];  
 
 
		 $arr_compilatabella = personal_list_apikey ($utenepriv["email"]);
		 
		 if ($arr_compilatabella == 0)  { 
            $listachiavitxt = '<tr> <td> <div id="chiave_"> Nessuna chiave attiva per questo utente </div>	</td> <td> <div id="def_email_"> </div>	</td> <td> <div id="credit_"> </div> </td>	</tr>';
			$myObj->value = $listachiavitxt;  $myJSON = json_encode($myObj); echo $myJSON;
		 }
         else {
			 
			 
			 //$listachiaviarr[] = '<thead>';
			 
			 foreach ($arr_compilatabella as $valarrcomptab) {
				 
				//$valarrcomptab["id"];
				$valarrcomptab["accesskey"];
				$valarrcomptab["default_req_email"];
				//$valarrcomptab["account_owner"];
				$valarrcomptab["crediti"];
				
				
				if ($valarrcomptab["crediti"] == "-1"){ $label_crediti = "Unlimited"; }else { $label_crediti =  $valarrcomptab["crediti"]; }
				
				  $listachiaviarr[] = '<tr id="'.$valarrcomptab["id"].'"> <td> <div id="chiave_'.$valarrcomptab["id"].'">'.$valarrcomptab["accesskey"].'</div> </td> <td> <div id="def_email_'.$valarrcomptab["id"].'">'.$valarrcomptab["default_req_email"].' </div>	</td> <td> <div id="credit_'.$valarrcomptab["id"].'">'.$label_crediti.' </div> </td> <td> <div id="restcode_"> <input type="button" class="staticoid" id="'.$valarrcomptab["id"].'" value="Rest Code" onclick="mufunll(this);" ></input> </div> </td>	</tr>';
				 
			 } // Chiusura Foreach
			 
			                //listachiaviarr[] = ' ';
									
			 //listachiaviarr[] = '</thead>';
			 $listachiavitxt = implode("", $listachiaviarr);
			 if ($listachiavitxt != ""){
				 $myObj = new \stdClass();
			 $myObj->value = $listachiavitxt;  $myJSON = json_encode($myObj); echo $myJSON; }
			 
			 
		 }// Chiurusa Else esistenza chiavi utente
 
 }



/*
if(isset($_GET['id'])) {
	echo "ID: ".$_GET['id'];
	$ident = $_GET['id'];
	//cambiastato ($ident);
	//cambiastatoprior ($ident);
}
else{
	echo "non è settato l\'ID!";
}
*/






/*

	<tr>
		<td>
			<div id="chiave_">  </div>
		</td>
		<td>
			<div id="def_email_"> </div>
		</td>
		<td>
			<div id="credit_"> </div>
		</td>
	</tr>	

*/




/*

$stato = pendingselection ();

if ($stato == 1) {$stato = 0;}
else {$stato = 1;}
cambiastato ($stato);
*/



?>









<?php


	function personal_list_apikey ($email_owner) {	
		global $servername; global $username; global $password;	global $dbname;
		$conn = new mysqli($servername, $username, $password, $dbname);
		if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error);	} 

		$sql = "SELECT * FROM `api_keys` where account_owner like '$email_owner'";
		
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			
			
			$indice = 0;
		    // output data of each row
			while($row = $result->fetch_assoc()) {
				
				$daritorno[$indice]["id"] = $row["id"];
				$daritorno[$indice]["accesskey"] = $row["accesskey"];
				$daritorno[$indice]["default_req_email"] = $row["default_req_email"];
				$daritorno[$indice]["account_owner"] = $row["account_owner"];
				$daritorno[$indice]["crediti"] = $row["crediti"];
				
				$indice = $indice + 1;
			}
			return $daritorno;		
			
		} else {
			//echo "0 results";
			return 0;
		}
		$conn->close();	
			
		}  


		
		
		
		
		
		
	    
		
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
		$sql = "UPDATE `clienti_pre_fat_for` SET `sm35ok` = '0' WHERE `clienti_pre_fat_for`.`id_cpff` = $idop;";
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
		global $servername; global $username; global $password; global $dbname;



		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 

		//$sql = "SELECT * FROM `riferimenti_fat_for` WHERE `402fattura` LIKE '$fatt402' AND `email` LIKE 'senzaemail@senzaemail.com' ";
		$sql = "UPDATE `priorityorder` SET `end_lavorazione` = '0' WHERE `priorityorder`.`ext_id_queue` = $idop;";
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
		
		
		
		
		
		
		
		
		
		
		
		
		

		
		

?>