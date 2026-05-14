<?php


require_once('../dbconf.php');
//require_once('funzioni-dash_main.php');
//require_once('login-lista-user-db_main.php');
//require_once('login-err-non-valido_main.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


//SELECT targa_veicolo, atar_file, status  FROM `ataraxia_main` where STATUS = 400





//fetch.php
$connect = mysqli_connect($servername, $username, $password, $dbname);
//$column = array("product.id", "product.name", "category.category_name", "product.price");
//GOOD    $query = "SELECT partite_aperte__Soc, partite_aperte__def_conto, partite_aperte__conto, partite_aperte__n_doc, partite_aperte__data_registrazione, partite_aperte__sc_netto, REPLACE(partite_aperte__importo, '-', '') as partite_aperte__importo, Sblocco_Partita, mese_anno FROM `a2a_durc_all_tab_sblocchi_unified`";
$query = "SELECT 
    codice_stazione, 
    DATE_FORMAT(t1.ts_in_dt,'%d/%m/%Y %T') as tsindtloc, 
    DATE_FORMAT(t1.start_processing,'%d/%m/%Y %T') as start_processing_loc, 
    t1.targa_veicolo as targa_veicolo, 
    t1.atar_file as atar_file, 
    t2.long_description as long_description, 
    t1.email_inviata_autofficina as email,
    t3.ragione_sociale as supplier,
	t1.observations as observations,
	t1.status
FROM 
	ataraxia_main as t1 
    
inner JOIN 
	tab_status as t2 
	ON 
	t1.status = t2.actual_status

left JOIN	
	canaliz_rete as t3
    ON
    t3.EMAIL  = t1.email_inviata_autofficina

";
//$query = "SELECT partite_aperte__Soc, partite_aperte__def_conto, partite_aperte__conto, partite_aperte__n_doc, partite_aperte__data_registrazione, partite_aperte__sc_netto, partite_aperte__importo, Sblocco_Partita, mese_anno FROM `a2a_durc_all_tab_sblocchi_unified` ";


$query .= " WHERE 1=1 AND t1.status != 0 ";

$order = array("codice_stazione","ts_in_dt", "start_processing","targa_veicolo","atar_file","long_description","email","email_inviata_autofficina");

if(isset($_REQUEST["search"]) && isset($_REQUEST["field"])) 
{
	$field = trim($order[$_REQUEST["field"]]);
	
	
	if($_REQUEST["field"] == 1 or $_REQUEST["field"] == 2){
			
		$date = str_replace("/","-",$_REQUEST["search"]);
		$key = date('Y-m-d',strtotime($date));
		
	}
	else {
		$key = trim($_REQUEST["search"]);
	}
 
	$query .= "AND ".$field." like '%".$key."%'";


}

$query .= "AND t1.dt_insert BETWEEN ('2019-04-01') AND NOW() ORDER BY t1.dt_insert DESC";


$query1 = '';




if (isset($_POST["length"])) { 
			if($_POST["length"] != 1)
			{
			 $query1 .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
			}
}




//echo $query; die();

$number_filter_row = mysqli_num_rows(mysqli_query($connect, $query));

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
  
 $sub_array = array();

	$sub_array[] = strtoupper(mb_convert_encoding($row["codice_stazione"], 'UTF-8', 'UTF-8'));
	$sub_array[] = mb_convert_encoding($row["tsindtloc"], 'UTF-8', 'UTF-8');
	$sub_array[] = mb_convert_encoding($row["start_processing_loc"], 'UTF-8', 'UTF-8');
	$sub_array[] = strtoupper(mb_convert_encoding($row["targa_veicolo"], 'UTF-8', 'UTF-8'));
	$sub_array[] = mb_convert_encoding($row["atar_file"], 'UTF-8', 'UTF-8');
	$sub_array[] = mb_convert_encoding($row["observations"], 'UTF-8', 'UTF-8');
	//$sub_array[] = give_the_status_icon (mb_convert_encoding($row["status"], 'UTF-8', 'UTF-8'),'tab_status','actual_status','long_description') ;
	$sub_array[] = mb_convert_encoding($row["supplier"], 'UTF-8', 'UTF-8');
	$sub_array[] = mb_convert_encoding($row["email"], 'UTF-8', 'UTF-8');


 $data[] = $sub_array;
}

//SELECT partite_aperte__Soc, partite_aperte__def_conto, partite_aperte__conto, partite_aperte__n_doc, partite_aperte__data_registrazione, partite_aperte__sc_netto, partite_aperte__importo, Sblocco_Partita, mese_anno FROM `a2a_durc_all_tab_sblocchi_unified`


//$data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');

//echo '<pre>';
//print_r($data);
//echo '</pre>';

//die();

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



function get_all_data($connect)
{
 $query = "select codice_stazione, DATE_FORMAT(ts_in_dt,'%d/%m/%Y %T') as ts_in_dt_loc, DATE_FORMAT(start_processing,'%d/%m/%Y %T') as start_processing_loc, targa_veicolo, atar_file, email_inviata_autofficina, status  FROM `ataraxia_main` WHERE dt_insert BETWEEN ('2019-04-01') AND NOW()";
 $result = mysqli_query($connect, $query);
 return mysqli_num_rows($result);
}


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




?>