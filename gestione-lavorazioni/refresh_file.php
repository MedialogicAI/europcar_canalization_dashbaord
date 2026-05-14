<?php

require_once('../dbconf.php');

if($_POST['fileName']){
	$fileName = $_POST['fileName'];
	cambiastato ($fileName);
}

if($_POST['action']){
	checkstato();
}




?>

<?php

	function cambiastato ($fileName) {	
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

		$sql = "UPDATE `refresh_file` SET `$fileName` = 1";
		$result = $conn->query($sql);
		$conn->close();	
		$risposta = 'record aggiornati';
		
		die($risposta);
			
	}  
	
	
	function checkstato () {	
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

		$sql = "SELECT * FROM `refresh_file`";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		$button_state = json_encode($row);
		die($button_state);
		
		
			
	}  
		

		
		

?>