<?php

require_once('dbconf.php');

// Mandatory define variables and set to empty values
$md5_accesskey = $cod_fiscale = $data_richiesta = $email_richiedente = $purpose_id = $pdf_needed = "";


// Optional define variables and set to empty values





//INIZIO Controlli Access Key
if(isset($_REQUEST['accesskey'])) {  
$md5_accesskey = $_REQUEST['accesskey'];
$akc = new accesskey_control; 
$isvalid_format_accesskey = $akc->isValidMd5format($md5_accesskey);
    if ($isvalid_format_accesskey == 0) { $myObj->response = "Access Key Not Valid";  $myJSON = json_encode($myObj); echo $myJSON;  exit(); }
	else {
		  $accesskey_exist_in_db = $akc->accesskey_exist_for_one_customer($md5_accesskey);
		  if ($accesskey_exist_in_db == 0) { $myObj->response = "Access Key Not Valid";  $myJSON = json_encode($myObj); echo $myJSON;  exit(); } 
		  else {
			  $get_accesskey_credit = $akc->get_accesskey_credit($md5_accesskey);
			  if ($get_accesskey_credit == "0") { $myObj->response = "No Credit to spent"; 
			                                      $myObj->credit = "0";  
												  $myJSON = json_encode($myObj); echo $myJSON;  exit(); 
												  } 
			  else if ($get_accesskey_credit == "-1") { 
			                                             $myObj->response = "Enabled"; 
														 //$myObj->credit = "Unlimit"; 
														 $conteggia = 0;  
														 } 
			  else {  $myObj->response = "Enabled";  
			          //$myObj->credit = $get_accesskey_credit; 
					  $conteggia = 1;  }	  
		  }
	    } // chiusura else di accesskey valida 
      }else { $myObj->response = "accesskey is Required";  $myJSON = json_encode($myObj); echo $myJSON;  exit();} 
//FINE Controlli Access Key










//INIZIO Controlli  Codice Fiscale	  
	if(isset($_REQUEST['codice_fiscale'])) {
	$cod_fiscale = $_REQUEST['codice_fiscale'];	  
	$cfc = new codice_fiscale_control; 
	   //Controllo_Lunghezza caratteri minimi
	   $minmax_lngt = strlen($cod_fiscale);
		 if ($minmax_lngt < 9 || $minmax_lngt > 30) { $myObj->response = "codice_fiscale wrong length";  $myJSON = json_encode($myObj); echo $myJSON;  exit(); } 
		 else {
			  //La lunghezza minima/massima del codice fiscale è confermata 				
					//Inizio con il cercare se già esiste una lavorazione per questo codice fiscale
					  $arr_field_cf = $cfc->get_cf_field_from_a2adurc($cod_fiscale);
		 } // Chiusura Else
	} else { $myObj->response = "codice_fiscale is Required";  $myJSON = json_encode($myObj); echo $myJSON;  exit();} 
//FINE Controlli  Codice Fiscale	 









//INIZIO Controlli  purpose_id	  
if(isset($_REQUEST['purpose_id'])) {
$purpose_id = strtolower($_REQUEST['purpose_id']);	  
$clas_purid = new purpose_id_control; 
$user_customer_id = $clas_purid->get_customer_id_from_usertable($md5_accesskey);
	$lunghezza_str_purid = strlen ($purpose_id);
	if ($lunghezza_str_purid <= 4) { $myObj->response = "purpose_id is wrong";  $myJSON = json_encode($myObj); echo $myJSON;  exit(); }
	else {
	$primi4char = substr($purpose_id, 0, 4);
	$check_sintattico_pid = strtolower ($primi4char);
	if (!(($check_sintattico_pid == "def_") || ($check_sintattico_pid == "cus_"))) { $myObj->response = "purpose_id is wrong";  $myJSON = json_encode($myObj); echo $myJSON;  exit(); }
	     else {
			 if ($check_sintattico_pid == "def_") { 
			       $esiste_nella_lista = $clas_purid->check_if_exist_this_default_purid($purpose_id); 
                   if ($esiste_nella_lista == 0) { $myObj->response = "purpose_id is wrong";  $myJSON = json_encode($myObj); echo $myJSON;  exit(); }
				   //else { echo "questo else è da togliere ma il purpose_id è OK"; }
			 }
			 else if ($check_sintattico_pid == "cus_") { 
                   //Qui dentro faccio un secondo controllo di validità riferita al cliente
				   $lunghezza_str_purid_cliente_from_db = strlen ("cus_".$user_customer_id);
				   $controllo_cliente = substr($purpose_id, 0, $lunghezza_str_purid_cliente_from_db);
				   //echo "<br>";
				   //echo "cus_".$user_customer_id;
				   //echo "<br>";
				   if ( $controllo_cliente ==  "cus_".$user_customer_id) { 
				   //echo "Yes il cliente dell'accesskey è lo stesso del richiedente "; 
				                    $esiste_nella_lista = $clas_purid->check_if_exist_this_default_purid($purpose_id);
				                    if ($esiste_nella_lista == 0) { $myObj->response = "purpose_id is wrong";  $myJSON = json_encode($myObj); echo $myJSON;  exit(); }
				                    //else { echo "questo else è da togliere ma il purpose_id è OK"; }
				   }
				   else { 
				      //echo "No il cliente è diverso da quello del richiedente"; 
					  $myObj->response = "purpose_id is wrong";  $myJSON = json_encode($myObj); echo $myJSON;  exit();
				   }
			 } // chiusura else controllo cus_ sintattico
		 } // Chiusura else controllo sintatticamente valido ora faccio l'eccezione per def_ o singolare cus_ (quest'ultimo per il customer richiedente)
	} //chiusura Else
} else { $myObj->response = "purpose_id is Required";  $myJSON = json_encode($myObj); echo $myJSON;  exit();} 
//FINE Controlli  purpose_id	 











//INIZIO Controlli  se è settato pdf_needed
	if(isset($_REQUEST['pdf_needed'])) {
	$pdfboolvalue = $_REQUEST['pdf_needed'];	  
	      if ($pdfboolvalue == '1') {}
		  else if ($pdfboolvalue == '0') {}
		  else { $myObj->response = "pdf_needed field is wrong value need to be 0 to know only the result or 1 to download also the PDF";  $myJSON = json_encode($myObj); echo $myJSON;  exit(); }
	} else { $myObj->response = "pdf_needed field is Required 0 to know only the result or 1 to download also the PDF";  $myJSON = json_encode($myObj); echo $myJSON;  exit();   } 
//FINE Controlli  se è settato pdf_needed	







//INIZIO Controlli  se è settato email_richiedente
	if(isset($_REQUEST['email_richiedente'])) {
	$email_richiedente = test_input($_REQUEST['email_richiedente']);
	      // Quando è Settato il presente campo inizio con il fare i controlli sintattici dell'email valida
		  
		  
			if (!filter_var($email_richiedente, FILTER_VALIDATE_EMAIL)) {
			  $emailErr = "Invalid email format"; 
			  $myObj->response = "email_richiedente field is in Invalid email format";  $myJSON = json_encode($myObj); echo $myJSON;  exit();
			}
		  
		  
		  
	} else { 
         // Quando non è settato il presente campo prendo i valori di default   
        $clas_email_req = new email_richiedente_control; 
		$email_richiedente = $clas_email_req->get_default_email_richiedente_from_a2adurc($md5_accesskey);
	} //Chiusura del campo quando non è settato email_richiedente
//FINE Controlli  se è settato email_richiedente	







//INIZIO Controlli  se è settato dettaglio_chiamante
	if(isset($_REQUEST['dettaglio_chiamante'])) {
		
	    $dettaglio_chiamante = test_input($_REQUEST['dettaglio_chiamante']);	  
		if (strlen($dettaglio_chiamante) == 0) { $myObj->response = "dettaglio_chiamante field is Required to make the request";  $myJSON = json_encode($myObj); echo $myJSON;  exit(); }
	} else { 
         // Quando non è settato il presente campo prendo i valori di default         
         $myObj->response = "dettaglio_chiamante field is Required to make the request";  $myJSON = json_encode($myObj); echo $myJSON;  exit();
	} //Chiusura del campo quando non è settato dettaglio_chiamante
//FINE Controlli  se è settato dettaglio_chiamante	




//INIZIO Preimpostazione data al fine di controllo
$data_di_oggi = $data_richiesta = date("Y-m-d H:i:00");

$key_link_dinamico = md5(date("Y-m-d H:i:s"));





      //exit("Script concluso");
	  
	  
	    //    echo "<br>md5_accesskey: ".          $md5_accesskey;          // Campo Obbligatorio a questo punto del codice già lo troviamo impostato
        //    echo "<br>cod_fiscale: ".            $cod_fiscale;            // Campo Obbligatorio a questo punto del codice già lo troviamo impostato
        //    
        //    
	    //    echo "<br>stato_richiesta: ".        $stato_richiesta;         //Se presente in A2A_durc viene utilizzato in ordine per fare un controllo della data $arr_field_cf['expire_date'];  in caso fare l'update,  se non presente indicare PENDING
	    //    echo "<br>data_richiesta: ".         $data_richiesta;          //Now solo Giorno in caso non sia presente un altro record con la giornata odierna    date("Y-m-d 00:00:00");
        //    echo "<br>data_evasione: ".          $data_evasione;           //Campo Da popolare subito in caso sia presente su A2A_durc con esito REGOLARE
	    //    
	    //    echo "<br>email_richiedente: ".      $email_richiedente;       //Se non indicato dall'utente prendere `api_keys`  -> `default_req_email`  - Se indicata dall'utente fare il controllo sintattico
        //    echo "<br>key_link_dinamico: ".      $key_link_dinamico;       //Inserire solo l'MD5 poi in fase di controllo dello script downloader.php  verrà fatto il controllo di esistenza per questo MD5    $arr_field_cf['file_name'];     md5("generazione@icsi.com".date("Y-m-d H:i:s"));
	    //    
        //    
        //    echo "<br>purpose_id: ".             $purpose_id;              // Campo Obbligatorio a questo punto del codice già lo troviamo impostato
        //    echo "<br>pdfboolvalue: ".           $pdfboolvalue;            // Campo Obbligatorio a questo punto del codice già lo troviamo impostato
	    //    
	    //    echo "<br>dettaglio_chiamante: ".    $dettaglio_chiamante;     // Fare l'update nel caso sia presente il record ma il campo sia vuoto oppure Se indicato dall'utente immettere nella insert generica
	    //    
	    //    echo "<br>data_di_scadenza: ".       $data_di_scadenza;        //$arr_field_cf['expire_date']; 
	    //    echo "<br>esito_lavorazione: ".      $esito_lavorazione;       //$arr_field_cf['esito_durc'];
        //    
        //    echo "<br>get_accesskey_credit: ".   $get_accesskey_credit;   //Campo già presente con credito illimitato o maggiore di 0  
	    //    echo "<br>conteggia: ".	             $conteggia;	           //Campo già presente con valore 1 per scalare il credito oppure con valore 0 per non scalare il credito (quando è 0 Implica che  `api_keys`->`crediti` si trova già impostato a -1 )
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  //composizione chiave logica
	      
	  // echo "<br>md5_accesskey: ". $md5_accesskey;
	  // echo "<br>cod_fiscale: ". $cod_fiscale;
	  // echo "<br>data_richiesta: ".$data_richiesta;
	  // echo "<br>email_richiedente: ".$email_richiedente;
	  // echo "<br>purpose_id: ".$purpose_id;
	  // echo "<br>pdfboolvalue: ".$pdfboolvalue;
	  // echo "<br>dettaglio_chiamante: ".$dettaglio_chiamante;
	  // echo "<br><br>";
	  
	  
	  
	 

//INIZIO LAVORAZIONE Logica Globale
				  if ($arr_field_cf == 0) { 
				      //echo "Ancora non lavorato su a2a_durc  faccio l'inserimento in pending solo se non è presente nella tabella attuale customers_requests ";  
					   //echo "NOT in a2a_durc <br>";
					   /* echo "id customers_requests: ". */ $id_esistenza_record = esiste_il_record_composto($md5_accesskey, $cod_fiscale, $data_richiesta, $email_richiedente, $purpose_id, $pdfboolvalue, $dettaglio_chiamante);

                                $esito_durc_ricontrollato = "PENDING";					   
					   			if ($id_esistenza_record == 0) { 
								   //Non esiste nella tabella customers_requests  quindi faccio l'inserimento
									
									//set_insert ($accesskey, $codice_fiscale, $stato_richiesta, $data_richiesta, $data_evasione, $email_richiedente, $key_download_link_dinamico, $purpose_id, $pdf_needed, $dettaglio_chiamante);
                                    $fk_id_fonte = set_insert_without_durc($md5_accesskey, $cod_fiscale, $esito_durc_ricontrollato, $data_richiesta, $email_richiedente, '' /*$key_link_dinamico*/, $purpose_id, $pdfboolvalue, $dettaglio_chiamante);
								    //echo "<br>FIASCOJOB";
									set_insert_tab_pending ($cod_fiscale, $data_richiesta, $email_richiedente, $fk_id_fonte);
								}

					   
					   
				  }
				  else { 
				       //echo "E' già stato lavorato su a2a_durc precedentemente faccio l'inserimento solo se non è presente nella tabella attuale customers_requests "; 
					   //echo "YES in a2a_durc <br>";
					   /* echo "id customers_requests: ". */ $id_esistenza_record = esiste_il_record_composto($md5_accesskey, $cod_fiscale, $data_richiesta, $email_richiedente, $purpose_id, $pdfboolvalue, $dettaglio_chiamante);
					   
					   
					   
					   
					//          echo "<br>";
					//          echo "file_name: ".$arr_field_cf['file_name'];
					//          echo "<br>";
					//          echo "esito_durc: ".$arr_field_cf['esito_durc'];
					//          echo "<br>";
					//          echo "data_scadenza: ".$arr_field_cf['expire_date'];
					   
					   
					   
					   
					   
					   //con le seguenti due righe prendo lo UNIX time per i confronti tra la data odierna e la data letta a suo tempo dal DURC
					    $ts_actual_time = strtotime($data_di_oggi);
					    $ts_data_durc = strtotime($arr_field_cf['expire_date'].' 23:59:59');

						
						
						
						
						
						
                        //Questo confronto finira sul campo stato_richiesta   della tabella   customers_requests
						if ($ts_actual_time <= $ts_data_durc) { /* echo "<br>Tutto Ok la data risulta regolare"; */ $esito_durc_ricontrollato = $arr_field_cf['esito_durc'];  }
						else { /* echo "<br>ATTENZIONE: la data NON risulta regolare"; */ $esito_durc_ricontrollato = "PENDING"; }						
						
						
						
					   //if ($arr_field_cf['esito_durc'] == "RISULTA REGOLARE"){
						if ($esito_durc_ricontrollato != "PENDING"){
     						if ($id_esistenza_record == 0) { 
								   //Non esiste nella tabella customers_requests  quindi faccio l'inserimento
									
									$fk_id_fonte = set_insert ($md5_accesskey, $cod_fiscale, $esito_durc_ricontrollato, $data_richiesta, $data_richiesta, $email_richiedente, $key_link_dinamico, $purpose_id, $pdfboolvalue, $dettaglio_chiamante);
     
								 
								}
							   else {
								   //Esiste nella tabella customers_requests  quindi faccio solo UPDATE
									//echo "<br> Finisco Qui";
									//update_stato_richiesta_request ($id_esistenza_record, $esito_durc_ricontrollato);
									//echo "Non Faccio Niente nel minuto richiesta Già Fatta";
								}
					   } // fine if di Controllo solo nell'etichetta ci sia scritto RISULTA REGOLARE ma non vuol dire che effettivamente la data sia regolare in data odierna; il controllo data viene fatto all'interno degli altri if
					   else {
						   
						     // Fare l'insert o update con esito KO
							    $esito_durc_ricontrollato = "PENDING";
							    if ($id_esistenza_record == 0) { 
								   //Non esiste nella tabella customers_requests  quindi faccio l'inserimento
									
									$fk_id_fonte = set_insert ($md5_accesskey, $cod_fiscale, $esito_durc_ricontrollato, $data_richiesta, $data_richiesta, $email_richiedente, '' /* $key_link_dinamico */, $purpose_id, $pdfboolvalue, $dettaglio_chiamante);
								    set_insert_tab_pending ($cod_fiscale, $data_richiesta, $email_richiedente, $fk_id_fonte);
								}
							   else {
								   //Esiste nella tabella customers_requests  quindi faccio solo UPDATE
									//update_stato_richiesta_request ($id_esistenza_record, $esito_durc_ricontrollato);
									//echo "Non Faccio Niente nel minuto richiesta Già Fatta";
								}
							 
						   
					   }
					   

				  } // Chiusura Else principale quindi ho la risposta DURC ($arr_field_cf != 0) è quindi un array

				  
				  
				  
				  
				  
				  
				    // Ho Concluso tutte le insert procedo con le select per poi passarle in Json  ATTENZIONE la LOGICA DELLE SELECT è Disgiunta dalla logica delle insert quindi rifaccio i controlli PENDING di "stato_richiesta"
					
					
								if ($id_esistenza_record == 0) { $id_select_json = $fk_id_fonte; } else { $id_select_json = $id_esistenza_record; }
				     
					             //echo $id_select_json;
					            
								/*
								$cf_letto = '';
								
					            
								while($cf_letto == '') {
                                    $arrvaltojson = sel_fields_to_transform_in_json ($id_select_json);
									$cf_letto = $arrvaltojson["codice_fiscale"];
								} 
								*/
								
					            $arrvaltojson = sel_fields_to_transform_in_json ($id_select_json);
								
								     //$arrvaltojson["id"];                                
                                     //$arrvaltojson["accesskey"];
									 //$arrvaltojson["data_richiesta"];                     
                                     //$arrvaltojson["data_evasione"];                      
                                     //$arrvaltojson["email_richiedente"];
                                     //$arrvaltojson["purpose_id"];        
                                     //$arrvaltojson["dettaglio_chiamante"];
					 
					                //die();
									//var_dump ($arrvaltojson);
									
									// $arrvaltojson["codice_fiscale"];
                                    // $arrvaltojson["stato_richiesta"];                    
                                    // $arrvaltojson["key_download_link_dinamico"];
									// $arrvaltojson["pdf_needed"];
					 
					 
					            //die();    
				  
				  
				                    if ($arrvaltojson["stato_richiesta"] != "PENDING") {
										$myObj->codice_fiscale = $arrvaltojson["codice_fiscale"];
										$myObj->esito_durc = $arrvaltojson["stato_richiesta"];  
										$myObj->data_scadenza = $arr_field_cf['expire_date'];
										if ($arrvaltojson["pdf_needed"] == 1){
										  $myObj->pdf_file = "http://durc.vincix.com/downloader.php?streamfile=".$arrvaltojson["key_download_link_dinamico"];
										}
										
										$myJSON = json_encode($myObj); 
										echo $myJSON;  
										exit();
										
									}else {
										$myObj->codice_fiscale = $arrvaltojson["codice_fiscale"];
										$myObj->esito_durc = "PENDING";  
										$myJSON = json_encode($myObj); 
										echo $myJSON;  
										exit();
										
									}
				  
				  
				  
				  
				  //FINE LAVORAZIONE Logica Globale









/*

	  //conclusione risposta Json
	  $myJSON = json_encode($myObj); 
	  echo $myJSON;  exit();

*/





 	













//if(isset($_REQUEST['cod_fisc'])) {  if(is_numeric($_REQUEST['cod_fisc'])) { $idpr = $_REQUEST['cod_fisc'];  }  else{ exit('<meta http-equiv="refresh" content="3; URL=/index.php" />');}   } else { exit('<meta http-equiv="refresh" content="3; URL=/index.php" />');} 
//if(isset($_REQUEST['email_requester'])) {  if(is_numeric($_REQUEST['email_requester'])) { $idpr = $_REQUEST['cod_fisc'];  }  else{ exit('<meta http-equiv="refresh" content="3; URL=/index.php" />');}   } else { exit('<meta http-equiv="refresh" content="3; URL=/index.php" />');} 

//echo md5("generazione@icsi.com".date("Y-m-d H:i:s"));



/*
accesskey 
codice_fiscale
email_richiedente     
purpose_id
pdf_needed
-- data_richiesta    date("Y-m-d 00:00:00");
*/





/*
accesskey 
codice_fiscale
//stato_richiesta
data_richiesta    date("Y-m-d 00:00:00");
//data_evasione
email_richiedente     
//key_download_link_dinamico
purpose_id
pdf_needed
*/












//INSERT INTO `customers_requests` (`id`, `accesskey`, `codice_fiscale`, `data_richiesta`, `data_evasione`, `email_richiedente`, `key_download_link_dinamico`, `purpose_id`, `pdf_needed`) VALUES (NULL, 'c84b4ed65d9f0adee820371f81f834e9', 'RTTRRT72P13B111N', '2018-08-21 00:00:00', NULL, 'ugo.fiasconaro@medialogicai.it', '', 'def_1', '1');

















//FUNZIONI SISTEMA


function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}


function esiste_il_record_composto($md5_accesskey, $cod_fiscale, $data_richiesta, $email_richiedente, $purpose_id, $pdfboolvalue, $dettaglio_chiamante)
	{
				
    	global $servername; global $username; global $password; global $dbname;
    	// Create connection
    	$conn = new mysqli($servername, $username, $password, $dbname);		if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error); } 
    	
    	// Impose Query
    	     //SELECT * FROM `customers_requests` WHERE `accesskey` LIKE 'c84b4ed65d9f0adee820371f81f834e9' AND `codice_fiscale` LIKE 'RTTRRT72P13B111N' AND `data_richiesta` = '2018-08-22 00:00:00' AND `email_richiedente` LIKE 'ugo.fiasconaro@medialogicai.it' AND `purpose_id` LIKE 'def_1' AND `pdf_needed` = 1 AND `dettaglio_chiamante` LIKE 'id_buyer'
		$sql = "SELECT id FROM `customers_requests` WHERE `accesskey` LIKE '$md5_accesskey' AND `codice_fiscale` LIKE '$cod_fiscale' AND `data_richiesta` = '$data_richiesta' AND `email_richiedente` LIKE '$email_richiedente' AND `purpose_id` LIKE '$purpose_id' AND `pdf_needed` = '$pdfboolvalue' AND `dettaglio_chiamante` LIKE '$dettaglio_chiamante'";
    	$result = $conn->query($sql);
    
    	//  Response/return Management
    			if ($result->num_rows > 0) { 
				      
					  	while($row = $result->fetch_assoc()) {
									$daritorno = $row["id"];
								 }  //Close While
						return $daritorno;
				} else { return 0; }
    			
    	//	Close Connection 	
    			$conn->close();	
    
	} // Chiusura Funzione accesskey_exist_for_one_customer


function sel_fields_to_transform_in_json ($id_select_json) 
    {
		global $servername; global $username; global $password; global $dbname;
    	// Create connection
    	$conn = new mysqli($servername, $username, $password, $dbname);		if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error); } 
    	
    	// Impose Query
    	     //SELECT * FROM `customers_requests` WHERE `accesskey` LIKE 'c84b4ed65d9f0adee820371f81f834e9' AND `codice_fiscale` LIKE 'RTTRRT72P13B111N' AND `data_richiesta` = '2018-08-22 00:00:00' AND `email_richiedente` LIKE 'ugo.fiasconaro@medialogicai.it' AND `purpose_id` LIKE 'def_1' AND `pdf_needed` = 1 AND `dettaglio_chiamante` LIKE 'id_buyer'
		$sql = "SELECT * FROM `customers_requests` WHERE `id` = '$id_select_json'";
    	$result = $conn->query($sql);
    
    	//  Response/return Management
    			if ($result->num_rows > 0) { 
				      
					  	while($row = $result->fetch_assoc()) {
																
									 $daritorno["id"] = $row["id"];
                                     $daritorno["accesskey"] = $row["accesskey"];
                                     $daritorno["codice_fiscale"] = $row["codice_fiscale"];
                                     $daritorno["stato_richiesta"] = $row["stato_richiesta"];
                                     $daritorno["data_richiesta"] = $row["data_richiesta"];
                                     $daritorno["data_evasione"] = $row["data_evasione"];
                                     $daritorno["email_richiedente"] = $row["email_richiedente"];
                                     $daritorno["key_download_link_dinamico"] = $row["key_download_link_dinamico"];
                                     $daritorno["purpose_id"] = $row["purpose_id"];
                                     $daritorno["pdf_needed"] = $row["pdf_needed"];
                                     $daritorno["dettaglio_chiamante"] = $row["dettaglio_chiamante"];
																		
								 }  //Close While
						return $daritorno;
				} else { return 0; }
    			
    	//	Close Connection 	
    			$conn->close();	
	   
    }
	
	

	
function set_insert ($accesskey, $codice_fiscale, $stato_richiesta, $data_richiesta, $data_evasione, $email_richiedente, $key_download_link_dinamico, $purpose_id, $pdf_needed, $dettaglio_chiamante) {	
		global $servername;  global $username; global $password; global $dbname;
		
		  $conn = new mysqli($servername, $username, $password, $dbname);    if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error);  } 
          $sql = "INSERT INTO `customers_requests` (`id`, `accesskey`, `codice_fiscale`, `stato_richiesta`, `data_richiesta`, `data_evasione`, `email_richiedente`, `key_download_link_dinamico`, `purpose_id`, `pdf_needed`, `dettaglio_chiamante`) VALUES (NULL, '$accesskey', '$codice_fiscale', '$stato_richiesta', '$data_richiesta', '$data_evasione', '$email_richiedente', '$key_download_link_dinamico', '$purpose_id', '$pdf_needed', '$dettaglio_chiamante');";
		  
		  $result = $conn->query($sql);
		
			   //return 1;	
			return $conn->insert_id;
			
		    $conn->close();	
	}  //set_insert	


function set_insert_without_durc ($accesskey, $codice_fiscale, $stato_richiesta, $data_richiesta, $email_richiedente, $key_download_link_dinamico, $purpose_id, $pdf_needed, $dettaglio_chiamante) {	
		global $servername;  global $username; global $password; global $dbname;
		
		  $conn = new mysqli($servername, $username, $password, $dbname);    if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error);  } 
          $sql = "INSERT INTO `customers_requests` (`id`, `accesskey`, `codice_fiscale`, `stato_richiesta`, `data_richiesta`, `data_evasione`, `email_richiedente`, `key_download_link_dinamico`, `purpose_id`, `pdf_needed`, `dettaglio_chiamante`) VALUES (NULL, '$accesskey', '$codice_fiscale', '$stato_richiesta', '$data_richiesta', NULL, '$email_richiedente', '$key_download_link_dinamico', '$purpose_id', '$pdf_needed', '$dettaglio_chiamante');";
		  
		  $result = $conn->query($sql);
		
			   //return 1;	
			   return $conn->insert_id;
			   
		    $conn->close();	
	}  //set_insert		
	
	
	
function update_stato_richiesta_request ($id_esistenza_record, $esito_durc_ricontrollato) {	
		global $servername;  global $username; global $password; global $dbname;

		  $conn = new mysqli($servername, $username, $password, $dbname);    if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error);  } 
          $sql = "UPDATE `customers_requests` SET `stato_richiesta` = '$esito_durc_ricontrollato' WHERE `customers_requests`.`id` = $id_esistenza_record";
		  
		  $result = $conn->query($sql);
		
			   return 1;	
			
		    $conn->close();	
	}  //selectarrfromonetable		
	
	


	
function set_insert_tab_pending ($codice_fiscale, $data_richiesta, $email_richiedente, $fk_id_fonte) {	
		global $servername;  global $username; global $password; global $dbname;
		
		  $conn = new mysqli($servername, $username, $password, $dbname);    if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error);  } 
          $sql = "INSERT INTO `richieste_durc` (`codice_fiscale`, `data_richiesta`, `data_evasione`, `richiedente`, `fonte`, `fk_id_fonte`) VALUES ('$codice_fiscale', '$data_richiesta', NULL, '$email_richiedente', 'WS', '$fk_id_fonte');";
		  
		  $result = $conn->query($sql);
		
			   return 1;	
			
		    $conn->close();	
	}  //set_insert			
	
	

	
	
	
	
	
	
	



// --------------    FUNZIONI RICHIAMANTI ACCESS KEY   --------------- //

class accesskey_control { 


			function isValidMd5format($md5 ='')
			{
				return preg_match('/^[a-f0-9]{32}$/', $md5);
			}




			function accesskey_exist_for_one_customer($md5_accesskey)
			{
				
					global $servername; global $username; global $password; global $dbname;

					// Create connection
					$conn = new mysqli($servername, $username, $password, $dbname);		if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error); } 

					// Impose Query
					$sql = "SELECT * FROM `api_keys` WHERE `accesskey` LIKE '$md5_accesskey'";
					$result = $conn->query($sql);

					//  Response/return Management
							if ($result->num_rows > 0) { return 1; } else { return 0; }
							
					//	Close Connection 	
							$conn->close();	
				
			} // Chiusura Funzione accesskey_exist_for_one_customer




			function get_accesskey_credit($md5_accesskey)
			{
				
					global $servername; global $username; global $password; global $dbname;

					// Create connection
					$conn = new mysqli($servername, $username, $password, $dbname);		if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error); } 

					// Impose Query
					$sql = "SELECT * FROM `api_keys` WHERE `accesskey` LIKE '$md5_accesskey'";
					$result = $conn->query($sql);

					//  Response/return Management
							if ($result->num_rows > 0) { 
								 while($row = $result->fetch_assoc()) {
									$daritorno = $row["crediti"];
								 }  //Close While
							   return $daritorno;		
							} else { return 0; }
							
					//	Close Connection 	
							$conn->close();	
				
			}  // Chiusura Funzione  get_accesskey_credit

}  // Chiusura classe accesskey_control











// --------------    FUNZIONI RICHIAMANTI CODICE FISCALE   --------------- //


class codice_fiscale_control {
			function get_cf_field_from_a2adurc($cod_fiscale)
			{
				
					global $servername; global $username; global $password; global $dbname;

					// Create connection
					$conn = new mysqli($servername, $username, $password, $dbname);		if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error); } 

					// Impose Query
					$sql = "SELECT * FROM `a2a_durc` where codice_fiscale like '$cod_fiscale' and esito_durc is not null";
					$result = $conn->query($sql);

					//  Response/return Management
							if ($result->num_rows > 0) { 
								 while($row = $result->fetch_assoc()) {
									$daritorno['expire_date'] = $row['expire_date'];
									$daritorno['esito_durc'] = $row['esito_durc'];
									$daritorno['file_name'] = $row['file_name'];
								 }  //Close While
							   return $daritorno;		
							} else { return 0; }
							
					//	Close Connection 	
							$conn->close();	
				
			}  // Chiusura Funzione  get_accesskey_credit
	
} // Chiusura classe codice_fiscale_control



class purpose_id_control {
			function get_customer_id_from_usertable($md5_accesskey)
			{
				
					global $servername; global $username; global $password; global $dbname;
					// Create connection
					$conn = new mysqli($servername, $username, $password, $dbname);		if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error); } 

					
					
					// Impose Query
					$sql = "SELECT id FROM `utenti_durc` where email in (SELECT account_owner FROM `api_keys` where accesskey like '$md5_accesskey')";
					$result = $conn->query($sql);

					//  Response/return Management
							if ($result->num_rows > 0) { 
								 while($row = $result->fetch_assoc()) {
									$daritorno = $row['id'];
								 }  //Close While
							   return $daritorno;		
							} else { return 0; }
							
					//	Close Connection 	
							$conn->close();	
				
			}  // Chiusura Funzione  check_validity_purpose_id
	
	
	
	
		    function check_if_exist_this_default_purid($pur_id)
			{
				
					global $servername; global $username; global $password; global $dbname;
					// Create connection
					$conn = new mysqli($servername, $username, $password, $dbname);		if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error); } 

					// Impose Query
					$sql = "SELECT * FROM `pourpose_list` WHERE `custom_text_id` LIKE '$pur_id'";
					$result = $conn->query($sql);

					//  Response/return Management
							if ($result->num_rows > 0) { return 1; } else { return 0; }
							
					//	Close Connection 	
							$conn->close();	
				
			} // Chiusura Funzione accesskey_exist_for_one_customer
	
	
	
	
	
	
	
	
} // Chiusura classe purpose_id_control







class email_richiedente_control {
			function get_default_email_richiedente_from_a2adurc($md5_accesskey)
			{
				
					global $servername; global $username; global $password; global $dbname;
					// Create connection
					$conn = new mysqli($servername, $username, $password, $dbname);		if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error); } 

					// Impose Query
					$sql = "SELECT * FROM `api_keys` WHERE `accesskey` LIKE '$md5_accesskey'";
					$result = $conn->query($sql);

					//  Response/return Management
							if ($result->num_rows > 0) { 
								 while($row = $result->fetch_assoc()) {
									$daritorno = $row['default_req_email'];
								 }  //Close While
							   return $daritorno;		
							} else { return 0; }
							
					//	Close Connection 	
							$conn->close();	
				
			}  // Chiusura Funzione  get_accesskey_credit
	
} // Chiusura classe codice_fiscale_control










?>
