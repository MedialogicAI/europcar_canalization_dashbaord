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

$chars=$_GET['region'];

if($chars == "Valle d'Aosta"){
	$chars = 'valle';
};

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
				<div class="row">
					<div class="col-md-12" id="nselection" >
						<div class="col-md-4">
						    <span> Region: <b><?php echo $chars ?></b> </span> <br><br>
							
							<select class="form-control comuni" name="selectComuni" id="comuni">
								<option id="label" selected > Select Province </option>
							
								<?php 
								    
																	
								$comuni = elencoComuni($chars);

								for ($i=0;$i<count($comuni);$i++){ 
									
									echo '<option id="'.$comuni[$i]['PROVINCIA'].'">'.$comuni[$i]['PROVINCIA'].'</option>';
								}
									
								?>
								
							</select>
						
						</div>
					</div>
					<div class="col-md-12" id="chartContainer">
							<canvas id="capacityChart" style="height: ; width: 680px;" width="680" height="450"></canvas>
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
		
		for($i=0; $i<count($comuni); $i++){
			$solocitta[$i] = $comuni[$i]['PROVINCIA'];	
			$soloimpegno[$i] = $comuni[$i]['impegno'];		
			
			if($comuni[$i]['capacity'] == NULL){
				$solosomma[$i] = '2';
			}	
			else{
				$solosomma[$i] = $comuni[$i]['capacity'];	
						
			}	
		}
		
		
		$maxCapacity = $soloimpegno;
		//echo "quisolocitta";
		//print_r ($solosomma);
		
	    //exit();
		$nomicosecitta =  "\"";
		$nomicosecitta .= implode("\",\"",$solocitta);
		$nomicosecitta .=  "\"";
		
				
		$sommacosecitta .= implode(",",$solosomma);
		$impegnocosecitta .= implode(",",$soloimpegno);
		rSort($maxCapacity);
		
		
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
				datasets: [
					{
						label: '# Capacity',
						data: [<?php echo $sommacosecitta; ?>],
						backgroundColor: 'rgba(48, 205, 255, 0.2)',
						borderColor: 'rgba(48, 205, 255, 1)',
						borderWidth: 1
					},
					
					{
						label: '# Engaged',
						data: [<?php echo $impegnocosecitta; ?>],
						backgroundColor: 'rgba(255, 0, 0, 0.2)',
						borderColor: 'rgba(255, 0, 0, 1)',
						borderWidth: 1
					}
				]
			},
			options: {
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero:true,
							stepSize: 2,
							min: 0,
							max:<?php echo $maxCapacity[0]; ?> +10,
							//max:50,					
						}
					}],
					
					xAxes: [{
						ticks: {
							autoSkip: false
												
						}
					}],
				}	        
			}
		});
	</script>

	


<script type="text/javascript" language="javascript" >

	$(document).ready(function(){	 
		$( "#comuni" ).change(function(){
			var PROVINCIA = $(this).children(":selected").attr("id");
			location.href = '../dash/lista_capacita_comuni1.php?com='+PROVINCIA;
		});			 
	});
		 
	</script>	 
		 
<?php  require_once('../footer-pag.php');   ?>		 			 				 
<?php				
function elencoComuni ($regione) {	
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
	
	
	$sql="select sum(carico) as capacity, PROVINCIA , sum(impegno) as impegno FROM `view_completa_diretti` where REGIONE like '%".$regione."%' GROUP BY PROVINCIA";

	
	
	$result = $conn->query($sql);

	
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			
			
			$PROVINCIA[]=$row;	

			
		}		

		return $PROVINCIA;
		
	} else {
		//echo "0 results";
		return 0;
	}
	$conn->close();	
		
	}  //fine funzione
?>












