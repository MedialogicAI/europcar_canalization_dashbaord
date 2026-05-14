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
  <link rel="stylesheet"href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
 
               <div id="page-wrapper" >

            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                     <h2>Test movement</h2>   
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />
			
				<div class="col-md-12">
					
					<?php
					
					
						global $servername; global $username; global $password; global $dbname;
						$conn = new mysqli($servername, $username, $password, $dbname);
						if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 


						//$sql = "SELECT * FROM `riferimenti_fat_for` WHERE `402fattura` LIKE '$fatt402' AND `email` LIKE 'senzaemail@senzaemail.com' ";
						$sql = "SELECT targa_veicolo, wsm_bsm_dt, status FROM `ataraxia_main` WHERE NOW() BETWEEN (wsm_bsm_dt + INTERVAL 1 DAY) AND NOW() AND status = 402";
						$result = $conn->query($sql);

						if ($result->num_rows > 0) {
							//return $result->num_rows;						
							while($row = $result->fetch_assoc()) {
								
								echo $row['targa_veicolo'] . '<br>';
								$sqlMovement = "SELECT * FROM `movement` WHERE REGISTRATION_NUMBER like '".$row['targa_veicolo']."'";
								$resultMovement = $conn->query($sqlMovement);
								
								
								if ($resultMovement->num_rows > 0){
									
									echo 'targa trovata non fare nulla<br><hr>';
									
								}
								
								else{
									
									//$rowmov = $resultMovement->fetch_assoc();
									//print_r($rowmov);
									
									echo 'targa non trovata  - aggiorna record<br/><hr>';
									$sqlUpdate = "UPDATE ataraxia_main SET status='403' WHERE targa_veicolo='".$row['targa_veicolo']."'";
									
									if ($conn->query($sqlUpdate) === TRUE) {
										echo "Record updated successfully";
									} else {
										echo "Error updating record: " . $conn->error;
									}
									
								}
								
								//$row[] = $rows;
							}
							
							
						} 
						
						else { 

							echo 'non ci sono risultati';
						}
						$conn->close();	
							
					
					?>	
			
				</div>
			
			</div>	  

		 
		 
<?php  require_once('../footer-pag.php');   ?>		 

			












