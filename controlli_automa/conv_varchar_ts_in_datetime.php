<?php

/*
Questo script si occupa di effettuare i controlli delle certificazioni SOA di riferimento.
Per l'acceso alla pratica occorre che la pratica sia in stato 2 (in lavorazione)


SELECT `id_univoco` FROM `main_backlog_pratiche_mik` where stato_soa = 2 limit 1



select
tab1.id_univoco,
if (instr(group_concat(tab1.cat), group_concat(tab2.categoria)) > 0, 1, 0) as categorie_congruenti
from (
    SELECT
        id_univoco,
        REPLACE(SUBSTRING(SUBSTRING_INDEX(REPLACE(nome_campo,' ',''), '-', 1),LENGTH(SUBSTRING_INDEX(REPLACE(nome_campo,' ',''), '-', 0)) + 1), '-', '') AS cat
    FROM pratiche_all_tab
    where
        indice_campo <> ''
        and indice_campo > 1
        and nome_tab like'%soa%'
) as tab1
left join (select categoria, id_univoco from soa_pratiche) as tab2
    on tab2.id_univoco = tab1.id_univoco AND tab1.cat = tab2.categoria
-- inner join main_backlog_pratiche_mik as main on tab1.id_univoco = main.id_univoco
where tab1.id_univoco = '01733830606_1_20180723200010'
group by tab1.id_univoco




SELECT nome_campo  FROM `pratiche_all_tab` WHERE `id_univoco` LIKE '01733830606_1_20180723200010' and nome_tab like '%soa%' and indice_campo not like "" and indice_campo > 1

SELECT categoria  FROM `soa_pratiche` WHERE `id_univoco` LIKE '01554760692_1_20180723195625'


*/


?>


<?php
//require_once('../dbconf.php');

require_once('/home/a2aqualifiche/public_html/dbconf.php');

$tab_main_pratiche = "main_backlog_pratiche_mik";


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>




<?php

$lista_id_univoco = give_the_next_soa_with_status_2();
echo "<br>";

//print_r($lista_id_univoco);

$bloccoadunelemento = 0;


//$os = array("Mac", "NT", "Irix", "Linux");
//if (in_array("Irix", $os)) {
//    echo "Trovato Irix";
//}else { echo "non trovato"; }



if ($lista_id_univoco != "0")
foreach ($lista_id_univoco as $id_univoco) {
    
		//if ($bloccoadunelemento == 0) {
			
			
			
		echo "<br><br>Nuova Lavorazione <br>";	
		echo $id_univoco."<br>";
		
		
		update_presa_in_carico($id_univoco,"soa");	
		//update_conclusione_controllo($id_univoco,"strorg_dipendenti");
		
		$lista_soa_sito_a2a = give_me_all_soa_for_id_unico_from_a2a($id_univoco);

		$lista_soa_sito_internet = give_me_all_soa_for_id_unico_from_internet($id_univoco);
		

		echo "Categorie Portale A2A <br>";
		print_r($lista_soa_sito_a2a);

		echo "<br> Categorie Portale Internet <br>";
		print_r($lista_soa_sito_internet);
        echo "<br>";

		
		$numericamente_a2a = count ($lista_soa_sito_a2a);
		$numericamente_internet = count ($lista_soa_sito_internet);
		
		$escluso = 0;
		if ($numericamente_a2a == 1 && ($lista_soa_sito_a2a[0] == "")) { $escluso = 2; /* Occorre fare l'update della pratica perchè non è presente alcun elemento da controllare */   }
		if ($numericamente_internet == 1 && ($lista_soa_sito_internet[0] == "")) { $escluso = 1; /* Occorre fare l'update della pratica perchè non è presente alcun elemento da controllare */  }
		
		if ($escluso == 0){
		if ($numericamente_internet >= $numericamente_a2a) {
			//Le certificazioni presenti su internet sono di più delle certificazioni presenti sul sito A2A quindi proseguo a cercare per inclusione
			
			            //Inserisco una variabile con il numero di elementi realmente presenti che devono corrispondere numericamente a tutto il numero di elementi
			            
						$numero_di_elementi_matchanti = 0;
						foreach ($lista_soa_sito_a2a as $cert_a2a) {
									  if (in_array($cert_a2a, $lista_soa_sito_internet)) {
										//echo "Cerficicazione Presente <br>";
										$numero_di_elementi_matchanti = $numero_di_elementi_matchanti + 1;
									  }else { /* echo "Certificazione non trovata <br>"; */ }
						} //Chiusura foreach $lista_soa_sito_a2a
							
							//Il seguente if è legato al foreach soprastante
							if ($numero_di_elementi_matchanti == $numericamente_a2a) {
								echo "Match certificazioni OK con stato 580"; update_stato_soa($id_univoco, "580");
							}else { echo "Match certificazioni fallito con stato 530"; update_stato_soa($id_univoco, "530"); }
							
            	//print_r($lista_soa_sito_a2a);
         		//print_r($lista_soa_sito_internet);


			} else {
			
			//Ho più certificazioni sul sito A2A che su internet quindi per forza sono incongruenti a tal fine faccio già l'uldate di incongruenza
			echo "Match certificazioni fallito con stato ad 530";
			update_stato_soa($id_univoco, "530");
			
		    } //Chiusura else secondario
		} 
		else {
			
			if ($escluso == 2){ 
				echo "Nessuna categoria dichiarata sul portale stato 880";
				update_stato_soa($id_univoco, "880");
			}
			
			// Chiusura else esclusioni
			if ($escluso == 1){  
				echo "Nessuna indicazione indicata su internet SOA stato ad 850";
				update_stato_soa($id_univoco, "850");
			}
			
		}
		
		
		


		
		
		//}  //Chiusura if da eliminare per sbloccare tutti i cicli	

//$bloccoadunelemento = $bloccoadunelemento +1;	


	
} //Chiusura Foreach
















?>





<?php

function give_the_next_soa_with_status_2() {	
		global $servername;
		global $username;
		global $password;
		global $dbname;
        global $tab_main_pratiche;
		
		  $conn = new mysqli($servername, $username, $password, $dbname);
     	  if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error);  } 

		  $sql = "SELECT `id_univoco` FROM `$tab_main_pratiche` where stato_soa = 2";
		  $result = $conn->query($sql);

		if ($result->num_rows > 0) {
			
			//$row = $result->fetch_assoc();
			//$daritorno = $row["id_univoco"];
			//return $daritorno;
			
			// output data of each row
			
			while($row = $result->fetch_assoc()) {
				
		                $daritorno[] = $row["id_univoco"];                                                             
			
			 } 
			  //Chiusura While
			   return $daritorno;	
			} else { return "0"; }
		    $conn->close();	
	}  //selectarrfromonetable
	
	
	
	
	
	
	function give_me_all_soa_for_id_unico_from_a2a($id_univoco) {	
		global $servername;
		global $username;
		global $password;
		global $dbname;

		  $conn = new mysqli($servername, $username, $password, $dbname);
     	  if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error);  } 

		  $sql = "SELECT nome_campo FROM `pratiche_all_tab` WHERE `id_univoco` LIKE '$id_univoco' and nome_tab like '%soa%' and indice_campo not like '' and indice_campo > 1 and (valore_campo not like 'Nessuna certificazione') AND (valore_campo not like 'N.D.')";
		  $result = $conn->query($sql);

		if ($result->num_rows > 0) {
			
			//$row = $result->fetch_assoc();
			//$daritorno = $row["id_univoco"];
			//return $daritorno;
			// output data of each row
			
			while($row = $result->fetch_assoc()) {
				        
						$pieces = explode("-", $row["nome_campo"]);
						
		                $daritorno[] =  trim(str_replace(" ", "", $pieces[0]));
			
			 } 
			  //Chiusura While
			  return $daritorno;	
			} else { return "0"; }
		    $conn->close();	
	}  //
	
	
	
	function give_me_all_soa_for_id_unico_from_internet($id_univoco) {	
		global $servername;
		global $username;
		global $password;
		global $dbname;

		  $conn = new mysqli($servername, $username, $password, $dbname);
     	  if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error);  } 

		  $sql = "SELECT categoria  FROM `soa_pratiche` WHERE `id_univoco` LIKE '$id_univoco'";
		  $result = $conn->query($sql);

		if ($result->num_rows > 0) {
			
			//$row = $result->fetch_assoc();
			//$daritorno = $row["id_univoco"];
			//return $daritorno;
			// output data of each row
			
			while($row = $result->fetch_assoc()) {
				        
						//$pieces = explode("-", $row["categoria"]);
						$pieces = explode("-", $row["categoria"]);
		                $daritorno[] =  trim(str_replace(" ", "", $pieces[0]));
						
		                //$daritorno[] =  trim(str_replace(" ", "", $row["categoria"]));
			
			 } 
			  //Chiusura While
			  return $daritorno;	
			} else { return "0"; }
		    $conn->close();	
	}  //
	
	
	
	
	function update_stato_soa($id_univoco, $stato_verificato) {	
		global $servername;
		global $username;
		global $password;
		global $dbname;
        global $tab_main_pratiche;
		
		  $conn = new mysqli($servername, $username, $password, $dbname);
     	  if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error);  } 

		  
		   
		  $sql = "UPDATE `$tab_main_pratiche` SET `stato_soa` = '$stato_verificato' WHERE `id_univoco` LIKE '$id_univoco';";
		  //update_presa_in_carico($id_univoco,"soa");	
		  update_conclusione_controllo($id_univoco,"soa");
		  $result = $conn->query($sql);
		  
		  
        /*
		if ($result->num_rows > 0) {
			
			//$row = $result->fetch_assoc();
			//$daritorno = $row["id_univoco"];
			//return $daritorno;
			// output data of each row
			
			while($row = $result->fetch_assoc()) {
				        
						//$pieces = explode("-", $row["categoria"]);
						
		                $daritorno[] =  trim(str_replace(" ", "", $row["categoria"]));
			
			 } 
			  //Chiusura While
			  return $daritorno;	
			} else { return 0; }   */
		    $conn->close();	
	}  //
	
	
	
	function update_presa_in_carico($id_univoco,$grouptab) {	
		global $servername; global $username; global $password; global $dbname; global $tab_main_pratiche;
		$conn = new mysqli($servername, $username, $password, $dbname);   if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error);  } 
		$urireq = $_SERVER['REQUEST_URI']; $nowaccess = date("Y-m-d H:i:s"); 
		
		  //$sql = "select * from accredia"; 
		  $sql = "UPDATE `$tab_main_pratiche` SET `hostname_$grouptab` = '$urireq', `init_time_$grouptab` = '$nowaccess' WHERE `$tab_main_pratiche`.`id_univoco` like '$id_univoco';";
		  
		  return $result = $conn->query($sql);
		    $conn->close();	
	}   //Chiusura funzione update_presa_in_carico
	
    
    function update_conclusione_controllo($id_univoco,$grouptab) {	
		global $servername; global $username; global $password; global $dbname; global $tab_main_pratiche;
		$conn = new mysqli($servername, $username, $password, $dbname);   if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error);  } 
		$urireq = $_SERVER['REQUEST_URI'];   $nowaccess = date("Y-m-d H:i:s"); 

		  //$sql = "select * from accredia"; 
		  $sql = "UPDATE `$tab_main_pratiche` SET `hostname_$grouptab` = '$urireq', `end_time_$grouptab` = '$nowaccess' WHERE `$tab_main_pratiche`.`id_univoco` like '$id_univoco';";
		  
		  return $result = $conn->query($sql);
		    $conn->close();	
	}	//Chiusura funzione update_conclusione_controllo 
	
	
	
	
?>
