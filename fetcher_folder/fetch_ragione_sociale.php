<?php


require_once('../dbconf.php');
//require_once('funzioni-dash_main.php');
//require_once('login-lista-user-db_main.php');
//require_once('login-err-non-valido_main.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


//SELECT targa_veicolo, atar_file, email_inviata_autofficina  FROM `ataraxia_main` where STATUS = 400





//fetch.php
$connect = mysqli_connect($servername, $username, $password, $dbname);
//$column = array("product.id", "product.name", "category.category_name", "product.price");
//GOOD    $query = "SELECT partite_aperte__Soc, partite_aperte__def_conto, partite_aperte__conto, partite_aperte__n_doc, partite_aperte__data_registrazione, partite_aperte__sc_netto, REPLACE(partite_aperte__importo, '-', '') as partite_aperte__importo, Sblocco_Partita, mese_anno FROM `a2a_durc_all_tab_sblocchi_unified`";
//$query = "select codice_stazione, time_stamp, targa_veicolo, atar_file, email_inviata_autofficina  FROM `ataraxia_main` ";
$query = "select codice_stazione, DATE_FORMAT(ts_in_dt,'%d/%m/%Y %T') as ts_in_dt_loc, DATE_FORMAT(dt_insert,'%d/%m/%Y %T') as dt_insert, targa_veicolo, atar_file, email_inviata_autofficina FROM `ataraxia_main` ";
//$query = "SELECT partite_aperte__Soc, partite_aperte__def_conto, partite_aperte__conto, partite_aperte__n_doc, partite_aperte__data_registrazione, partite_aperte__sc_netto, partite_aperte__importo, Sblocco_Partita, mese_anno FROM `a2a_durc_all_tab_sblocchi_unified` ";









$query .= " WHERE 1=1 ";

if(isset($_REQUEST["is_societa"])) 
{
	
	/*
	if (strpos($_REQUEST["is_societa"], "_")) {
		$splarray = explode("_",$_REQUEST["is_societa"]);
		$idxsplarr = 0;
		
		$query .= "AND ( ";
		foreach ($splarray as $singlesplarr) {
			
			if ($idxsplarr == 0) {$query .= "t1.name_society  like '".$singlesplarr."' ";}
			else { $query .= "OR t1.name_society  like '".$singlesplarr."' "; }
			$idxsplarr = $idxsplarr +1;
		}
		$query .= ") ";
		
	}
	else {$query .= "AND t1.name_society  like '".$_REQUEST["is_societa"]."' ";}
    */



}

$query .= " AND STATUS = 400 AND email_inviata_autofficina not like '' ";
//$query .= " AND t2.fatture_consegnate like '%attura consegnat%' AND t2.status_completed = 1 ";
//$query .= " order by t2.date_downloaded_fatture ";
//echo $query;
//die();


/*

if(isset($_POST["search"]["value"]))
{
 $query .= '(product.id LIKE "%'.$_POST["search"]["value"].'%" ';
 $query .= 'OR product.name LIKE "%'.$_POST["search"]["value"].'%" ';
 $query .= 'OR category.category_name LIKE "%'.$_POST["search"]["value"].'%" ';
 $query .= 'OR product.price LIKE "%'.$_POST["search"]["value"].'%") ';
}
else {
   $query .= "1 = 1";	
}



*/


if(isset($_POST["order"]))
{
 $query .= 'ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
}
else
{
 $query .= 'ORDER BY `ts_in_dt` DESC ';
}











$query1 = '';




if (isset($_POST["length"])) { 
			if($_POST["length"] != 1)
			{
			 $query1 .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
			}
}




//echo $query; die();

//$number_filter_row = mysqli_num_rows(mysqli_query($connect, $query));

$result = mysqli_query($connect, $query . $query1);

$data = array();



//name_society	
//partita_iva	
//tipo_documento	
//data_emissione	
//identificato_cliente	
//imponibile	
//imposta	
//sdi_file	
//date_downloaded_fatture	
//trig_download_metadati	
//id


while($row = mysqli_fetch_array($result))
{
 
 if (strtoupper(trim(mb_convert_encoding(dammiComunecitydaemail (trim($row["email_inviata_autofficina"])), 'UTF-8', 'UTF-8'))) == strtoupper(trim($_REQUEST["is_societa"]))) {
  
 $sub_array = array();
 $sub_array[] = mb_convert_encoding($row["codice_stazione"], 'UTF-8', 'UTF-8');
 $sub_array[] = mb_convert_encoding($row["ts_in_dt_loc"], 'UTF-8', 'UTF-8');
 $sub_array[] = mb_convert_encoding($row["dt_insert"], 'UTF-8', 'UTF-8');
 $sub_array[] = mb_convert_encoding($row["targa_veicolo"], 'UTF-8', 'UTF-8');
 $sub_array[] = mb_convert_encoding($row["atar_file"], 'UTF-8', 'UTF-8');
 $sub_array[] = get_ragione_sociale(mb_convert_encoding($row["email_inviata_autofficina"], 'UTF-8', 'UTF-8'));
 $sub_array[] = mb_convert_encoding($row["email_inviata_autofficina"], 'UTF-8', 'UTF-8');
 $sub_array[] = mb_convert_encoding(dammiComunecitydaemail ($row["email_inviata_autofficina"]), 'UTF-8', 'UTF-8');

// $sub_array[] = mb_convert_encoding($row["id"], 'UTF-8', 'UTF-8');

 $data[] = $sub_array;
 } // Chiudo if
} // Chiudo While

//SELECT partite_aperte__Soc, partite_aperte__def_conto, partite_aperte__conto, partite_aperte__n_doc, partite_aperte__data_registrazione, partite_aperte__sc_netto, partite_aperte__importo, Sblocco_Partita, mese_anno FROM `a2a_durc_all_tab_sblocchi_unified`


//$data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');

//echo '<pre>';
//print_r($data);
//echo '</pre>';

//die();

$number_filter_row = count($data);


if (isset($_REQUEST["draw"])) { $drw = $_REQUEST["draw"]; } else {$drw = "1";}

$output = array(
 "draw"    => intval($drw),
 "recordsTotal"  =>  get_all_data($connect),
 "recordsFiltered" => $number_filter_row,
 "data"    => $data
);


$myObj = new \stdClass();
//"draw"    => intval($_REQUEST["draw"]),
 //"recordsTotal"  =>  get_all_data($connect),
 //"recordsFiltered" => $number_filter_row,

 $myObj->draw = intval($drw); 
 $myObj->recordsTotal = get_all_data($connect); 
 $myObj->recordsFiltered = $number_filter_row; 
 $myObj->data = $data; 


 //$myJSON = json_encode(htmlspecialchars($myObj, ENT_QUOTES, 'UTF-8')); echo $myJSON; exit();

echo json_encode($output);

//echo json_last_error_msg();
//var_dump($data);








// Gestione Funzioni  




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
			} else { return "<font color='red' >CRISTALLI</font>"; }
		    
		    }
			
			$conn->close();
			
}



function get_all_data($connect)
{
 $query = "select codice_stazione, DATE_FORMAT(ts_in_dt,'%d/%m/%Y %T') as ts_in_dt_loc,DATE_FORMAT(dt_insert,'%d/%m/%Y %T') as dt_insert, targa_veicolo, atar_file, email_inviata_autofficina  FROM `ataraxia_main` where STATUS = 400 ";
 $result = mysqli_query($connect, $query);
 return mysqli_num_rows($result);
}


function give_the_next_to_trim() {	
		global $servername;
		global $username;
		global $password;
		global $dbname;
        //global $tab_main_pratiche;
		
		  $conn = new mysqli($servername, $username, $password, $dbname);
     	  if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error);  } 

		  $sql = "SELECT * FROM `provincia_template_link`";
		  //$sql = "SELECT `id_univoco` FROM `$tab_main_pratiche` where stato like 'Chiusa' and stato_pratica_web = 440";
		  $result = $conn->query($sql);

		if ($result->num_rows > 0) {
			
			//$row = $result->fetch_assoc();
			//$daritorno = $row["id_univoco"];
			//return $daritorno;
			
			// output data of each row
			$indic = 0;
			while($row = $result->fetch_assoc()) {
				
		                $daritorno[$indic]["id"] = $row["targa_veicolo"];
						$daritorno[$indic]["provincia"] = $row["atar_file"];
						$daritorno[$indic]["link"] = $row["email_inviata_autofficina"];
						$indic++;
			 } 
			  //Chiusura While
			   return $daritorno;	
			} else { return "0"; }
		    $conn->close();	
	}  //selectarrfromonetable
	
	
	
	
	
	
	function update_provincia($id,$provincia) {	
		global $servername;
		global $username;
		global $password;
		global $dbname;
        global $tab_main_pratiche;
		
		  $conn = new mysqli($servername, $username, $password, $dbname);
     	  if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error);  } 

		  
		  //$sql = "select * from accredia"; 
		  $sql = "UPDATE `provincia_template_link` SET `provincia` = '$provincia' WHERE `provincia_template_link`.`id` = $id;";
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
	



	function give_the_status_icon ($give_me_status,$qualetabella,$qualecolonna,$cheritorno) {	
		global $servername;
		global $username;
		global $password;
		global $dbname;

		$conn = new mysqli($servername, $username, $password, $dbname);
     	if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error);  } 

		//$sql = "SELECT html_stato FROM `tab_stati` where stato_lavorazione = ".$give_me_status;
		$sql = "SELECT $cheritorno FROM `cassette_value_meaning` where table_name like '$qualetabella' and column_name like '$qualecolonna' and value = ".$give_me_status;
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





        

         		
				
				function dammiComunecitydaemail ($emailinviataautofficina) {	
					global $servername; global $username; global $password; global $dbname;
					$conn = new mysqli($servername, $username, $password, $dbname);
					if ($conn->connect_error) {	die("Connection failed: " . $conn->connect_error);	} 
					
					$sql = "SELECT TRIM(CITTA) as comunecit FROM `canaliz_canaliz` where (OFFICINE1 like '%".$emailinviataautofficina."%' OR OFFICINE2 like '%".$emailinviataautofficina."%' OR OFFICINE3 like '%".$emailinviataautofficina."%' or OFFICINE4 like '%".$emailinviataautofficina."%' or CARROZZERIE1 like '%".$emailinviataautofficina."%' or CARROZZERIE2 like '%".$emailinviataautofficina."%' or CARROZZERIE3 like '%".$emailinviataautofficina."%' or CARROZZERIE4 like '%".$emailinviataautofficina."%' or GOMME like '%".$emailinviataautofficina."%' or CRISTALLI like '%".$emailinviataautofficina."%') limit 1";
					$result = $conn->query($sql);

					
							if ($result->num_rows > 0) {
								while($row = $result->fetch_assoc()) {
								  if (is_null($row['comunecit'])) {$comune = "No_CITY";}
								  else { $comune = $row['comunecit']; }
								  
								}  return $comune;
							} else {
								//echo "0 results";
								return "No_CITY";
							}
							  $conn->close();	
							
					}  //Chiusura dammiComunecit
					
					
					

					function dammiSomaDelComune ($comunerif) {	
					global $servername; global $username; global $password; global $dbname;
					$conn = new mysqli($servername, $username, $password, $dbname);
					if ($conn->connect_error) {	die("Connection failed: " . $conn->connect_error);	} 
					
					$sql = "SELECT SUM(carico) as sommaCarico FROM `capacity_diretti` where codice in (select CODICE_FORNITORE FROM `canaliz_rete` where COMUNE like '%".$comunerif."%')";
					$result = $conn->query($sql);

					
							if ($result->num_rows > 0) {
								while($row = $result->fetch_assoc()) {
								  if (is_null($row['sommaCarico'])) {$comune = "0";}
								  else { $comune = $row['sommaCarico']; }
								  
								}  return $comune;
							} else {
								//echo "0 results";
								return "0";
							}
							  $conn->close();	
							
					}  //Chiusura dammiSomaDelComune


?>