 <?php require_once('dbconf.php'); ?>
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

 
 
 
               
            <div class="container">
			
					<h1>Processo di Registrazione RoboDURC</h1>
					<hr>
			
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                     <!-- <h2>Registrazione RoboDURC</h2>    -->
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />

			       <label for="outputmsg"><b> <div id='msg_out'></div> </b></label>


                 <!-- /. ROW  -->           
            </div>
			</div>
             <!-- /. PAGE INNER  -->
        
		

		
		
		
<?php  require_once('footer-pag.php');   ?>		 

			  











<?php

// Definisco le variabili che mi servono per poi controllarle e inserirle successivamente nel database
$nomer = $cognomer = $indirizzo = $nazione = $provincia = $citta = $cap = $numero_telefono = $email_referente = $cod_fiscale = $p_iva = $ragione_sociale = $email_owner = $psw = $psw_repeat = "";



//$nomer
//$cognomer
//$indirizzo
//$nazione
//$provincia
//$citta
//$cap
//$numero_telefono
//$email_referente
//$cod_fiscale
//$p_iva
//$ragione_sociale
//$email_owner
//$psw        
//$psw_repeat




//$nomer
//$cognomer
//$indirizzo
//$nazione
//$provincia
//$citta
//$cap
//$numero_telefono
//$email_referente
//$cod_fiscale
//$p_iva
//$ragione_sociale
//$email_owner
//$psw       



//exit ("<script> $('#msg_out').html('Test inserimento messaggio post elaborazione').css('color', 'Black'); </script>");




if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $nomer             =  test_input($_POST['nomer']);
    $cognomer          =  test_input($_POST['cognomer']);
    $indirizzo         =  test_input($_POST['indirizzo']);
    $nazione           =  test_input($_POST['nazione']);
    $provincia         =  test_input($_POST['provincia']);
    $citta             =  test_input($_POST['citta']);
    $cap               =  test_input($_POST['cap']);
    $numero_telefono   =  test_input($_POST['numero_telefono']);
    $email_referente   =  test_input($_POST['email_referente']);
    $cod_fiscale       =  test_input($_POST['cod_fiscale']);
    $p_iva             =  test_input($_POST['p_iva']);
    $ragione_sociale   =  test_input($_POST['ragione_sociale']);
    $email_owner       =  test_input($_POST['email_owner']);
    $psw               =  test_input($_POST['psw']);        
    $psw_repeat        =  test_input($_POST['psw-repeat']);

}  //Chiusura if del controllo del corretto POST Method
else {  exit ("<script> $('#msg_out').html('Errore di registrazione, E\' stata inviata una notifica all\' amministratore, ogni abuso verrà perseguito').css('color', 'Black'); </script>");  }





// Inizio controlli sintattici lato server  ( Lato client In javascript già li avevo fatto quindi se ho un anomalia rispondo con un messaggio di errore bloccante)




if (valid_em($email_owner) == 0)     { exit ("<script> $('#msg_out').html('Errore di registrazione, E\' stata inviata una notifica all\' amministratore, ogni abuso verrà perseguito').css('color', 'Black'); </script>");  } 
if (valid_em($email_referente) == 0) { exit ("<script> $('#msg_out').html('Errore di registrazione, E\' stata inviata una notifica all\' amministratore, ogni abuso verrà perseguito').css('color', 'Black'); </script>");  } 

$psw = pwd_input($psw);
$psw_repeat = pwd_input($psw_repeat);

if ($psw != $psw_repeat) { exit ("<script> $('#msg_out').html('Errore di registrazione, E\' stata inviata una notifica all\' amministratore, ogni abuso verrà perseguito').css('color', 'Black'); </script>");  }

if (!(is_numeric ($cap)) && (strlen($cap) == 5 )) { exit ("<script> $('#msg_out').html('Errore di registrazione, E\' stata inviata una notifica all\' amministratore, ogni abuso verrà perseguito').css('color', 'Black'); </script>");  }



if (strlen($cod_fiscale) < 2) { exit ("<script> $('#msg_out').html('Errore di registrazione, E\' stata inviata una notifica all\' amministratore, ogni abuso verrà perseguito').css('color', 'Black'); </script>");  }
if (strlen($p_iva) < 2) { exit ("<script> $('#msg_out').html('Errore di registrazione, E\' stata inviata una notifica all\' amministratore, ogni abuso verrà perseguito').css('color', 'Black'); </script>");  }
if (strlen($ragione_sociale) < 2) { exit ("<script> $('#msg_out').html('Errore di registrazione, E\' stata inviata una notifica all\' amministratore, ogni abuso verrà perseguito').css('color', 'Black'); </script>");  }
if (strlen($numero_telefono) < 2) { exit ("<script> $('#msg_out').html('Errore di registrazione, E\' stata inviata una notifica all\' amministratore, ogni abuso verrà perseguito').css('color', 'Black'); </script>");  }
if (strlen($nomer) < 2) { exit ("<script> $('#msg_out').html('Errore di registrazione, E\' stata inviata una notifica all\' amministratore, ogni abuso verrà perseguito').css('color', 'Black'); </script>");  }
if (strlen($cognomer) < 2) { exit ("<script> $('#msg_out').html('Errore di registrazione, E\' stata inviata una notifica all\' amministratore, ogni abuso verrà perseguito').css('color', 'Black'); </script>");  }
if (strlen($indirizzo) < 2) { exit ("<script> $('#msg_out').html('Errore di registrazione, E\' stata inviata una notifica all\' amministratore, ogni abuso verrà perseguito').css('color', 'Black'); </script>");  }
if (strlen($nazione) < 2) { exit ("<script> $('#msg_out').html('Errore di registrazione, E\' stata inviata una notifica all\' amministratore, ogni abuso verrà perseguito').css('color', 'Black'); </script>");  }
if (strlen($provincia) < 2) { exit ("<script> $('#msg_out').html('Errore di registrazione, E\' stata inviata una notifica all\' amministratore, ogni abuso verrà perseguito').css('color', 'Black'); </script>");  }
if (strlen($citta) < 2) { exit ("<script> $('#msg_out').html('Errore di registrazione, E\' stata inviata una notifica all\' amministratore, ogni abuso verrà perseguito').css('color', 'Black'); </script>");  }





$email_owner = trim($email_owner);

//Qui faccio l'inserimento utente, dopo che ho concluso tutti i controlli di "Massima" sintattici e di correttezza dei campi.





// Inizio con il controllare che la email owner e/o email principale non sia attualmente presente sulla tabella utenti durc

//echo ("<script> $('#msg_out').html('Procedo con il controllo dell\'email doppiona nel database').css('color', 'Black'); </script>");

if (check_email_owner_already_exist($email_owner) == 1) { exit("<script> $('#msg_out').html('Attenzione Email Principale già registrata nel portale, procedere con il recupera password per accedere').css('color', 'Black'); </script>"); }









$id_primary = registra_utente_login($email_owner, $psw);


$registr_id_2nd = registra_utente_dati($nomer, $cognomer, $indirizzo, $nazione, $provincia, $citta, $cap, $numero_telefono, $email_referente, $cod_fiscale, $p_iva, $ragione_sociale, $id_primary);




if ($registr_id_2nd > 0){
echo ("<script> $('#msg_out').html('Registrazione Utente Avvenuta con successo. Occorre attendere l\'abilitazione da parte di un operatore Medialogic AI per effettuare il login.').css('color', 'Black'); </script>");
}
else {
	
	echo ("<script> $('#msg_out').html('Problema di registrazione. Per informazioni assistenza.automi@medialogicai.it').css('color', 'Red'); </script>");
	
	}
	





















function registra_utente_login($email_owner, $psw) {

	global $servername;	global $username; global $password; global $dbname;
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error);  } 
		  
	$sql = "INSERT INTO `utenti_durc` (`id`, `email`, `password`, `privilegi`, `abilitazione_accesso`) VALUES (NULL, '$email_owner', '$psw', '3', '0');";
	
	$result = $conn->query($sql);
    $last_id = $conn->insert_id;
	$conn->close();

	return $last_id;
}



//$nomer
//$cognomer
//$indirizzo
//$nazione
//$provincia
//$citta
//$cap
//$numero_telefono
//$email_referente
//$cod_fiscale
//$p_iva
//$ragione_sociale
//$email_owner
//$psw       


function registra_utente_dati($nomer, $cognomer, $indirizzo, $nazione, $provincia, $citta, $cap, $numero_telefono, $email_referente, $cod_fiscale, $p_iva, $ragione_sociale, $last_id_fk) {

	global $servername;	global $username; global $password; global $dbname;
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error);  } 

    $data_reg = date('Y-m-d H:i:s');	
	
	$sql = "INSERT INTO `registrazione_web` (`id`, `nome`, `cognome`, `indirizzo`, `nazione`, `provincia`, `citta`, `cap`, `numero_telefono`, `email_referente`, `cod_fiscale`, `p_iva`, `ragione_sociale`, `data_registrazione`, `fk_utenti_durc`) VALUES (NULL, '$nomer', '$cognomer', '$indirizzo', '$nazione', '$provincia', '$citta', '$cap', '$numero_telefono', '$email_referente', '$cod_fiscale', '$p_iva', '$ragione_sociale', '$data_reg', '$last_id_fk');";
	
	$result = $conn->query($sql);
    $last_id = $conn->insert_id;
	$conn->close();

	return $last_id;
}












function check_email_owner_already_exist ($email) {	
	global $servername; global $username; global $password; global $dbname;

	$conn = new mysqli($servername, $username, $password, $dbname);
 	if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error);  } 

	$sql = "SELECT * FROM `utenti_durc` where email like '$email' ";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		// output data of each row
		//$row = $result->fetch_assoc();
		//$daritorno = $row["html_stato"];
		return 1;
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







function valid_em($email){
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      //$emailErr = "Invalid email format"; 
	  return 0;
    }
	else {return 1; }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function pwd_input($data) {
  $data = trim($data);
  $data = addslashes($data);
  //$data = stripslashes($data);
  //$data = htmlspecialchars($data);
  return $data;
}





?>





