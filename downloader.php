<?php

require_once('dbconf.php');

$dir    = 'http://durc.vincix.com/pdf_stream/';
//$files1 = scandir($dir);
//$files2 = scandir($dir, 1);

//print_r($files1);
//print_r($files2);




//streamfile=104e9967a02fd1cfe37ee49cd66fae3f

//INIZIO Controlli  se è settato dettaglio_chiamante
	if(isset($_REQUEST['streamfile'])) {
		      
			  
			  if (isValidMd5format($_REQUEST['streamfile']) != 0){ $md5streamfile = $_REQUEST['streamfile']; }
			  else { $myObj->response = "downloadkey field is not valid";  $myJSON = json_encode($myObj); echo $myJSON;  exit(); }
		
		
	} else { 
         // Quando non è settato il presente campo prendo i valori di default         
         $myObj->response = "downloadkey field is Required to make the request";  $myJSON = json_encode($myObj); echo $myJSON;  exit();
	} //Chiusura del campo quando non è settato dettaglio_chiamante
//FINE Controlli  se è settato dettaglio_chiamante	



$namefile = check_records_turn_back_file_name($md5streamfile);

if ($namefile == 0) { $myObj->response = "downloadkey is not valid";  $myJSON = json_encode($myObj); echo $myJSON;  exit(); }




//$namefile = '01421790427-INPS_11369847.pdf';

$file = $dir.$namefile;










//$file = 'http://example.com/somefile.mp3';
download($file,2000);

/*
Set Headers
Get total size of file
Then loop through the total size incrementing a chunck size
*/
function download($file,$chunks){
    set_time_limit(0);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-disposition: attachment; filename='.basename($file));
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');
    header('Pragma: public');
    $size = get_size($file);
    header('Content-Length: '.$size);

    $i = 0;
    while($i<=$size){
        //Output the chunk
        get_chunk($file,(($i==0)?$i:$i+1),((($i+$chunks)>$size)?$size:$i+$chunks));
        $i = ($i+$chunks);
    }

}

//Callback function for CURLOPT_WRITEFUNCTION, This is what prints the chunk
function chunk($ch, $str) {
    print($str);
    return strlen($str);
}

//Function to get a range of bytes from the remote file
function get_chunk($file,$start,$end){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $file);
    curl_setopt($ch, CURLOPT_RANGE, $start.'-'.$end);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    curl_setopt($ch, CURLOPT_WRITEFUNCTION, 'chunk');
    $result = curl_exec($ch);
    curl_close($ch);
}

//Get total size of file
function get_size($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    return intval($size);
}




function isValidMd5format($md5 ='')
{
	return preg_match('/^[a-f0-9]{32}$/', $md5);
}





function check_records_turn_back_file_name ($md5streamfile)
	{
				
    	global $servername; global $username; global $password; global $dbname;
    	// Create connection
    	$conn = new mysqli($servername, $username, $password, $dbname);		if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error); } 
    	
    	// Impose Query
		$sql = "SELECT file_name FROM `a2a_durc` where codice_fiscale in (SELECT codice_fiscale as cf FROM `customers_requests` where key_download_link_dinamico like '$md5streamfile')";
    	$result = $conn->query($sql);
    
    	//  Response/return Management
    			if ($result->num_rows > 0) { 
				      
					  	while($row = $result->fetch_assoc()) {
									$daritorno = $row["file_name"];
								 }  //Close While
						return $daritorno;
				} else { return 0; }
    			
    	//	Close Connection 	
    			$conn->close();	
    
	} // Chiusura Funzione accesskey_exist_for_one_customer








//if (file_exists($file)) {


//	echo "Il file esiste";
    //header('Content-Description: File Transfer');
    //header('Content-Type: application/octet-stream');
    //header('Content-Disposition: attachment; filename="'.basename($file).'"');
    //header('Expires: 0');
    //header('Cache-Control: must-revalidate');
    //header('Pragma: public');
    //header('Content-Length: ' . filesize($file));
    //echo filesize($file);
	//readfile($file);
    //exit;






?>