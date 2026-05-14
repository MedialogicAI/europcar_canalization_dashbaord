       

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
	<style>
		.buttonDisabled{
			
			background:#b2b2b2;
			cursor:not-allowed !important;
		}
	
	
	</style>
	   
	   <!-- /. NAV TOP  -->
        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar navbar-default">
                <ul class="nav" id="main-menu">
                    <li class="text-center user-image-back">
                        <div id="movelogo">
						<center> <img src="/image/Europcar-Logo.jpg" class="img-responsive" /> </center>
                        </div>
                    </li>


                    <li>
                        <a href="#" onClick='document.getElementById("ifr_page_central").src="/dash/user_account.php";' id="dash-ua" ><i class="fa fa-desktop "></i>Dashboard KPI</a>
                    </li>
					
					<li>
                        <a href="#" onClick='document.getElementById("ifr_page_central").src="/dash/lista_odierne_totali.php";' id="googleform" ><i class="material-icons">&#xe8a4;</i>Today Worked Practices</a>
                    </li>
					
					<li>
                        <a href="#" onClick='document.getElementById("ifr_page_central").src="/dash/lista_pending_totali.php";' id="pending" ><i class="material-icons">&#xe8a4;</i>
							Addressed <br> waiting for pick-up 12/24h
						</a>
                    </li>
					
					<li>
                        <a href="#" onClick='document.getElementById("ifr_page_central").src="/dash/lista_canalizzate.php";' id="automatic-proc" ><i class="material-icons">&#xe8a4;</i>Automatic Addressed Practices</a>
                    </li>
                    
					
					
					
					<li>
                        <a href="#" onClick='document.getElementById("ifr_page_central").src="/dash/lista_gestite_manualmente.php";' ><i class="material-icons">&#xe8a4;</i> Manually managed Practices</a>
                    </li>
					
					<?php if ($utenepriv["privilegi"] == 1) { ?>
					<?php } ?>
					
					<li>
                        <a href="#" onClick='document.getElementById("ifr_page_central").src="/dash/lista_missing_something.php";' ><i class="material-icons">	&#xe8a4;</i>Rejected Practices</a>
                    </li>
					
					
					
					<li>
                        <a href="#" onClick='document.getElementById("ifr_page_central").src="/dash/lista_not_piked_up.php";' ><i class="material-icons">	&#xe8a4;</i>Addressed not piked-up</a>
                    </li>
					 
					
					
					<li>
                        <a href="#" onClick='document.getElementById("ifr_page_central").src="/dash/lista_tutto.php";' ><i class="material-icons">	&#xe8a4;</i>All Practices</a>
                    </li>
					<li>&nbsp;	&nbsp;	&nbsp;	</li>
					
					<li style="color: #3c8dbc;">
							&nbsp;&nbsp;&nbsp;
							<i class="material-icons">	&#xe8a4;</i>Availability Diretti   <br>
								&nbsp;&nbsp;&nbsp;
								<select name="regioni" id="regioni" style="width:90%">
									<option id="label">Select Region</option>
									<?php
								
									foreach(listaRegioni() as $regione){
										if($regione[0] != ''){
											echo '<option id="'.trim($regione[0]).'">'.trim($regione[0]).'</option>';
										}
									}
										
									?>
								</select>
								
					</li>
					<li>&nbsp;	&nbsp;	&nbsp;	</li>
					
					
					
						<li style="color: #3c8dbc;">
							&nbsp;&nbsp;&nbsp;
							<i class="material-icons">	&#xe8a4;</i>Availability Network  <br>
								&nbsp;&nbsp;&nbsp;
								<select name="regioni" id="regioni_n" style="width:90%">
									<option id="label">Select Region</option>
									<?php
								
									foreach(listaRegioniNetwork() as $network){
										if($network[0] != ''){
											echo '<option id="'.trim($network[0]).'">'.trim($network[0]).'</option>';
										}
									}
										
									?>
								</select>
								
						</li>
						
						
						
					
					 
				
					
                    <li>
                        <a href="index.php?logout=1"><i class="glyphicon glyphicon-log-out"></i>Log out</a>
                    </li>
					
					
						
					<div style="max-width: 340px;">
							<table class="table table-bordered">
								<thead>
									<tr >
										<th colspan="2" style="text-align: center">
											STOP / START 
										</th>
									</tr>
								</thead>
								<tr class="active">
										<td style="vertical-align:middle; text-align:center;">
											<div id="statoiconaautoma">  </div>
										</td>
										<td style="vertical-align:middle;">
											<div id="stopextremis">  </div>
										</td>
									</tr>				
							</table>
						</div>
					
					
					
						<div style="max-width:100% ">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th  style="text-align: center">
											<button  type="button" class="refresh" value="canaliz_file" id="canaliz_file" style="width:100%" ><i class="glyphicon glyphicon-refresh"></i>  Refresh Canaliz </button>
										</th>										
									</tr>
									<tr>
										<th  style="text-align: center">
											<button class="refresh" type="button" value="capacity_file" id="capacity_file" style="width:100%" ><i class="glyphicon glyphicon-refresh"></i>  Refresh Capacity </button>
										</th>										
									</tr>
									<tr>
										<th  style="text-align: center">
											<button class="refresh" type="button" value="listabb_file" id="listabb_file" style="width:100%" ><i class="glyphicon glyphicon-refresh"></i>  Refresh BB file </button>
										</th>										
									</tr>
									<tr>
										<th  style="text-align: center">
											<button class="refresh" type="button" value="movement_file" id="movement_file" style="width:100%" ><i class="glyphicon glyphicon-refresh"></i> Refresh Movement from MailBox </button>
										</th>										
									</tr>
									<tr>
										<th  style="text-align: center">
											<button class="refresh" type="button" value="flotta_file" id="flotta_file" style="width:100%" ><i class="glyphicon glyphicon-refresh"></i>  Refresh Flotta from MailBox </button>
										</th>										
									</tr>
									
								</thead>			
							</table>
						</div>
						
					<?php if ($utenepriv["privilegi"] == 1) {?>
					 
					<?php } ?>
					
					
                </ul>

            </div>

        </nav>
		
	<?php
		 function listaRegioni () {	
			global $servername; global $username; global $password; global $dbname;
			$conn = new mysqli($servername, $username, $password, $dbname);
			if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 
			
			
			$sql = "SELECT REGIONE FROM `view_completa_diretti` GROUP BY REGIONE";
			//$sql = "SELECT REGIONE FROM `view_completa_diretti` GROUP BY REGIONE";
			$result = $conn->query($sql);
			
	
			if ($result->num_rows > 0) {
					
				while($row = $result->fetch_row()) {
					$rows[]=$row;
				}
				
				return $rows;
			} 
			
			else { 
			
				return 0;

			}
			$conn->close();	
				
			} // Chiusura Funzione*/
			
			
			
			function listaRegioniNetwork () {	
                global $servername; global $username; global $password; global $dbname;
                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }


                $sql = "SELECT REGIONE FROM `mouvement_odierne_network` GROUP BY REGIONE";
                //$sql = "SELECT REGIONE FROM `view_completa_diretti` GROUP BY REGIONE";
                $result = $conn->query($sql);


                if ($result->num_rows > 0) {

                    while($row = $result->fetch_row()) {
                        $rows[]=$row;
                    }

                    return $rows;
                }

                else {

                    return 0;

                }
                $conn->close();
				
			} // Chiusura Funzione*/

			
 
	?>
		
		
		
			<script>
	
	
	                function changestatus() {
						$('#stopextremis').empty();
						$.getJSON('/gestione-lavorazioni/stop-esecutivo.php', function (data) {
					  console.log(data);
					      
						});  //Chiusura 
					  }  //chiusura funzione changestatus
	
	
	//setInterval(function(){ alert("Hello"); }, 3000);
	
				$(document).ready(function () {
					
					
					
				  
					  setInterval(function(){
						  


						  
					//codcliente_lav_e	  
					
					//tot_lav_complessive
					
					
					
					var showDatastatustopextremis = $('#statoiconaautoma');
					var showDatasolotestostopextr = $('#stopextremis'); 
					$.getJSON('/gestione-lavorazioni/stop-lettura.php', function (data) {
					  console.log(data);
							  //var items = data.item.map(function (item) {
								//return data.value;
							  //});
					        showDatastatustopextremis.empty();
							showDatasolotestostopextr.empty();

					  //if (items.length) {
						//var content = '<li>' + items.join('</li><li>') + '</li>';
						//var list = $('<ul />').html(content);
						if (data.value == 1){
						
						showDatastatustopextremis.html('<img src="/image/red-icon.png" width="100px" />');
						showDatasolotestostopextr.html('<div ><center><font color="red"> Robot STOP  </font> </br> <button onclick="changestatus()" type="button" value="Attiva Automa" > Power on Robot </button> </center> </div>');
						}
						if (data.value == 0){
						
						showDatastatustopextremis.html('<img src="/image/green-icon.png" width="100px" />');
						showDatasolotestostopextr.html('<div> <center> <font color="green"> Robot ACTIVATED </font> </br> <button onclick="changestatus()" type="button" value="Ferma Automa" > Switch off Robot </button> </center> </div>');
						}
						
					  //}
					});  //Chiusura att_lav_402fatt
					
			
						  }, 3000);    //Chiusura intervallo di refresh
				  
				  
				  
				  
				  
				});    // Chiusura document.ready
	
	
	         
			 	     function getRegione(regione) {
						 if (regione != "0"){
 			             document.getElementById("ifr_page_central").src="/dash/user_account3.php?multi="+regione;	
						 //document.getElementsByClassName("selezionalo")[0].setAttribute("selected","selected");
						 }
					  }  //chiusura funzione changestatus
	
	         $(document).ready(function(){
				 
				setInterval("checkStatus()",3000);

				 
				 $( "#regioni" ).change(function(){
					var region = $(this).children(":selected").attr("id");
					$('#ifr_page_central').attr('src','/dash/user_account4.php?region='+region);
					$('#label').prop('selected',true);
				 
				 });
				 
				 
				  $( "#regioni_n" ).change(function(){
					var region = $(this).children(":selected").attr("id");
					$('#ifr_page_central').attr('src','/dash/carico_network_regione.php?region='+region);
					$('#label').prop('selected',true);
				 
				 });
				 
				 
				 
				
				$(".refresh").click(function(){
					var value = $(this).val();
					//alert (value);
					$.ajax({
						  type: "POST",
						  url: "/gestione-lavorazioni/refresh_file.php",
						  data:{fileName:value},
						  //dataType: "html",
						  success: function(risposta){
							  
							 
						  },
						  // ed una per il caso di fallimento
						  error: function(){
							alert("Chiamata fallita!!!");
						  }
						});

					 });
				});
				
				
				
				function checkStatus(){
					
						$.ajax({
							  type: "POST",
							  url: "/gestione-lavorazioni/refresh_file.php",
							  data:{action:'check'},
							  //dataType: "html",
							  success: function(risposta){
								  console.log('chiamata');
								 var obj = jQuery.parseJSON(risposta);

								if(obj.canaliz_file == '1'){
									$('#canaliz_file').prop("disabled",true);
									$('#canaliz_file').addClass('buttonDisabled');
								}
								else{
									$('#canaliz_file').prop("disabled",false);
									$('#canaliz_file').removeClass('buttonDisabled');
								}
								
								if(obj.capacity_file == '1'){
									$('#capacity_file').prop("disabled",true);
									$('#capacity_file').addClass('buttonDisabled');
								}
								else{
									$('#capacity_file').prop("disabled",false);
									$('#capacity_file').removeClass('buttonDisabled');
								}
								
								if(obj.listabb_file == '1'){
									$('#listabb_file').prop("disabled",true);
									$('#listabb_file').addClass('buttonDisabled');
								}
								else{
									$('#listabb_file').prop("disabled",false);
									$('#listabb_file').removeClass('buttonDisabled');
								}
								
								if(obj.movement_file == '1'){
									$('#movement_file').prop("disabled",true);
									$('#movement_file').addClass('buttonDisabled');
								}
								else{
									$('#movement_file').prop("disabled",false);
									$('#movement_file').removeClass('buttonDisabled');
								}
								
								if(obj.flotta_file == '1'){
									$('#flotta_file').prop("disabled",true);
									$('#flotta_file').addClass('buttonDisabled');
								}
								else{
									$('#flotta_file').prop("disabled",false);
									$('#flotta_file').removeClass('buttonDisabled');
								}
							  },
							  // ed una per il caso di fallimento
							  error: function(){
								alert("Chiamata fallita!!!");
							  }
							});

						
				};
				
	</script>
		
			
	
        <!-- /. NAV SIDE  -->
		
		
		
		