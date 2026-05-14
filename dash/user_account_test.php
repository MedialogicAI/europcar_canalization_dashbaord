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
 
 
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  
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

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->


   <script src="https://unpkg.com/ionicons@4.5.5/dist/ionicons.js"></script>

  <!-- Google Font -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
 
 
 
 <div id="reload"></div>
 
               <div id="page-wrapper" >

            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                     <h2>Dashboard</h2>   
					
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
				
				<!-- inizio ROW 24 ore -->
				
				<div class="row">
				
					<div class="col-md-12 text-center">
						<h2> DAILY </h2>
					</div>
				
					<?php

                    $conta_righe_addessed_24 =  conta_righe_con_stato_24ore ('400') + conta_righe_con_stato_24ore ('401') + conta_righe_con_stato_24ore ('402') + conta_righe_con_stato_24ore ('403');
                    $perc_applicata = round(($conta_righe_addessed_24 / conta_righe_totali_24ore()) * 100);


                    ?>




					<div class="col-md-4">
						<div class="info-box" style="background-color:#2369bf;color:white">
						
							<span class="info-box-icon"><ion-icon name="checkmark-circle"></ion-icon></span>

							<div class="info-box-content">
							  <span class="info-box-text">Automatic Addressed Practices</span>
							  <span class="info-box-number"><?php echo $conta_righe_addessed_24; ?> </span>

							  <div class="progress">
								<div class="progress-bar" style="width: <?php echo $perc_applicata; ?>%"></div>
							  </div>
							  <span class="progress-description">
								
									<?php echo $perc_applicata ?> % of Total
								  </span>
							</div>
							<!-- /.info-box-content -->
						</div>
					</div>
					
					<?php 
					//echo 'Righe totali:'.conta_righe_totali();
					
					?>
					
					
					
					<!-- INIZIO Gestite Manualmente -->
					
					
					<?php

                    $conta_righe_manually_24 = conta_righe_con_stato_24ore ('490') + conta_righe_con_stato_24ore ('540') + conta_righe_con_stato_24ore ('450') + conta_righe_con_stato_24ore ('570') + conta_righe_con_stato_24ore ('600') + conta_righe_con_stato_24ore ('405') + conta_righe_con_stato_24ore ('560') + conta_righe_con_stato_24ore ('480');
                    $perc_applicata_ge_ma = round(($conta_righe_manually_24  / conta_righe_totali_24ore()) * 100 );


                    ?>
					
					<div class="col-md-4">
						<div class="info-box" style="background-color:#9c6aff; color:white">

							<span class="info-box-icon"><ion-icon name="checkmark-circle"></ion-icon></span>

							<div class="info-box-content">
							  <span class="info-box-text">Manually managed Practices</span>
							  <span class="info-box-number"><?php echo $conta_righe_manually_24; ?> </span>

							  <div class="progress">
								<div class="progress-bar" style="width: <?php echo $perc_applicata_ge_ma; ?>%"></div>
							  </div>
							  <span class="progress-description">
								
									<?php echo $perc_applicata_ge_ma ?> % of Total
								  </span>
							</div>
							<!-- /.info-box-content -->
						</div>
					</div>
					
					
					<!-- INIZIO Rimandate alla stazione -->
					
					
					<?php

                    $conta_righe_rej_24 = conta_righe_con_stato_24ore ('410') + conta_righe_con_stato_24ore ('440') + conta_righe_con_stato_24ore ('460') + conta_righe_con_stato_24ore ('430');
                    $perc_applicata_ri_sta = round(($conta_righe_rej_24  / conta_righe_totali_24ore()) * 100 ); ?>
					
					<div class="col-md-4">
					<div class="info-box bg-aqua">
					
					
						<span class="info-box-icon"><ion-icon name="checkmark-circle"></ion-icon></span>

						<div class="info-box-content">
						  <span class="info-box-text">Rejected Practices</span>
						  <span class="info-box-number"><?php echo $conta_righe_rej_24 ; ?>   </span>

						  <div class="progress">
							<div class="progress-bar" style="width: <?php echo $perc_applicata_ri_sta; ?>%"></div>
						  </div>
						  <span class="progress-description">
							
								<?php echo $perc_applicata_ri_sta ?> % of Total
							  </span>
						</div>
						<!-- /.info-box-content -->
					</div>
					</div>
					
					<?php 
					//echo 'Righe totali:'.conta_righe_totali();
					
					?>
					
					<!-- Fine Rimandate alla stazione -->
					
					


					<!-- inizio irm processati -->
						
					<?php $perc_applicata = round((conta_irm_processati_24ore()  / conta_righe_totali_24ore()) *100 ); ?>
						
					<div class="col-md-4">
						<div class="info-box bg-green">

							<span class="info-box-icon"><ion-icon name="checkmark-circle"></ion-icon></span>

							<div class="info-box-content">
							  <span class="info-box-text">IRM PROCESSED</span>
							  <span class="info-box-number"><?php echo conta_irm_processati_24ore(); ?> </span>

							  <div class="progress">
								<div class="progress-bar" style="width: <?php echo $perc_applicata; ?>%"></div>
							  </div>
							  <span class="progress-description">
								
									<?php echo $perc_applicata ?> % of Total
								  </span>
							</div>
							<!-- /.info-box-content -->
						</div>
					</div>
					
					<!-- fine irm processati -->
					
					
					<!-- Inizio irm falliti -->
					
					<?php $perc_applicata = round((conta_irm_falliti_24ore() / conta_righe_totali_24ore() ) * 100 ); ?>
					
					
					<div class="col-md-4">
						<div class="info-box bg-red">

							<span class="info-box-icon"><ion-icon name="checkmark-circle"></ion-icon></span>

							<div class="info-box-content">
							  <span class="info-box-text">IRM FAILED</span>
							  <span class="info-box-number"><?php echo conta_irm_falliti_24ore(); ?> </span>

							  <div class="progress">
								<div class="progress-bar" style="width: <?php echo $perc_applicata; ?>%"></div>
							  </div>
							  <span class="progress-description">
								
									<?php echo $perc_applicata ?> % of Total
								  </span>
							</div>
							<!-- /.info-box-content -->
						</div>
					</div>
						
					<!-- fine irm falliti -->

                    <?php $perc_applicata = round((conta_irm_restanti_24ore()  / conta_righe_totali_24ore()) *100 ); ?>

                    <div class="col-md-4">
                        <div class="info-box bg-orange">

                            <span class="info-box-icon"><ion-icon name="checkmark-circle"></ion-icon></span>

                            <div class="info-box-content">
                                <span class="info-box-text">IRM REJECTED</span>
                                <span class="info-box-number"><?php echo conta_irm_restanti_24ore(); ?> </span>

                                <div class="progress">
                                    <div class="progress-bar" style="width: <?php echo $perc_applicata; ?>%"></div>
                                </div>
                                <span class="progress-description">

									<?php echo $perc_applicata ?> % of Total
								  </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </div>




					
					
					

					<div class="col-md-12" style="margin-bottom:15px;">

						<span> Total last 24 hours: <b><?php
                                $total24 = $conta_righe_addessed_24 + $conta_righe_manually_24 + $conta_righe_rej_24;
                                echo $total24; ?></b> </span>
					</div>
				
				
				</div>   <!-- fine ROW 24 ore -->
				
				
				
				<div class="row">
					<div class="col-md-12 text-center">
						<h2> YEAR TO DATE </h2>
				
					</div>
				
					<?php

                    $conta_righe_addessed_all = conta_righe_con_stato ('400') + conta_righe_con_stato ('401') + conta_righe_con_stato ('402') + conta_righe_con_stato ('403');

                    $perc_applicata = round(($conta_righe_addessed_all / conta_righe_totali() ) * 100 ); ?>
					
					<div class="col-md-4">
						<div class="info-box " style="background-color:#2369bf;color:white">
						
							<span class="info-box-icon"><ion-icon name="checkmark-circle"></ion-icon></span>

							<div class="info-box-content">
							  <span class="info-box-text">Automatic Addressed Practices</span>
							  <span class="info-box-number"><?php echo $conta_righe_addessed_all; ?> </span>

							  <div class="progress">
								<div class="progress-bar" style="width: <?php echo $perc_applicata; ?>%"></div>
							  </div>
							  <span class="progress-description">
								
									<?php echo $perc_applicata ?> % of Total
								  </span>
							</div>
							<!-- /.info-box-content -->
						</div>
					</div>
					
					<?php 
					//echo 'Righe totali:'.conta_righe_totali();
					
					?>
					
					
					
					<!-- INIZIO Gestite Manualmente -->
					
					
					<?php

                    $conta_righe_manually_all = conta_righe_con_stato ('490') + conta_righe_con_stato ('540') +  conta_righe_con_stato ('450') +  conta_righe_con_stato ('570') +  conta_righe_con_stato ('600') + conta_righe_con_stato ('560') + conta_righe_con_stato ('405') + conta_righe_con_stato ('480');
                    $perc_applicata_ge_ma = round(($conta_righe_manually_all / conta_righe_totali() ) * 100 ); ?>
					
					<div class="col-md-4">
						<div class="info-box" style="background-color:#9c6aff; color:white">

							<span class="info-box-icon"><ion-icon name="checkmark-circle"></ion-icon></span>

							<div class="info-box-content">
							  <span class="info-box-text">Manually managed Practices</span>
							  <span class="info-box-number"><?php echo $conta_righe_manually_all; ?> </span>

							  <div class="progress">
								<div class="progress-bar" style="width: <?php echo $perc_applicata_ge_ma; ?>%"></div>
							  </div>
							  <span class="progress-description">
								
									<?php echo $perc_applicata_ge_ma ?> % of Total
								  </span>
							</div>
							<!-- /.info-box-content -->
						</div>
					</div>
					
					
					<!-- INIZIO Rimandate alla stazione -->
					
					
					<?php
                    $conta_righe_rejected_all = conta_righe_con_stato ('410') + conta_righe_con_stato ('440') + conta_righe_con_stato ('460') + conta_righe_con_stato ('420') + conta_righe_con_stato ('430');
                    $perc_applicata_ri_sta = round(($conta_righe_rejected_all  / conta_righe_totali() ) *100 ); ?>
					
					<div class="col-md-4">
					<div class="info-box bg-aqua">
					
					
						<span class="info-box-icon"><ion-icon name="checkmark-circle"></ion-icon></span>

						<div class="info-box-content">
						  <span class="info-box-text">Rejected Practices</span>
						  <span class="info-box-number"><?php echo $conta_righe_rejected_all; ?>   </span>

						  <div class="progress">
							<div class="progress-bar" style="width: <?php echo $perc_applicata_ri_sta; ?>%"></div>
						  </div>
						  <span class="progress-description">
							
								<?php echo $perc_applicata_ri_sta ?> % of Total
							  </span>
						</div>
						<!-- /.info-box-content -->
					</div>
					</div>
					
					<?php 
					//echo 'Righe totali:'.conta_righe_totali();
					
					?>
					
					<!-- Fine Rimandate alla stazione -->
					

					<!-- inizio irm processati -->
						
					<?php $perc_applicata = round((conta_irm_processati() / conta_righe_totali()) * 100 ); ?>
						
					<div class="col-md-4">
						<div class="info-box bg-green">

							<span class="info-box-icon"><ion-icon name="checkmark-circle"></ion-icon></span>

							<div class="info-box-content">
							  <span class="info-box-text">IRM PROCESSED</span>
							  <span class="info-box-number"><?php echo conta_irm_processati(); ?> </span>

							  <div class="progress">
								<div class="progress-bar" style="width: <?php echo $perc_applicata; ?>%"></div>
							  </div>
							  <span class="progress-description">
								
									<?php echo $perc_applicata ?> % of Total
								  </span>
							</div>
							<!-- /.info-box-content -->
						</div>
					</div>

					<!-- fine irm processati -->
					
					
					<!-- Inizio irm falliti -->
					
					<?php $perc_applicata = round((conta_irm_falliti() / conta_righe_totali()) * 100 ); ?>
					
					
					<div class="col-md-4">
						<div class="info-box bg-red">

							<span class="info-box-icon"><ion-icon name="checkmark-circle"></ion-icon></span>

							<div class="info-box-content">
							  <span class="info-box-text">IRM FAILED</span>
							  <span class="info-box-number"><?php echo conta_irm_falliti(); ?> </span>

							  <div class="progress">
								<div class="progress-bar" style="width: <?php echo $perc_applicata; ?>%"></div>
							  </div>
							  <span class="progress-description">
								
									<?php echo $perc_applicata ?> % of Total
								  </span>
							</div>
							<!-- /.info-box-content -->
						</div>
					</div>


                    <?php $perc_applicata = round((conta_irm_restanti() / conta_righe_totali()) * 100 ); ?>

                    <div class="col-md-4">
                        <div class="info-box bg-orange">

                            <span class="info-box-icon"><ion-icon name="checkmark-circle"></ion-icon></span>

                            <div class="info-box-content">
                                <span class="info-box-text">IRM REJECTED</span>
                                <span class="info-box-number"><?php echo conta_irm_restanti(); ?> </span>

                                <div class="progress">
                                    <div class="progress-bar" style="width: <?php echo $perc_applicata; ?>%"></div>
                                </div>
                                <span class="progress-description">

									<?php echo $perc_applicata ?> % of Total
								  </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </div>
					
					<div class="col-md-12">
						 <span>Total from 01/04/2019: <b><?php
                                 $righe_totali_all = $conta_righe_addessed_all + $conta_righe_manually_all + $conta_righe_rejected_all;
                                 echo $righe_totali_all; ?> </b>
					
					</div>
						
					<!-- fine irm falliti -->
				</div>    <!-- Questo è il div di fine riga ROW -->
				
				<hr style="border-top:1px solid #c7c7c7">
				
				
				
				
				</div>    <!-- Questo è il div del container  -->
				
			
			
		 
<?php  require_once('../footer-pag.php');   ?>		 



 
				 
				 
				 
				 
				 <?php
				 
				 
				 
				 
				 //Funzioni di questa pagina
				 
				 function conta_righe_con_stato ($status) {	
					global $servername; global $username; global $password; global $dbname;
					$conn = new mysqli($servername, $username, $password, $dbname);
     				if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 


					//$sql = "SELECT * FROM `riferimenti_fat_for` WHERE `402fattura` LIKE '$fatt402' AND `email` LIKE 'senzaemail@senzaemail.com' ";
					$sql = "SELECT count(*) FROM `ataraxia_main` where status = $status AND dt_insert BETWEEN ('2019-04-01') AND NOW()";
					
					/*if($status == '450'){
						
						$sql.= "AND email_inviata_autofficina like ''";
					}*/
					
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						//return $result->num_rows;						
						 $row = $result->fetch_row();
						  return $row[0];
					} else { return 0; }
					$conn->close();	
						
					} // Chiusura Funzione
					
					
					
					
				function conta_righe_con_stato_24ore ($status) {	
					global $servername; global $username; global $password; global $dbname;
					$conn = new mysqli($servername, $username, $password, $dbname);
     				if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 


					//$sql = "SELECT * FROM `riferimenti_fat_for` WHERE `402fattura` LIKE '$fatt402' AND `email` LIKE 'senzaemail@senzaemail.com' ";
					//$sql = "SELECT count(*) FROM `ataraxia_main` where status = $status AND dt_insert AND dt_insert >= DATE_SUB(CURDATE(),INTERVAL 1 DAY)";
					$sql="SELECT count(*) FROM `ataraxia_main` where status = $status AND dt_insert BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d') AND NOW()";

					
					/*if($status == '450'){
						
						$sql.= "AND email_inviata_autofficina like ''";
					}*/
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						//return $result->num_rows;						
						 $row = $result->fetch_row();
						  return $row[0];
					} else { return 0; }
					$conn->close();	
						
					} // Chiusura Funzione
					
					
					function conta_manuali_24_ore() {	
					global $servername; global $username; global $password; global $dbname;
					$conn = new mysqli($servername, $username, $password, $dbname);
     				if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 


					//$sql = "SELECT * FROM `riferimenti_fat_for` WHERE `402fattura` LIKE '$fatt402' AND `email` LIKE 'senzaemail@senzaemail.com' ";
					//$sql = "SELECT count(*) FROM `ataraxia_main` where status = $status AND dt_insert AND dt_insert >= DATE_SUB(CURDATE(),INTERVAL 1 DAY)";
					$sql="SELECT count(*) FROM `ataraxia_main` where status>0 AND status<400 AND retrybricked = 2 AND dt_insert BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d') AND NOW()";
					
					/*if($status == '450'){
						
						$sql.= "AND email_inviata_autofficina like ''";
					}*/
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						//return $result->num_rows;						
						 $row = $result->fetch_row();
						  return $row[0];
					} else { return 0; }
					$conn->close();	
						
					} // Chiusura Funzione
					
					
					 function conta_manuali() {	
					global $servername; global $username; global $password; global $dbname;
					$conn = new mysqli($servername, $username, $password, $dbname);
     				if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 


					//$sql = "SELECT * FROM `riferimenti_fat_for` WHERE `402fattura` LIKE '$fatt402' AND `email` LIKE 'senzaemail@senzaemail.com' ";
					$sql = "SELECT count(*) FROM `ataraxia_main` where status>0 AND status<400 AND retrybricked = 2 AND dt_insert BETWEEN ('2019-04-01') AND NOW()";
					
					/*if($status == '450'){
						
						$sql.= "AND email_inviata_autofficina like ''";
					}*/
					
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
					//$sql = "SELECT count(*) FROM `ataraxia_main`";
					$sql = "SELECT count(*) FROM `ataraxia_main` WHERE dt_insert BETWEEN ('2019-04-01') AND NOW() AND status in(400,490,540,450,430,570,401,410,440,460,420,600,405,402,403,560,480,402)";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						//return $result->num_rows;						
						 $row = $result->fetch_row();
						  return $row[0];
					} else { return 0; }
					$conn->close();	
						
					} // Chiusura Funzione
					
					
				function conta_righe_totali_24ore() {	
					global $servername; global $username; global $password; global $dbname;
					$conn = new mysqli($servername, $username, $password, $dbname);
     				if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 


					//$sql = "SELECT * FROM `riferimenti_fat_for` WHERE `402fattura` LIKE '$fatt402' AND `email` LIKE 'senzaemail@senzaemail.com' ";
					//$sql = "SELECT count(*) FROM `ataraxia_main`";
					//$sql = "SELECT count(*) FROM `ataraxia_main` WHERE dt_insert >= DATE_SUB(CURDATE(),INTERVAL 1 DAY) ";
					$sql="SELECT count(*) FROM `ataraxia_main` WHERE dt_insert BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d') AND NOW() AND status in(400,490,540,450,430,570,401,410,440,460,420,600,405,402,403,560,480,402)";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						//return $result->num_rows;						
						 $row = $result->fetch_row();
						  return $row[0];
					} else { return 0; }
					$conn->close();	
						
					} // Chiusura Funzione
					
					
				function conta_irm_falliti() {	
					global $servername; global $username; global $password; global $dbname;
					$conn = new mysqli($servername, $username, $password, $dbname);
     				if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 


					
					$sql = "SELECT count(*) FROM `ataraxia_main` WHERE `dt_insert` BETWEEN ('2019-04-01') AND NOW() AND `irm_status` in (6,7)";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						//return $result->num_rows;						
						 $row = $result->fetch_row();
						  return $row[0];
					} else { return 0; }
					$conn->close();	
						
				} // Chiusura Funzione
					
					
					
				function conta_irm_falliti_24ore() {	
					global $servername; global $username; global $password; global $dbname;
					$conn = new mysqli($servername, $username, $password, $dbname);
     				if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 


					
					//$sql = "SELECT count(*) FROM `ataraxia_main` WHERE `irm_dt`IS NOT NULL AND `ts_in_dt` >= DATE_SUB(CURDATE(),INTERVAL 1 DAY) AND `status` in (6,7)";
					$sql="SELECT count(*) FROM `ataraxia_main` WHERE dt_insert BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d') AND NOW() AND `irm_status` in (6,7)";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						//return $result->num_rows;						
						 $row = $result->fetch_row();
						  return $row[0];
					} else { return 0; }
					$conn->close();	
						
				} // Chiusura Funzione	
					
					
				function conta_irm_processati() {	
					global $servername; global $username; global $password; global $dbname;
					$conn = new mysqli($servername, $username, $password, $dbname);
     				if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 


					
					$sql = "SELECT count(*) FROM `ataraxia_main` WHERE `dt_insert` BETWEEN ('2019-04-01') AND NOW() AND `irm_status` = 5";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						//return $result->num_rows;						
						 $row = $result->fetch_row();
						  return $row[0];
					} else { return 0; }
					$conn->close();	
						
				} // Chiusura Funzione
				
				
				function conta_irm_processati_24ore() {	
					global $servername; global $username; global $password; global $dbname;
					$conn = new mysqli($servername, $username, $password, $dbname);
     				if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 


					
					$sql = "SELECT count(*) FROM `ataraxia_main` WHERE dt_insert BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d') AND NOW() AND `irm_status` = 5";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						//return $result->num_rows;						
						 $row = $result->fetch_row();
						  return $row[0];
					} else { return 0; }
					$conn->close();	
						
				} // Chiusura Funzione




				
				function conta_irm_restanti() {
                    global $servername; global $username; global $password; global $dbname;
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }



                    $sql = "SELECT count(*) from ataraxia_main where dt_insert >= '2019-04-01' and irm_status = 0 and `status`>= 400 and `status` not in (0,1)";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        //return $result->num_rows;
                        $row = $result->fetch_row();
                        return $row[0];
                    } else { return 0; }
                    $conn->close();

                } // Chiusura Funzione



                 function conta_irm_restanti_24ore() {
                     global $servername; global $username; global $password; global $dbname;
                     $conn = new mysqli($servername, $username, $password, $dbname);
                     if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }



                     $sql = "SELECT count(*) FROM `ataraxia_main` WHERE dt_insert BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d') AND NOW() and irm_status = 0 and `status`>= 400 and `status` not in (0,1)";
                     $result = $conn->query($sql);

                     if ($result->num_rows > 0) {
                         //return $result->num_rows;
                         $row = $result->fetch_row();
                         return $row[0];
                     } else { return 0; }
                     $conn->close();

                 } // Chiusura Funzione


				
				function conta_righe_pending() {	
					global $servername; global $username; global $password; global $dbname;
					$conn = new mysqli($servername, $username, $password, $dbname);
     				if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 


					
					$sql = "SELECT count(*) FROM `ataraxia_main` WHERE dt_insert BETWEEN ('2019-04-01') AND NOW() AND `status` = 400 AND wsm_bsm_status like 'First Reminder Sent%' ";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						//return $result->num_rows;						
						 $row = $result->fetch_row();
						  return $row[0];
					} else { return 0; }
					$conn->close();	
						
				} // Chiusura Funzione
				
				
				function conta_righe_pending_24ore() {	
					global $servername; global $username; global $password; global $dbname;
					$conn = new mysqli($servername, $username, $password, $dbname);
     				if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 


					
					$sql = "SELECT count(*) FROM `ataraxia_main` WHERE `status` = 400 AND wsm_bsm_status like '%Reminder Sent%'";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						//return $result->num_rows;						
						 $row = $result->fetch_row();
						  return $row[0];
					} else { return 0; }
					$conn->close();	
						
				} // Chiusura Funzione
				 
				 ?>



