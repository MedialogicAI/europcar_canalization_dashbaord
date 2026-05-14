<?php
require_once('../dbconf.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$connect = mysqli_connect($servername, $username, $password, $dbname);

$query = "SELECT
			codice_stazione,
			DATE_FORMAT(t1.ts_in_dt,'%d/%m/%Y %T') as tsindtloc,
			DATE_FORMAT(t1.start_processing,'%d/%m/%Y %T') as start_processing_loc,
			t1.targa_veicolo, t1.atar_file,
			-- t2.long_description,
			t1.observations as observations,
			t1.status
		FROM ataraxia_main as t1 LEFT JOIN tab_status as t2 ON t1.status = t2.actual_status";

$query .= " WHERE 1=1 ";

$order = array("codice_stazione","ts_in_dt", "start_processing","targa_veicolo","atar_file","long_description"); 

$field1 = "";
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
else{
    $query .= " AND start_processing BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d') AND NOW() or start_processing is null";
}

$query .= " and dt_insert >= '2019-04-01'";


if(isset($_POST["order"]))
{
	$indiceOrdine = $_POST['order']['0']['column'];
	$query .= 'ORDER BY '.$order[$indiceOrdine].' '.$_POST['order']['0']['dir'].' ';
}
else
{
 $query .= 'order by ts_in_dt desc ';
}

$query1 = '';

$number_filter_row = mysqli_num_rows(mysqli_query($connect, $query));
$result = mysqli_query($connect, $query . $query1);
$data = array();

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
	 $sub_array[] = mb_convert_encoding($row["observations"], 'UTF-8', 'UTF-8');
 }
 
 if($row["status"] == 0){
	$sub_array[] = '<button type="button" style="width: 100%;" data-atar="'.$row["atar_file"].'" class="putManually">Manage manually</button>';
 }
 else{
	 $sub_array[] = ' ';
 }

 $data[] = $sub_array;
}

if (isset($_REQUEST["draw"])) { $drw = $_REQUEST["draw"]; } else {$drw = "1";}

$output = array(
 "draw"    => intval($drw),
 "recordsTotal"  =>  get_all_data($connect),
 "recordsFiltered" => $number_filter_row,
 "data"    => $data
);


$myObj = new \stdClass();
 $myObj->draw = intval($drw); 
 $myObj->recordsTotal = get_all_data($connect); 
 $myObj->recordsFiltered = $number_filter_row; 
 $myObj->data = $data; 

echo json_encode($output);

function get_all_data($connect)
{
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