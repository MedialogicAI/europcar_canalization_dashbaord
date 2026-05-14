<?php

$arrlistautenti = listautentipassword();
session_start();
$utenti_abilitati = array();
//print_r($arrlistautenti);
//die();
foreach ($arrlistautenti as $value) {
		//print_r($value);
	$utenti_abilitati [$value["email"]] = $value["password"];
}
//print_r($utenti_abilitati);
$userinfo = $utenti_abilitati;

//$userinfo = array(
  //              'Martinella'=>'fiascojob4ever',
  //              'fiascojob'=>'fiascojob4ever'
  //              );



//print_r($userinfo);

//exit();




?>