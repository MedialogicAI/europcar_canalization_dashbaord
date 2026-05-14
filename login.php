<?php


require_once('dbconf.php');
require_once('funzioni-dash.php');
require_once('login-lista-user-db.php');
require_once('login-err-non-valido.php');


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);





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

	<title>A2A - Login</title>

	<link href="/vendor/dist/jquery-ui-1.12.1/jquery-ui.css?v=1.0" rel="stylesheet" />
	<link href="/vendor/dist/jquerysctipttop.css?v=1.0" rel="stylesheet">
	<link href="/vendor/bs337/css/bootstrap.min.css?v=1.0" rel="stylesheet">
	<link href="/vendor/dist/jquery.simplewizard.css?v=1.0" rel="stylesheet" />

	<style>
		img.ui-datepicker-trigger{
			width:35px;
		}
		
		ui-datepicker-div {
			z-index: 3 !important;
			position: relative !important; 
		}
	</style>

	<script src="/vendor/dist/jquery-1.12.4.js?ver=1.0"></script>
	<script src="/vendor/dist/jquery-ui-1.12.1/jquery-ui.js?ver=1.0"></script>
	<script src="/vendor/bs337/js/bootstrap.min.js?ver=1.0"></script>
	<script src="/vendor/dist/jquery.validate.min.js?ver=1.0"></script>
	<script src="/vendor/dist/jquery.simplewizard.js?ver=1.0"></script>

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

<?php if (isset($_SESSION['username'])) if($_SESSION['username']):   /* Sono in Sessione quindi l'utente è loggato nel DB rilascio l'accesso alle pagine in base all'ordine di permissività indicato nella tabella utenti_web */  ?>
	
	     
		 
		 
           <!-- Utente correttamente loggato lo ridireziono nella dashboard -->
		   <meta http-equiv="refresh" content="0; URL='/dash.php'" />		
	
 	
	       <!-- 
	        <p>You are logged in as <?=$_SESSION['username']?></p>
            <p><a href="?logout=1">Logout</a></p>
		    -->
		
          




		 






	
<?php endif; /* Chiusura dell'if se sono in sessione */  ?>
<?php	if(!$_SESSION['username']) { ?>		
     
     <meta http-equiv="refresh" content="3; URL='/index.php'" />   
 <?php } ?>





	
	
	
</body>
</html>