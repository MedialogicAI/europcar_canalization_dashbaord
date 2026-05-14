 <?php  require_once('header-pag.php');   ?>  
 
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<style>
				body {
					font-family: Arial, Helvetica, sans-serif;
					background-color: black;
				}

				* {
					box-sizing: border-box;
				}

				/* Add padding to containers */
				.container {
					padding: 16px;
					background-color: white;
					width: 768px;
				}

				/* Full-width input fields */
				input[type=text], input[type=password] {
					width: 100%;
					padding: 15px;
					margin: 5px 0 22px 0;
					display: inline-block;
					border: none;
					background: #f1f1f1;
				}

				input[type=text]:focus, input[type=password]:focus {
					background-color: #ddd;
					outline: none;
				}

				/* Overwrite default styles of hr */
				hr {
					border: 1px solid #f1f1f1;
					margin-bottom: 25px;
				}

				/* Set a style for the submit button */
				.registerbtn {
					background-color: #4CAF50;
					color: white;
					padding: 16px 20px;
					margin: 8px 0;
					border: none;
					cursor: pointer;
					width: 100%;
					opacity: 0.9;
				}

				.registerbtn:hover {
					opacity: 1;
				}

				/* Add a blue text color to links */
				a {
					color: dodgerblue;
				}

				/* Set a grey background color and center the text of the "sign in" section */
				.signin {
					background-color: #f1f1f1;
					text-align: center;
				}
				</style>
 

<script type='text/javascript' src='http://code.jquery.com/jquery.min.js'></script> 

 <script>
 
 
 
 
var abilitabottone = ["1", "1", "1", "1"];
 // Email owner ,   email referente  ,   cap ,   repeat_password
 // Se abilitabottone sta a tutti 0 abilitare bottone
 
 
 
 $(document).ready(function () {
  //your code here
 
 
         var disab_uno_almeno = 0;
         
 
 
 
		 $('#psw, #psw-repeat').on('keyup', function(event){
		});   //Chiusura Keyup
		

		
		
		$('body').on('keyup', function(event){
		//$('body').change(function(event){
		
		var sEmail = $('#email_owner').val();
        if ($.trim(sEmail).length == 0) {
            console.log('Please enter owner valid email address');
			//$('.registerbtn').prop('disabled', true);
            //disab_uno_almeno = 1;
			abilitabottone [0] = "1";
			event.preventDefault();
        }
        if (validateEmail(sEmail)) {
			console.log('EMAIL Owner OK');
            //$('.registerbtn').prop('disabled', false);
			//disab_uno_almeno = 0;
			abilitabottone [0] = "0";
        }
        else {
			//disab_uno_almeno = 1;
			abilitabottone [0] = "1";
            //$('.registerbtn').prop('disabled', true);
            event.preventDefault();
        }
	


	
		
		var srefEmail = $('#email_referente').val();
        if ($.trim(srefEmail).length == 0) {
            console.log('Please enter referente valid email address');
			//disab_uno_almeno = 1;
			abilitabottone [1] = "1";
			//$('.registerbtn').prop('disabled', true);
            event.preventDefault();
        }
        if (validateEmail(srefEmail)) {
			console.log('Email referente OK');
           //$('.registerbtn').prop('disabled', false);
		   //disab_uno_almeno = 0;
		   abilitabottone [1] = "0";
        }
        else {
            //disab_uno_almeno = 1;
			//$('.registerbtn').prop('disabled', true);
			abilitabottone [1] = "1";
            event.preventDefault();
        }
		 
		
		
		
		
		
		
		var srefCAP = $('#cap').val();
		//console.log ("CAP: "+isNaturalNumber( srefCAP ));
        if ($.trim(srefCAP).length == 0) {
            console.log('Lunghezza campo CAP non corrispondente');
            //disab_uno_almeno = 1;
			abilitabottone [2] = "1";
			//$('.registerbtn').prop('disabled', true);
			event.preventDefault();
        }
        else if ( isNaturalNumber( srefCAP ) ) {
			console.log('CAP OK');
            //$('.registerbtn').prop('disabled', false);
			abilitabottone [2] = "0";
			//disab_uno_almeno = 0;
        }
        else {
			console.log('CAP non congruente quindi non numero intero');
            //disab_uno_almeno = 1;
			abilitabottone [2] = "1";
			//$('.registerbtn').prop('disabled', true);
            event.preventDefault();
        }
		
		
		
		
		
		
		
		
		//INIZIO Controllo coerenza Password 
		
		   event.preventDefault();
		  if ($('#psw').val() == $('#psw-repeat').val())  {
			$('#msg_passw').html('Verificata').css('color', 'green');
            //$('.registerbtn').prop('disabled', false);
			abilitabottone [3] = "0";
			//disab_uno_almeno = 0;
			//$('.registerbtn').removeAttr("disabled");
		} else {
			$('#msg_passw').html('Non corrispondente').css('color', 'red');
			//disab_uno_almeno = 1;
			abilitabottone [3] = "1";
			//$('.registerbtn').prop('disabled',true);
			//$('.registerbtn').attr("disabled");
		   }
		   
		   
		 if (($('#psw').val() == '') || ($('#psw-repeat').val() == '')) {
           
			$('#msg_passw').html('Inserire e ripetere la password').css('color', 'darkblue');
			//$('.registerbtn').prop('disabled',true);		   
			 //disab_uno_almeno = 1;
			 abilitabottone [3] = "1";
		 }
		
		//FINE Controllo coerenza Password 
		
		
		
		
		
		
		
		
		//console.log("disab:"+disab_uno_almeno);
		if (abilitabottone.includes('1')){
			
			
			var indexab = abilitabottone.indexOf("1"); // 1
			
			console.log("indice errato che sta ad 1:  " + indexab);
			
			
			$('.registerbtn').prop('disabled',true);
			
			$('.registerbtn').tooltip('enable');
			
			
			
			switch(indexab) {
				case 0:
					$('.registerbtn').attr('title', "Occorre impostare correttamente l\'email principale che verrà utilizzata per il corretto accesso all\'account ");
					break;
				case 1:
					$('.registerbtn').attr('title', "Occorre impostare correttamente l\'email del referente");
					break;
                case 2:
					$('.registerbtn').attr('title', "Occorre impostare correttamente il Codice avviamento postale");
					break;
				case 3:
					$('.registerbtn').attr('title', "Occorre che la password sia congruente");
					break;
			}  // Chiusura switch case
			
			//$('.registerbtn').attr('title', 'test messaggio fiascojob');
            
			$('.registerbtn').tooltip('fixTitle');
            $('.registerbtn').tooltip('show');
			
		} else { 
		$('.registerbtn').prop('disabled',false); 
		$('.registerbtn').tooltip('disable');
	    //$('.registerbtn').attr('title', '')
        //$('.registerbtn').tooltip('fixTitle');
		//$('.registerbtn').tooltip('show');
		}
		
		
		
		
		
		
		
		
       });   //Chiusura on change all del document ready

		
		
		
		
		$(function(){
		
		
		//$('.registerbtn').prop('disabled',true);
		
		
		});



		
		
		
		
		

});  // Chiusura Document.ready


		
     function validateEmail(email){
		 var allowed=/^([a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$)/; 
	 	 if (allowed.test(email))
		 return true;
	 else return false;
	 
	 }
	 
	 
	 
	 
	 
	 
	 function isNaturalNumber(n) {
    n = n.toString(); // force the value incase it is not
    if (!isNaN(n)) //true 
	{return 1; } 
	else {return 0; }
    }


	 
	 
	 
	 

	 
	 
	 


	 
	 

		




</script>
 
 
 
               

            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                     <!-- <h2>Registrazione RoboDURC</h2>    -->
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />
              
								<form action="/pst_new_registration.php"  method="post">
								  <div class="container">
									<h1>Modulo di Registrazione RoboDURC</h1>
									<p>Inserire i dati idenficativi per procedere nella registrazione.</p>
									<hr>
                                  
								  
								    <label for="nomer"><b>Nome Referente*</b></label>
									<input type="text" placeholder="Inserire Nome Referente" name="nomer" id ="nomer" required>


								  
								    <label for="cognomer"><b>Cognome Referente*</b></label>
									<input type="text" placeholder="Inserire Cognome Referente" name="cognomer" id ="cognomer" required>
								    								    

								  
								    <label for="indirizzo"><b>Indirizzo*</b></label>
									<input type="text" placeholder="Inserire Indirizzo" name="indirizzo" id ="indirizzo" required>

									
								    <label for="Nazione"><b>Nazione*</b></label>
									<input type="text" placeholder="Inserire Nazione" name="nazione" id ="nazione" required>																											
									
									
								    <label for="provincia"><b>Provincia*</b></label>
									<input type="text" placeholder="Inserire Provincia" name="provincia" id ="provincia" required>																											

								    <label for="Citta"><b>Città*</b></label>
									<input type="text" placeholder="Inserire Città" name="citta" id ="citta" required>																											
																		
								    <label for="CAP"><b>CAP*</b></label>
									<input type="text" placeholder="Inserire CAP" name="cap" id ="cap" required>																											
																		
								    <label for="recapito_telefonico"><b>Numero di Telefono*</b></label>
									<input type="text" placeholder="Inserire Numero di telefono" name="numero_telefono" id="numero_telefono"  required>																											

									<label for="email_referente"><b>Indirizzo e-mail Referente*</b></label>
									<input type="text" placeholder="Inserire Indirizzo E-mail Referente" name="email_referente" id="email_referente" required>
									   

									<label for="cod_fiscale"><b>Codice Fiscale*</b></label>
									<input type="text" placeholder="Inserire Codice Fiscale" name="cod_fiscale" id="cod_fiscale" required>																											
									   
									<label for="p_iva"><b>Partita I.V.A.*</b></label>
									<input type="text" placeholder="Inserire Partita I.V.A." name="p_iva" id="p_iva" required>																											
									   
									<label for="ragione_sociale"><b>Ragione sociale*</b></label>
									<input type="text" placeholder="ragione_sociale" name="ragione_sociale" id="ragione_sociale" required>																											
									   
																		   
 									
									<hr>
									
									
 
									
   
									<label for="email"><b>Email* (verra utilizzata per il login di questo account)</b></label>
									<input type="text" placeholder="Inserisci Email Account" name="email_owner" id="email_owner" required>

									<label for="psw"><b>Password*</b></label>
									<input type="password" id="psw" placeholder="Imposta la tua Password" name="psw" required>

									<label for="psw-repeat"><b>Repeat Password*</b> <div id='msg_passw'></div> </label>
									<input type="password" placeholder="Ripeti la Password" id="psw-repeat" name="psw-repeat" required>
									 
									 <br><br><br>
									<hr>
									
									
									
									<p>Creando questo account prendi accettazione dei nostri termini e delle condizioni del servizio offerto <a href="#">Terms & Privacy</a>.</p>
 
                                    
									<button type="submit" class="registerbtn" id="d_registerbtn" disabled="disabled" >Registra il mio account</button>
								  </div>
								  
								  <div class="container signin">
									<p>Hai già le credenziali di accesso? <a href="#">Accedi</a>.</p>
								  </div>
								</form>
			  
			  
                 <!-- /. ROW  -->           
            </div>
             <!-- /. PAGE INNER  -->
        
		

		
		
		
<?php  require_once('footer-pag.php');   ?>		 