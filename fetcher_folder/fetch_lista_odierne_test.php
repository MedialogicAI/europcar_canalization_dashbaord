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
//$query = "select codice_stazione,DATE_FORMAT(ts_in_dt,'%d/%m/%Y %T') as ts_in_dt, DATE_FORMAT(dt_insert,'%d/%m/%Y %T') as dt_insert_loc, targa_veicolo, atar_file, status  FROM `ataraxia_main` ";

$query="SELECT codice_stazione, DATE_FORMAT(t1.ts_in_dt,'%d/%m/%Y %T') as tsindtloc, DATE_FORMAT(t1.start_processing,'%d/%m/%Y %T') as start_processing_loc, t1.targa_veicolo, t1.atar_file, t2.long_description, t1.status FROM ataraxia_main as t1 LEFT JOIN tab_status as t2 ON t1.status = t2.actual_status";
//$query = "SELECT partite_aperte__Soc, partite_aperte__def_conto, partite_aperte__conto, partite_aperte__n_doc, partite_aperte__data_registrazione, partite_aperte__sc_netto, partite_aperte__importo, Sblocco_Partita, mese_anno FROM `a2a_durc_all_tab_sblocchi_unified` ";



$query .= " WHERE 1=1 ";

$order = array("codice_stazione","ts_in_dt", "start_processing","targa_veicolo","atar_file","long_description"); 
//print_r($order);

$field1 = "";
if(isset($_REQUEST["search"]) && isset($_REQUEST["field"])) 
{
	//echo $_REQUEST["field"];die();
	$field = trim($order[$_REQUEST["field"]]);
	//echo $field;
		if($_REQUEST["field"] == 1 or $_REQUEST["field"] == 2){
			
			$date = str_replace("/","-",$_REQUEST["search"]);
			$key = date('Y-m-d',strtotime($date));
			
		}
		else {
			$key = trim($_REQUEST["search"]);
		}
	
	$query .= "AND ".$field." like '%".$key."%'";
	//echo $query; die();
} else 

$query .= " AND start_processing BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d') AND NOW() or start_processing is null and dt_insert >= '2019-04-01'";
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
	$indiceOrdine = $_POST['order']['0']['column'];
	
	//$query .= 'ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
	$query .= 'ORDER BY '.$order[$indiceOrdine].' '.$_POST['order']['0']['dir'].' ';
}
else
{
 $query .= 'order by ts_in_dt desc ';
}



$query1 = '';




/*if (isset($_POST["length"])) { 
	if($_POST["length"] != 1)
	{
		$query1 .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
	}
}*/




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
 
 if($row["status"]>0 && $row['status']<400){
	 $sub_array[] = 'Processing pratices';
 }
 if($row["status"] > 400 && $row["status"] != 410 && $row["status"] != 440 && $row["status"] != 460 ){
	$sub_array[] = 'Manually addressed';
 }
 else{
	 $sub_array[] = mb_convert_encoding($row["long_description"], 'UTF-8', 'UTF-8');
 }
 
 if($row["status"] == 0){
	$sub_array[] = '<button type="button" style="width: 100%;" data-atar="'.$row["atar_file"].'" class="putManually">Manage manually</button>';
 }
 else{
	 $sub_array[] = ' ';
 }
	
 
 //$sub_array[] = mb_convert_encoding($row["long_description"], 'UTF-8', 'UTF-8');
 
 //$sub_array[] = give_the_status_icon (mb_convert_encoding($row["status"], 'UTF-8', 'UTF-8'),'tab_status','actual_status','long_description') ;



// $sub_array[] = mb_convert_encoding($row["id"], 'UTF-8', 'UTF-8');

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
 //query = "select codice_stazione,DATE_FORMAT(ts_in_dt,'%d/%m/%Y %T') as ts_in_dt, DATE_FORMAT(dt_insert,'%d/%m/%Y %T') as dt_insert_loc, targa_veicolo, atar_file, status  FROM `ataraxia_main` where dt_insert >= DATE_SUB(CURDATE(),INTERVAL 1 DAY) order by dt_insert desc ";
 
 //$query="select codice_stazione,DATE_FORMAT(ts_in_dt,'%d/%m/%Y %T') as ts_in_dt, DATE_FORMAT(dt_insert,'%d/%m/%Y %T') as dt_insert_loc, targa_veicolo, atar_file, status  FROM `ataraxia_main` WHERE dt_insert BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d') AND NOW() order by dt_insert desc ";
 $query="SELECT codice_stazione, DATE_FORMAT(t1.ts_in_dt,'%d/%m/%Y %T') as tsindtloc, DATE_FORMAT(t1.start_processing,'%d/%m/%Y %T') as start_processing_loc, t1.targa_veicolo, t1.atar_file, t2.long_description, t1.status FROM ataraxia_main as t1 LEFT JOIN tab_status as t2 ON t1.status = t2.actual_status where start_processing BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d') AND NOW() or start_processing is null and dt_insert >= '2019-04-01'";
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