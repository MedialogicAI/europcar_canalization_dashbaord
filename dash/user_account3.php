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

//elencoComuni();

//die(print_r(elencoComuni()));


if (!is_numeric($_GET['multi'])){ exit("Value Multiselect of chars not selected");  }

$chars=$_GET['multi'];




?>












			

 <?php  require_once('../header-pag.php');   ?>  
 

	<script>
	
	//$(document).ready(function() {
	//	$('.comuni').select2();
	//});
	
	</script>
 
   <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>-->
   
  <script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU=" crossorigin="anonymous"></script>
  
  
  <script src="../bower_components/select2/select2.js"></script>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="/bower_components/Ionicons/css/ionicons.min.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="/bower_components/jvectormap/jquery-jvectormap.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="/dist/css/skins/_all-skins.min.css">
    
  <link rel="stylesheet" href="../bower_components/select2/select2.css">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->





<!-- Multiselect and Datatable Library -->
<link rel="stylesheet" href="/lib_ms/jquery-ui.min.css">
<script src="/lib_ms/jquery-ui.min.js"></script>
<link rel="stylesheet" href="/lib_ms/jquery.multiselect.css">
<script src="/lib_ms/src/jquery.multiselect.js"></script>
<link rel="stylesheet" href="/lib_ms/jquery.multiselect.filter.css">
<script src="/lib_ms/src/jquery.multiselect.filter.js"></script>

<script src="/lib_ms/i18n/jquery.multiselect.it.js"></script>
<script src="/lib_ms/i18n/jquery.multiselect.filter.it.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> 

<link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>




<!-- Charts JS library -->

   <script src="https://unpkg.com/ionicons@4.5.5/dist/ionicons.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.js"></script>
   

  <!-- Google Font -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
 
               <div id="page-wrapper" >

            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                     <h2>Account</h2>   
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />
                  
				


				
				<div id='msg_out_old'>
				
				
				<?php 
		          //Array_ che contiene i dati relativi alla tabella utenti_durc
		          //var_dump($utenepriv);
		        ?>
				
				
				

				
				<div class="container pull-left">
				<div class="row">
				
				
				
				
				
				
				
				
				</div>    <!-- Questo è il div di fine riga ROW -->
				
				</div>    <!-- Questo è il div del container  -->
				

				
				
				
				<br>
				
				
				<div class="row">
					<div class="col-md-12" id="nselection" >
						<div class="col-md-4">
						    
							<label for="selectComuni"> Seleziona comune </label>
							<select class="form-control comuni" name="selectComuni" onchange='mydatatablecity($(this).children(":selected").attr("id"))' >
							
								<?php 
								    $arr_unico_comuni_capacita = elencoComuni($chars);
									
									//var_dump($arr_unico_comuni_capacita);
									//exit();
									
									$idxselafield = 0;
									foreach ($arr_unico_comuni_capacita as $comune) {
										    if ($idxselafield == 0) { echo "<option id='selezione'> Select a City </option>"; }
										if ($comune['somma'] != "0") {
											$sommaquattrocentook = tuttelequattrocento ($comune['name']);
											if ($sommaquattrocentook > 0){
                                          	echo " <option  id='".$comune['name']."' style='color: darkorange;'> ". $comune['name']." - ".$sommaquattrocentook."/".$comune['somma']." </option>";
											}
											else {
                                          	echo "<option  id='".$comune['name']."'>". $comune['name']." - ".$sommaquattrocentook."/".$comune['somma']."</option>";
											}
											
										}
										
										$idxselafield++;
									}
								?>
								
							</select>
						
						</div>
					</div>



                   <div class="col-md-12" id="nascondoprecitta">
					<div class="table-responsive">
	    <table id="product_data" class="table table-bordered table-striped">
		 <thead>
		  <tr>
			<th style="min-width: 55px" class="align-top" >
			 Station code
			</th>
			<!--qui-->
			<th style="min-width: 55px" class="align-top" >
			 Requested date
			</th>
			<th style="min-width: 55px" class="align-top" >
			 Start Process Date
			</th>
			<th style="min-width: 55px" class="align-top" >
			 Registration Number
			</th>
			<th style="width: 200px;" class="align-top" >
				Atar File
			</th>	
			<th style="min-width: 55px" class="align-top" >
			 Supplier
			</th>
			<th class="align-top" >
				Supplier e-mail
			</th>	
			<th class="align-top" >
				City
			</th>
		   </tr>
		 </thead>
		</table>
				   </div>
                   </div>
				   
				   
				   
					<div class="col-md-12">
						
							<canvas id="capacityChart" style="height: 230px; width: 680px;" width="680" height="230"></canvas>
					
					</div>
				

				  
                 <!-- /. ROW  -->           
            </div>
             <!-- /. PAGE INNER  -->
        
             </div>
             <!-- /. PAGE WRAPPER  -->
			 
			 
			 
			 
			 
     

	
	
	
	<?php
	//Preparo l'array per il grafico comuni capacità
	
	
	//foreach($arr_unico_comuni_capacita as $com) {
		
		
		//var_dump ($arr_unico_comuni_capacita);
		//exit();
		
		foreach ($arr_unico_comuni_capacita as $singola) {
			if ($singola['somma'] != "0"){
			  $solocitta[] = $singola['name'];
			  $solosomma[] = $singola['somma'];
			}
		}

		
        
		//echo "quisolocitta";
		//print_r ($solosomma);
		
	    //exit();
		$nomicosecitta =  "\"";
		$nomicosecitta .= implode("\",\"",$solocitta);
		$nomicosecitta .=  "\"";
		
		
		
		$sommacosecitta .= implode(",",$solosomma);
		
		
		//var_dump($arr_unico_comuni_capacita);
		
		//exit();
		
		
		
	//}
	
	
	?>
	
	
	<script>
		var ctx = document.getElementById("capacityChart").getContext('2d');
		var myChart = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: [<?php echo $nomicosecitta; ?>],
				datasets: [{
					label: '# Availability',
					data: [<?php echo $sommacosecitta; ?>],
					//backgroundColor: [
					//	'rgba(255, 99, 132, 0.2)',
					//	'rgba(54, 162, 235, 0.2)',
					//	'rgba(255, 206, 86, 0.2)',
					//	'rgba(75, 192, 192, 0.2)',
					//	'rgba(153, 102, 255, 0.2)',
					//	'rgba(255, 159, 64, 0.2)'
					//],
					//borderColor: [
					//	'rgba(255,99,132,1)',
					//	'rgba(54, 162, 235, 1)',
					//	'rgba(255, 206, 86, 1)',
					//	'rgba(75, 192, 192, 1)',
					//	'rgba(153, 102, 255, 1)',
					//	'rgba(255, 159, 64, 1)'
					//],
					borderWidth: 1
				}]
			},
			options: {
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero:true			                
						}
					}],
					xAxes: [{
						ticks: {
							display:false
						}
					}]
				}	        
			}
		});
	</script>

	



<script type="text/javascript" language="javascript" >
var elementioptions = [];



function load_data(is_societa)
 {
  var dataTable = $('#product_data').DataTable({
   "processing":true,
    language: {
        "processing": "Caricamento in corso. Attendere Prego..."
    },
   "serverSide":true,
   "order":[],
   "ajax":{
    url:"/fetcher_folder/fetch_single_city.php",
    type:"GET",
    data:{is_societa:is_societa}
   },
   "columnDefs":[
    {
     "targets":[0],
     "orderable":false,
    },
	{
     "targets":[1],
     "orderable":false,
    },
	{
     "targets":[2],
     "orderable":false,
    },
		{
     "targets":[3],
     "orderable":false,
    },
	{
     "targets":[4],
     "orderable":false,
    },
	{
     "targets":[5],
     "orderable":false,
    }
	],
  });
  
  
  //$( "#product_data_length" ).prepend( "<button type='button' class='btn btn-success' id='applicafilterfsc' style='margin-bottom: 4px; margin-right: 30px; ' >Applica Filtro</button> <select name='societa' id='societa' class='form-control'> <optgroup label='Società'> <?php echo $opzione; ?> </optgroup></select>");
  //$( "#societa_ms" ).append( '<div > <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"><button class="btn" onclick="location.href=\'/fetch_download_durc.php\';" ><i class="fa fa-download"></i> Scarica tutte le partite aperte </button></div>');
  
  
 }




$(document).ready(function(){
 //load_data();

 $(document).on('change', '#societa', function(){
  var societa = $(this).val();
  //$('#product_data').DataTable().destroy();
  if(societa != '')
  {
   //load_data(societa);
  }
  else
  { load_data(); }
 }); 
});    // Chiusura Document.ready

</script>



   <script> 
      $( "#nascondoprecitta" ).hide();
	  
	  
	  function mydatatablecity (idcitta) {
		  
		  if (idcitta != 'selezione') {
		    //alert(idcitta);
		    $( "#nascondoprecitta" ).show();
            $( "#capacityChart" ).hide();
			$( "#nselection" ).hide();
             	
			load_data(idcitta);
		  }
			  
		  
		  
	  } //Chiusura funzione javascript
	  
	  
	  
   </script>

	
		 
		 
		 
<?php  require_once('../footer-pag.php');   ?>		 



 
				 
				 
				 
				 
				 <?php
				 
				 
				 
				 
				 //Funzioni di questa pagina
				 
				 function conta_righe_con_stato ($status) {	
					global $servername; global $username; global $password; global $dbname;
					$conn = new mysqli($servername, $username, $password, $dbname);
     				if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 


					//$sql = "SELECT * FROM `riferimenti_fat_for` WHERE `402fattura` LIKE '$fatt402' AND `email` LIKE 'senzaemail@senzaemail.com' ";
					$sql = "SELECT count(*) FROM `ataraxia_main` where status = $status ";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						//return $result->num_rows;						
						 $row = $result->fetch_row();
						  return $row[0];
					} else { return 0; }
					$conn->close();	
						
					} // Chiusura Funzione
					
					
				function conta_righe_totali() {	
					global $servername; global $username; global $password; global $dbname;
					$conn = new mysqli($servername, $username, $password, $dbname);
     				if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 


					//$sql = "SELECT * FROM `riferimenti_fat_for` WHERE `402fattura` LIKE '$fatt402' AND `email` LIKE 'senzaemail@senzaemail.com' ";
					$sql = "SELECT count(*) FROM `ataraxia_main`";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						//return $result->num_rows;						
						 $row = $result->fetch_row();
						  return $row[0];
					} else { return 0; }
					$conn->close();	
						
					} // Chiusura Funzione
					
				
				
				function elencoComuni ($rangelettere) {	
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
					
					
					
					switch ($rangelettere) {
						case "1":
							//Range di comuni tra la A e la F
							$sql = "select COMUNE FROM `canaliz_rete` where (TRIM(`canaliz_rete`.COMUNE) like 'A%' OR TRIM(`canaliz_rete`.COMUNE) like 'B%' OR TRIM(`canaliz_rete`.COMUNE) like 'C%' OR TRIM(`canaliz_rete`.COMUNE) like 'D%' OR TRIM(`canaliz_rete`.COMUNE) like 'E%' OR TRIM(`canaliz_rete`.COMUNE) like 'F%') GROUP BY TRIM(`canaliz_rete`.COMUNE) order by TRIM(`canaliz_rete`.`COMUNE`)";
							break;
						case "2":
							//Range di comuni tra la G e la M
							$sql = "select COMUNE FROM `canaliz_rete` where (TRIM(`canaliz_rete`.COMUNE) like 'G%' OR TRIM(`canaliz_rete`.COMUNE) like 'H%' OR TRIM(`canaliz_rete`.COMUNE) like 'I%' OR TRIM(`canaliz_rete`.COMUNE) like 'L%' OR TRIM(`canaliz_rete`.COMUNE) like 'M%') GROUP BY TRIM(`canaliz_rete`.COMUNE) order by TRIM(`canaliz_rete`.`COMUNE`)";
							break;
						case "3":
							//Range di comuni tra la N e la R
							$sql = "select COMUNE FROM `canaliz_rete` where (TRIM(`canaliz_rete`.COMUNE) like 'N%' OR TRIM(`canaliz_rete`.COMUNE) like 'O%' OR TRIM(`canaliz_rete`.COMUNE) like 'P%' OR TRIM(`canaliz_rete`.COMUNE) like 'Q%' OR TRIM(`canaliz_rete`.COMUNE) like 'R%') GROUP BY TRIM(`canaliz_rete`.COMUNE) order by TRIM(`canaliz_rete`.`COMUNE`)";
							break;
						case "4":
							//Range di comuni tra la S e la Z
							$sql = "select COMUNE FROM `canaliz_rete` where (TRIM(`canaliz_rete`.COMUNE) like 'S%' OR TRIM(`canaliz_rete`.COMUNE) like 'T%' OR TRIM(`canaliz_rete`.COMUNE) like 'U%' OR TRIM(`canaliz_rete`.COMUNE) like 'V%' OR TRIM(`canaliz_rete`.COMUNE) like 'Z%') GROUP BY TRIM(`canaliz_rete`.COMUNE) order by TRIM(`canaliz_rete`.`COMUNE`)";
							break;	
							
					}
					
					
									
					
					
					$result = $conn->query($sql);

					
					if ($result->num_rows > 0) {
					
						
						$i = 0;
						
						while($row = $result->fetch_assoc()) {
							if(!(trim($row["COMUNE"]) == "")){
								
								$comune[$i]['name'] = trim($row["COMUNE"]);
								
								$questocomune = trim($row['COMUNE']);
								
								
								
								

								
								
								
								/*
							    $resultconnsomma = $conn->query($sqlsomma);
								//var_dump($resultconnsomma); die();
								if ($resultconnsomma->num_rows > 0) {
										//return $result->num_rows;						
										 //$rowsomma = $resultconnsomma->fetch_row();
										  $totsommacomune = $resultconnsomma['sommaCarico'];
									    exit("qui dentro:".$totsommacomune);
									} else { $totsommacomune = "0"; }
								*/
								
								
								
								
								
								
								$comune[$i]['somma'] = dammiSomaDelComune($questocomune);
								
								
								$i = $i +1;
							}
						}
						
						return $comune;
						
						
					} else {
						//echo "0 results";
						return 0;
					}
					$conn->close();	
						
					}  //selectarrfromonetable
					
					
					
					
					
					
					
					
					
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
					
				
                   	function tuttelequattrocento ($ricercacomune) {	
					global $servername; global $username; global $password; global $dbname;
					$conn = new mysqli($servername, $username, $password, $dbname);
					if ($conn->connect_error) {	die("Connection failed: " . $conn->connect_error);	} 
					
					$sql = "select email_inviata_autofficina FROM `ataraxia_main` WHERE 1=1 AND STATUS = 400 AND email_inviata_autofficina not like ''";
					$result = $conn->query($sql);

					        $comune = 0;
							if ($result->num_rows > 0) {
								while($row = $result->fetch_assoc()) {
								  if (is_null($row['email_inviata_autofficina'])) {$comune = "0";}
								  else { 
								    if (dammiComunecitydaemail ($row['email_inviata_autofficina']) == $ricercacomune) { $comune = $comune + 1; }
								  } // chiusura else
								  
								}  return (string)$comune;
							} else {
								//echo "0 results";
								return "0";
							}
							  $conn->close();	
							
					}  //Chiusura dammiSomaDelComune
                
					
					
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
					

					
					
					
					
		
?>












