<?php



if(isset($_GET['logout'])) {
    $_SESSION['username'] = '';
    header('Location:  ' . $_SERVER['PHP_SELF']);
}


if(isset($_POST['username'])) {
   if (array_key_exists ( $_POST['username'] , $userinfo )) {
    
	
	if($userinfo[$_POST['username']] == $_POST['password']) {
        $_SESSION['username'] = $_POST['username'];
    }else {
        //echo "Login non valido";
		?>
		<div class="alert alert-danger alert-dismissible">
		  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		  <strong>Errore!</strong> Login non valido.
		</div>
		<?php
		//Invalid Login
    }
	
   }  // Chiusura condizione se esiste INDICE array_key_exists
   else {
        //echo "Login non valido";
		
		?>
		<div class="alert alert-danger alert-dismissible">
		  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		  <strong>Errore!</strong> Login non valido.
		</div>
		<?php
		
		//Invalid Login
    }
   
   
}   //Chiusura isset username


?>