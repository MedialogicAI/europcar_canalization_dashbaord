<?php
if (0) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

require_once('dbconf.php');
require_once('funzioni-dash.php');
require_once('login-lista-user-db.php');
require_once('login-err-non-valido.php');
require_once('global_var.php');

require_once('header-pag.php');   ?>
<!-- qui la navbar sidebar -->
<?php  require_once('sidebar-pag.php');   ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        setInterval(function(){
            if ($('#ifr_page_central').attr('src') == '/dash/user_account.php'){
                reloadIFrame();
            }
        }, 30000);
	});

	function reloadIFrame() {
		document.getElementById("ifr_page_central").src="/dash/user_account.php";
	}
</script>

<?php if (isset($_SESSION['username'])) if($_SESSION['username']): /* Sono in Sessione quindi l'utente è loggato nel DB rilascio l'accesso alle pagine in base all'ordine di permissività indicato nella tabella utenti_web */  ?>
<!-- Utente correttamente loggato lo ridireziono nella dashboard    -->
<!-- <meta http-equiv="refresh" content="0; URL='/dash.php'" />     -->
<!--
    <p>You are logged in as <?=$_SESSION['username']?></p>
    <p><a href="?logout=1">Logout</a></p>
-->
            <div class="update">
		        <div id="ifr2update">
                    	<tbody style="margin:0px;padding:0px;overflow:hidden">
		        			<iframe src="/dash/user_account.php" id="ifr_page_central"  frameborder="0" style="overflow:hidden;height:2000px;width:100%" height="2000px" width="100%"></iframe>
		        		</tbody>
		        </div>
            </div>

<?php endif; /* Chiusura dell'if se sono in sessione */  ?>
<?php	if(!$_SESSION['username']) { ?>		
     <meta http-equiv="refresh" content="3; URL='/index.php'" />
<?php } ?>

<script>
    /*	window.setInterval("reloadIFrame();", 30000);
    	function reloadIFrame() {
		
		 document.frames["report"].location.reload();
		}
	*/
	</script>

	<?php if($_GET['comune']){ ?>
		<script>
			$('#ifr_page_central').attr('src','/dash/<?php echo $_GET['comune'] ?>');
		</script>
	<?php } ?>
		 
<?php  require_once('footer-pag.php');   ?>		 
