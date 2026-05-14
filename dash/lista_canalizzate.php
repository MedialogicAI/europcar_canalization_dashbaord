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

<script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU=" crossorigin="anonymous"></script>


<?php
/*
<script src="/lib_ms/jquery.min.js"></script>

*/
?>


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

 
               <div id="page-wrapper" >

            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                     <h2>Automatic Addressed Practices List</h2>   
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />
              

<style>
select.form-control:not([size]):not([multiple]) {
    height: calc(2.25rem + 10px) !important;
    width: 98%;
}
</style>

<?php 



require_once('../dbconf.php');
//require_once('funzioni-dash_main.php');
//require_once('login-lista-user-db_main.php');
//require_once('login-err-non-valido_main.php');

//global $servername;
//$username = "a2aqualifiche";
//$password = "reset";

//$dbname = "a2aqualifiche";


$connect = mysqli_connect($servername, $username, $password, $dbname);
$query = "select targa_veicolo, atar_file, email_inviata_autofficina  FROM `ataraxia_main` where STATUS = 400";
$result = mysqli_query($connect, $query);
//var_dump($result); die();
?>


   <div class="float-right" style="margin-top: 109px;  width: 83%; ">
   <h1 align="center">Automatic Addressed Practices</h1>
   <br>
       
     	 <?php 
		  $opzione = "";
        //while($row = mysqli_fetch_array($result))
        //{ /* $opzione .= '<option value="'.$row["name_society"].'">'.$row["name_society"].'</option>'; */ }
         ?>
	
	
	<div class="col-md-4">
		<label for="searchFields"> Select column </label>
		<select id="searchFields" name="searchFields" class="form-control">
			<option value="lock" selected >Select field</option>
			<option value="0">Station code</option>
			<option value="1">Requested date</option>
			<option value="2">Start Process Date</option>
			<option value="3">Registration Number</option>
			<option value="4">Atar File</option>
			<option value="5">Defect code</option>
			<option value="6">Supplier</option>
			<option value="7">Supplier e-mail</option>
			
		</select>
	</div>
		
	<div class="col-md-4">
		<label id="labelSearch" for="search">Search</label>
		<input name="search" id="search" class="form-control" autocomplete="off" data-date-format="dd/mm/yyyy" disabled /> 
	</div>
	
	<div class="col-md-4">
		<br>
		<button type="button" class="downloadPratices" style="width: 100%;height: 33px;margin-top: 6px;" onclick="location.href='../fetcher_folder/fetch_download_canalizzate.php';"><i class="fa fa-download" aria-hidden="true"></i>
  Download CSV</button>
	</div>
	
	<div class="table-responsive" style="width: 120%; padding-top:20px;" id="daSvuotare" >
		<table id="product_data" class="table table-bordered table-striped display">
			<thead>
				<tr>
					<th style="min-width: 85px; max-width:85px" class="align-top col-name" >
						Station code
					</th>
					<!--qui-->
					<th style="min-width: 120px; max-width:120px" class="align-top" >
						Requested date
					</th>
					<th style="min-width: 125px; max-width:125px" class="align-top" >
						Start Process Date
					</th>
					<th style="min-width: 125px; max-width:125px" class="align-top" >
						Registration Number
					</th>
					<th style="min-width:80px; max-width:800px" class="align-top" >
						Atar File
					</th>	
					<th style="min-width:80px; max-width:800px" class="align-top" >
						Defect_code
					</th>
					<th style="min-width:180px; max-width:200px" class="align-top" >
						Supplier
					</th>
					<th class="align-top" style="min-width:400px; max-width:500px" >
						Supplier e-mail
					</th>	
				</tr>
			</thead>
		</table>
	</div>
<br><br>
 

   
   
   <div id="test_output">
   </div>
   
  </div>

  









<script type="text/javascript" language="javascript" >
//$("#search").datepicker();
$("#searchFields").change(function(){
	$("#search").val('');
	var fieldValue = $( "#searchFields option:selected" ).val();
	var fieldText = $( "#searchFields option:selected" ).text();
	
	if(fieldValue == 'lock'){
		
		$("#search").attr("disabled",true);
		return false;
	}
	
	
	if(fieldValue == 1 || fieldValue == 2){
		$("#search").datepicker({
			dateFormat: 'dd/mm/yy',
			onSelect: function(dateText) {
				var key = dateText;
				$('#daSvuotare').empty();
				$('#daSvuotare').html('<table id="product_data" class="table table-bordered table-striped display"><thead><tr><th style="min-width: 85px; max-width:85px" class="align-top col-name" >Station code</th><th style="min-width: 120px; max-width:120px" class="align-top" >Requested date</th><th style="min-width: 125px; max-width:125px" class="align-top" >Start Process Date</th><th style="min-width: 125px; max-width:125px" class="align-top" >Registration Number</th><th style="min-width:80px; max-width:800px" class="align-top" >Atar File</th><th style="min-width:80px; max-width:800px" class="align-top" >Defect_code</th><th style="min-width:180px; max-width:200px" class="align-top" >Supplier</th><th class="align-top" style="min-width:400px; max-width:500px" >Supplier e-mail</th></tr></thead></table>');	
				load_data(key,fieldValue);
			}
		});	
	}
	else{
		$("#search").datepicker("destroy");
	};
	
	$('#labelSearch').text('Search '+fieldText+':');
	$('#search').removeAttr("disabled");
	$('#search').keyup(function(){
		$('#daSvuotare').empty();
		$('#daSvuotare').html('<table id="product_data" class="table table-bordered table-striped display"><thead><tr><th style="min-width: 85px; max-width:85px" class="align-top col-name" >Station code</th><th style="min-width: 120px; max-width:120px" class="align-top" >Requested date</th><th style="min-width: 125px; max-width:125px" class="align-top" >Start Process Date</th><th style="min-width: 125px; max-width:125px" class="align-top" >Registration Number</th><th style="min-width:80px; max-width:800px" class="align-top" >Atar File</th><th style="min-width:80px; max-width:800px" class="align-top" >Defect_code</th><th style="min-width:180px; max-width:200px" class="align-top" >Supplier</th><th class="align-top" style="min-width:400px; max-width:500px" >Supplier e-mail</th></tr></thead></table>');
		var key = $(this).val();
		load_data(key,fieldValue);
		console.log(fieldValue);
	});
});


var elementioptions = [];

function load_data(search,field)
 {
  var dataTable = $('#product_data').DataTable({
   "processing":true,
    language: {
        "processing": "Caricamento in corso. Attendere Prego..."
    },
   "serverSide":true,
    "language": {
      "info": " From _START_ to _END_  of _TOTAL_",
    },
   "order":[],
   "ajax":{
    url:"/fetcher_folder/fetch_canalizzate.php",
    type:"POST",
    data:{search: search,field:field}
   },
   "columnDefs":[
    {
     "targets":[0],
     "orderable":true,
    },
    {
     "targets":[1],
     "orderable":true,
    },
	{
     "targets":[2],
     "orderable":true,
    },
	{
     "targets":[3],
     "orderable":true,
    },
	{
     "targets":[4],
     "orderable":true,
    },
	{
     "targets":[5],
     "orderable":true,
    },
	{
     "targets":[6],
     "orderable":true,
    },
	{
     "targets":[7],
     "orderable":true,
    }
	],
  });
  
  
  //$( "#product_data_length" ).prepend( "<button type='button' class='btn btn-success' id='applicafilterfsc' style='margin-bottom: 4px; margin-right: 30px; ' >Applica Filtro</button> <select name='societa' id='societa' class='form-control'> <optgroup label='Società'> <?php echo $opzione; ?> </optgroup></select>");
  //$( "#societa_ms" ).append( '<div > <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"><button class="btn" onclick="location.href=\'/fetch_download_durc.php\';" ><i class="fa fa-download"></i> Scarica tutte le partite aperte </button></div>');
  
  
 }




$(document).ready(function(){

 load_data();


 $(document).on('change', '#societa', function(){
  var societa = $(this).val();
  //$('#product_data').DataTable().destroy();
  if(societa != '')
  {
   //load_data(societa);
     
  }
  else
  {
   load_data();
  }
 });
 
 
/*$(document).on('click', '.downloadPratices', function(){
	
	$.ajax({
		url : '../fetcher_folder/fetch_download_canalizzate.php',
		type : 'POST',
		dataType:'json',
	});
});
 */
 
 
 
});









</script>




<script>

//Solo Cassetto Fiscale

$(document).ready(function () {
	
	$("a").click(function(event) {
						
                   //Non Usato
				   
       	});    // Chiusura evento click su anchor	
	});    // Chiusura document.ready
	
						
						
						
						
						
	function attivazione_download_metadati (identificativo) {					
						//alert('link cliccato id: '+identificativo );
						
						$.getJSON('/ajax/dyn_change_cassetto_fiscale_metadati_status.php?id='+identificativo, function (data) {
						console.log(data);
						});  //Chiusura 
						
						
						var showDatamessoutput = $('#cambio_val_icon_'+identificativo);
						showDatamessoutput.empty();
						showDatamessoutput.html('<center> <span class="glyphicon glyphicon-cloud-upload"></span> </center>');		
						
						
						/*
					    // Questo è il tasto di Gestione trigger metadati
						if ($(this).attr('class').indexOf('attivazione-metadati') > -1) {
							//alert ("Ho cliccato rilavsm");
							
								var txtmsg = "";
								//var showDatamessoutput = $('#messaggiinoutput');
								//showDatamessoutput.empty();
								
								
								
								if (confirm("Vuoi procedere con il download dei metadati?")) {
									txtmsg = 'OK processo download Metadati attivato per questa lavorazione';
									
									//$('#'+$(this).parents("tr").attr('id')).addClass( "nonmostraregialavorato");
									
									//$('#'+$(this).parents("tr").attr('id')).hide();
									
									
									
								//$.getJSON('gestione-lavorazioni/rilavsm35.php?id='+$(this).parents("tr").attr('id'), function (data) {
								//console.log(data);
								//});  //Chiusura 
									
									
								} else {
									txtmsg = 'Non Confermato attivazione Metadati'; 
								}
								
								
								//showDatamessoutput.html(txtmsg);
								//$("#messaggiinoutput").fadeTo(2000, 500).slideUp(500, function(){
								//$("#messaggiinoutput").slideUp(500);
							    alert(txtmsg);
							
						}  //Chiusura ricerca tasto Abilitazione Trigger Metadati
	                  */
	
		}  // Chiusura funzione per attivazione download del metadato


    
	
	function attivazione_download_fatture (identificativo) {					
						//alert('link cliccato id: '+identificativo );
						
						$.getJSON('/ajax/dyn_change_cassetto_fiscale_fatture_status.php?id='+identificativo, function (data) {
						console.log(data);
						});  //Chiusura 
						
						
						var showDatamessoutput = $('#cambio_val_icon_'+identificativo);
						showDatamessoutput.empty();
						showDatamessoutput.html('<center> <span class="glyphicon glyphicon-cloud-upload"></span> </center>');		
	
		}  // Chiusura funzione per attivazione download del metadato





</script>






<script>






	    
		var $callback = $("#societa");

		$("#societa").multiselect({
		 
			  click: function(event, ui){
			  //$callback.text(ui.value + ' ' + (ui.checked ? 'checked' : 'unchecked') );
			  if (ui.checked) { elementioptions.push(ui.value);} else { elementioptions.splice(elementioptions.indexOf(ui.value),1); }
			  
			  //console.log(elementioptions);
			  //console.log("test Fiascojob");
			  
		   },
		 
		   autoReset: true,
		   uncheckAll: true,

		   checkAll: function(){
			  $callback.text("Check all clicked!");
		   },
		   uncheckAll: function(){
			  $callback.text("Uncheck all clicked!");
		   },
		   close: function(){
			   
			   
			  var txtjoinffgghh = elementioptions.join("_");
			  console.log("FIASCOJOB: "+elementioptions.join("_"));
			  if (txtjoinffgghh != "") {
			  $('#product_data').DataTable().destroy();
			  load_data(elementioptions.join("_")); 
			  }else {
				  
			  $('#product_data').DataTable().destroy();
			  load_data(); 
				  
			  }
			   
			   
			  //$callback.text("Select closed!");
			  //var txtjoinffgghh = elementioptions.join("|");
			  //console.log("FIASCOJOB: "+elementioptions.join("|"));
			  //if (txtjoinffgghh != "") {
			  //load_data(elementioptions.join("|")); 
			  //}
		   },   
		});



		//$('#societa').multiselect("deselectAll", true).multiselect("refresh");
		$("#societa").multiselect("widget").find('.ui-multiselect-none').click();
         
		 //$("#applicafilterfsc")
		 
		  $(document).on('click', '#applicafilterfsc', function(){
               var txtjoinffgghh = elementioptions.join("_");
			  console.log("FIASCOJOB: "+elementioptions.join("_"));
			  if (txtjoinffgghh != "") {
			  $('#product_data').DataTable().destroy();
			  load_data(elementioptions.join("_")); 
			  }else {
				  
			  $('#product_data').DataTable().destroy();
			  load_data(); 
				  
			  }
			  
          });
		  
		  
		  
		  
		  

</script>





<style>
div.dataTables_wrapper div.dataTables_processing {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 200px;
    margin-left: -100px;
    margin-top: -26px; 
    text-align: center;
    padding: 1em 0;
	color: orangered;
	/* background-color: white; */
}

#product_data_wrapper {
	min-height: 200px;
	
}

#societa_ms{
	max-width: 146px;
	max-height: 30px;
}

#product_data_filter {
	display: none !important;
}

select.form-control:not([size]):not([multiple]) {
    height: calc(2.25rem + 5px);
    width: 98%;
}

.ui-multiselect-header {
    margin-bottom: 3px;
    padding: 3px 0 3px 4px;
    display: none;
}



.glyphicon {
    font-size: 29px;
}

.glyphicon.glyphicon-plus {
    font-size: 29px;
	color: darkorange;
}
.glyphicon.glyphicon-ok {
    font-size: 29px;
	color: green;
}

.glyphicon.glyphicon-retweet {
    font-size: 29px;
	color: orange;
}

.glyphicon.glyphicon-cloud-upload {
    font-size: 29px;
	color: grey;
}


</style>





<?php

function givemelastupdate($parametro) {	
		global $servername;
		global $username;
		global $password;
		$dbname = "durc";
        //global $tab_main_pratiche;
		
		  $conn = new mysqli($servername, $username, $password, $dbname);
     	  if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error);  } 

		  $sql = "SELECT * FROM `tbl_parametri` WHERE `parametro` LIKE '$parametro'";
		  //$sql = "SELECT `id_univoco` FROM `$tab_main_pratiche` where stato like 'Chiusa' and stato_pratica_web = 440";
		  $result = $conn->query($sql);

		if ($result->num_rows > 0) {
			
			//$row = $result->fetch_assoc();
			//$daritorno = $row["id_univoco"];
			//return $daritorno;
			
			// output data of each row
			$indic = 0;
			while($row = $result->fetch_assoc()) {
				
		                $daritorno = $row["data_aggiornamento"];
						$indic++;
			 } 
			  //Chiusura While
			   return $daritorno;	
			} else { return "0"; }
		    $conn->close();	
	}  //selectarrfromonetable

?>			  
			


			
			  
			  
			  
			  

	
	
	<?php 
	/*
		<div class="col-md-2">
		  <table class="table table-bordered">
					
					<thead>
						<tr >
							<th colspan="2" style="text-align: center">
								Processo di STOP / RUN Lavorazioni
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
	*/
	?>
	
	
	
	
	
	
	
	
			  
			  
			  
                 <!-- /. ROW  -->           
            </div>
             <!-- /. PAGE INNER  -->
        
             </div>
             <!-- /. PAGE WRAPPER  -->
		 
		 
<?php  /* require_once('../footer-pag.php');     */ ?>		  