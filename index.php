<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

session_start();

$userinfo = array(
                'Martinella'=>'fiascojob4ever',
                'fiascojob'=>'fiascojob4ever'
                );



//print_r($userinfo);

//die();

if(isset($_GET['logout'])) {
    $_SESSION['username'] = '';
    header('Location:  ' . $_SERVER['PHP_SELF']);
}


if(isset($_POST['username'])) {
   if (array_key_exists ( $_POST['username'] , $userinfo )) {
    
	
	if($userinfo[$_POST['username']] == $_POST['password']) {
        $_SESSION['username'] = $_POST['email'];
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





<!DOCTYPE html>
<html lang="en">
<head>

	<meta charset="UTF-8" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta http-equiv="cache-control" content="max-age=0" />
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
	<meta http-equiv="pragma" content="no-cache" />

	<title>Ataraxia - Login</title>

	<link href="./vendor/dist/jquery-ui-1.12.1/jquery-ui.css?v=1.0" rel="stylesheet" />
	<link href="./vendor/dist/jquerysctipttop.css?v=1.0" rel="stylesheet">
	<link href="./vendor/bs337/css/bootstrap.min.css?v=1.0" rel="stylesheet">
	<link href="./vendor/dist/jquery.simplewizard.css?v=1.0" rel="stylesheet" />

	<style>
		img.ui-datepicker-trigger{
			width:35px;
		}
		
		ui-datepicker-div {
			z-index: 3 !important;
			position: relative !important; 
		}
	</style>

	<script src="./vendor/dist/jquery-1.12.4.js?ver=1.0"></script>
	<script src="./vendor/dist/jquery-ui-1.12.1/jquery-ui.js?ver=1.0"></script>
	<script src="./vendor/bs337/js/bootstrap.min.js?ver=1.0"></script>
	<script src="./vendor/dist/jquery.validate.min.js?ver=1.0"></script>
	<script src="./vendor/dist/jquery.simplewizard.js?ver=1.0"></script>

	<script type="text/javascript">
		function validateEmail(id) {
			var email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i;

			if(!email_regex.test($("#"+id).val())) {
				var div = $("#"+id).closest("div");
				div.removeClass("has-success");
				$("#glypcn"+id).remove();
				div.addClass("has-error has-feedback");
				div.append('<span id="glypcn'+id+'" class="glyphicon glyphicon-remove form-control-feedback"></span>');
				div.append('<p id="email-non-corretta">E-mail non corretta</p>');
				return false;
			}
			else {
			    var div = $("#"+id).closest("div");
			    div.removeClass("has-error");
				$("#glypcn"+id).remove();
				div.addClass("has-success has-feedback");
				div.append('<span id="glypcn'+id+'" class="glyphicon glyphicon-ok form-control-feedback"></span>');
				$('#email-non-corretta').remove();
			    return true;
			}
		}

		$(document).ready(function() {
			$("#submit-button").click(function() {
				if(!validateEmail("ins_email")) { return false; }

				$("form#snaiform").submit();
			});
		});
	</script>

</head>

<body>




        <?php 
		if (isset($_SESSION['username']))
		if($_SESSION['username']): 
	
	
	        /* <p>You are logged in as <?=$_SESSION['username']?></p>
            <p><a href="?logout=1">Logout</a></p> */
		
		?>
          


         <meta http-equiv="refresh" content="0; URL='/dash.php'" />

		 






	
	        <?php endif; ?>
	
<?php	if(!array_key_exists('username', $_SESSION) || !$_SESSION['username']) { ?> 
		
        <?php /* non è loggato quindi faccio spuntare il form */ ?>   
		
		<div class="container">
		<div class="row">
			<div class="col-md-12" style="margin-top: 15%">
				<span>&nbsp;</span>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 col-md-offset-4">
				<div class="panel panel-default">
					<div class="panel-heading text-center">
						<img src="/image/Europcar-Logo.jpg" width="100%"/>
					</div>
					<div class="panel-body">
						<form accept-charset="UTF-8" role="form" method="POST" name="login" action="login.php">
							<fieldset>
							  	<div class="form-group">
									<input class="form-control" placeholder="Email Owner" name="username" type="text" value="">
									<div style="color:red"></div>								</div>
								<div class="form-group">
									<input class="form-control" placeholder="Password" name="password" type="password" value="">
									<div style="color:red"></div>								</div>
								<input class="btn btn-lg btn-success btn-block" type="submit" value="Login">
								<!-- <a class="btn btn-lg btn-success btn-block" type="button" value="Registrati" href="nuova_registrazione.php"> Nuova Registrazione </a> -->
							</fieldset>
						</form>
						<br>
						<p>Per abilitazioni scrivere a <br> <img src="/image/email-antispam.png" width="100%" /> </p>
					</div>
				</div>
			</div>
		</div>
		
	</div>

		
		

 <?php 
// Chiudo l'if se non è in sessione
 } 
?>


	
	
	
</body>
</html>