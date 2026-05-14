 <?php
if (0) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

require_once('../dbconf.php');
require_once('../funzioni-dash.php');
require_once('../login-lista-user-db.php');
require_once('../login-err-non-valido.php');
require_once('../global_var.php');


?>
 
 
 <?php  require_once('../header-pag.php');   ?>  
 
 
               <div id="page-wrapper" >

            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                     <h2>Pagina Documentazione Rest API</h2>   
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />
              
			  
			  
			  
			  
			  
			  <body lang=IT style='tab-interval:36.0pt'>

<div class=WordSection1>

<p class=MsoNormal>Documentazione e Manualistica per l’utilizzo delle
interrogazioni DURC in REST API </p>

<p class=MsoNormal>Il Portale si compone di due sezioni:</p>

<p class=MsoNormalCxSpMiddle style='margin-top:0cm;margin-right:0cm;margin-bottom:
0cm;margin-left:36.0pt;margin-bottom:.0001pt;mso-add-space:auto;text-indent:
-18.0pt;mso-list:l0 level1 lfo1;border:none;mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;
mso-border-shadow:yes'><![if !supportLists]><span style='mso-list:Ignore'>1)<span
style='font:7.0pt "Times New Roman"'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span><![endif]><span
style='color:black'>Web Application<span style='mso-spacerun:yes'>  </span></span></p>

<p class=MsoNormalCxSpMiddle style='margin-top:0cm;margin-right:0cm;margin-bottom:
0cm;margin-left:36.0pt;margin-bottom:.0001pt;mso-add-space:auto;text-indent:
-18.0pt;mso-list:l0 level1 lfo1;border:none;mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;
mso-border-shadow:yes'><![if !supportLists]><span style='mso-list:Ignore'>2)<span
style='font:7.0pt "Times New Roman"'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span><![endif]><span
style='color:black'>Web Service </span></p>

<p class=MsoNormal style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
margin-left:36.0pt;margin-bottom:.0001pt;text-indent:-36.0pt;border:none;
mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;mso-border-shadow:yes'><span
style='color:black'><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
margin-left:36.0pt;margin-bottom:.0001pt;text-indent:-36.0pt;border:none;
mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;mso-border-shadow:yes'><span
style='color:black'><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal style='margin-left:36.0pt;text-indent:-36.0pt;border:none;
mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;mso-border-shadow:yes'><span
style='color:black'><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal><b style='mso-bidi-font-weight:normal'>La Web Application</b>,
Genera le accesskey (Univoche, ad unico uso utente), registrazione nuove
utenze, impostazione email di default per ogni singola accesskey (che in caso
non indicato nella request verrà utilizzata per le notifiche di default al
momento del cambiamento di stato DURC da PENDING ad altro Stato).</p>

<p class=MsoNormal style='margin-left:36.0pt;text-indent:-36.0pt;border:none;
mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;mso-border-shadow:yes'><span
style='color:black'><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal><b style='mso-bidi-font-weight:normal'>Il Web Service</b>,
attualmente si compone di due modalità di request che si distinguono nella
richiesta su due pagine dinamiche le seguenti:</p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><a href="http://durc.vincix.com/request.php"><span
style='color:#0563C1'>http://durc.vincix.com/request.php</span></a></p>

<p class=MsoNormal><a href="http://durc.vincix.com/downloader.php"><span
style='color:#0563C1'>http://durc.vincix.com/downloader.php</span></a></p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal>La prima web service (<a
href="http://durc.vincix.com/request.php"><span style='color:#0563C1'>http://durc.vincix.com/request.php</span></a>)
si compone dei seguenti parametri richiamabili da Client sia in modatilà HTTP a
scelta sia GET che POST:</p>

<p class=MsoNormal><u>Parametri di INPUT Obbligatori<span
style='mso-spacerun:yes'>   </span>request.php:<o:p></o:p></u></p>

<p class=MsoNormal><b style='mso-bidi-font-weight:normal'>accesskey</b><span
style='mso-spacerun:yes'>   </span>=&gt;<span style='mso-spacerun:yes'>  
</span>codice accesso chiamante</p>

<p class=MsoNormal>Questo codice è associato univocamente all’utente iscritto
nel portale, quest’ultimo può avere più codici univoci per l’abilitazione a
fare le richieste, ad ogni codice è associato una email di default per le
notifiche che, se non indicata in fase di richiesta (request) verrà presa in
alternativa come email principale sul quale inviare notifiche al cambiamento di
stato (da Pending ad Altro stato) per la richiesta in corso.</p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><b style='mso-bidi-font-weight:normal'>purpose_id</b> =&gt;
etichetta funzionalità chiamante ( al momento valori di default es. def_1,
def_2, def_3 )</p>

<p class=MsoNormal>Questa etichetta definisce una breve motivazione della richiesta,
troviamo quindi la possibilità di indicare motivazioni di default che
attualmente si suddividono come segue</p>

<p class=MsoNormal>def_1<span style='mso-spacerun:yes'>   </span>=&gt;<span
style='mso-spacerun:yes'>    </span>Qualifica<span style='mso-spacerun:yes'>  
</span></p>

<p class=MsoNormal>def_2<span style='mso-spacerun:yes'>   </span>=&gt;<span
style='mso-spacerun:yes'>    </span>Gara </p>

<p class=MsoNormal>def_3<span style='mso-spacerun:yes'>   </span>=&gt;<span
style='mso-spacerun:yes'>     </span>Contrattualizzazione</p>

<p class=MsoNormal>L’utente ha la possibilità di definire delle proprie etichette
con delle motivazioni personalizzate che non vengono condivise con gli altri
utenti registrati al portale, a regime verranno definite le metodologie di
inserimento nuove etichette personalizzate (personalizzabili sia da portale che
da REST API).</p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><b style='mso-bidi-font-weight:normal'>codice_fiscale</b><span
style='mso-spacerun:yes'>  </span>=&gt; codice fiscale </p>

<p class=MsoNormal>Il codice fiscale del quale è richiesto il controllo DURC</p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><b style='mso-bidi-font-weight:normal'>pdf_needed</b><span
style='mso-spacerun:yes'>       </span>=&gt;<span style='mso-spacerun:yes'>    
</span>1<span style='mso-spacerun:yes'>  </span>OPPURE<span
style='mso-spacerun:yes'>   </span>0<span style='mso-spacerun:yes'>   </span></p>

<p class=MsoNormal>“Se impostato a valore 1 la risposta prevede l’indicazione
del link di download del file pdf in caso la lavorazione al momento della
chiamata sia diversa da PENDING;</p>

<p class=MsoNormal>Differentemente, se ha valore 0<span
style='mso-spacerun:yes'>  </span>verrà indicata la risposta senza link di
download.”</p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><b style='mso-bidi-font-weight:normal'>dettaglio_chiamante</b><span
style='mso-spacerun:yes'>   </span>=&gt;<span style='mso-spacerun:yes'>   
</span>id_univoco_chiamante<span style='mso-spacerun:yes'>     </span></p>

<p class=MsoNormal>“Campo che identifica univocamente la chiamata client
definito come codice o identificativo del buyer”</p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><u>Parametri di INPUT<span style='mso-spacerun:yes'>  
</span>Opzionali<span style='mso-spacerun:yes'>    </span>request.php:<o:p></o:p></u></p>

<p class=MsoNormal><b style='mso-bidi-font-weight:normal'>email_richiedente</b><span
style='mso-spacerun:yes'>   </span>=&gt;<span style='mso-spacerun:yes'>   
</span>email di chi sta effettuando la request</p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal>Esempio di richiesta HTTP GET completa per la request.php:</p>

<p class=MsoNormal><span style='font-size:10.0pt;line-height:107%'>http://durc.vincix.com/request.php?accesskey=c84000065d9f0adee000071f810000e9&amp;codice_fiscale=06530761219&amp;purpose_id=def_1&amp;pdf_needed=1&amp;dettaglio_chiamante=operatore_1&amp;email_richiedente=test@vincix.com<o:p></o:p></span></p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><u>Parametri di OUTPUT <o:p></o:p></u></p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal>Esempio risposta dopo la chiamata<span
style='mso-spacerun:yes'>   </span>request.php</p>

<p class=MsoNormal><span style='font-size:10.0pt;line-height:107%;color:black'><span
style='mso-spacerun:yes'>          </span>{<o:p></o:p></span></p>

<p class=MsoNormal><span style='font-size:10.0pt;line-height:107%;color:black'><span
style='mso-spacerun:yes'>                 
</span>&quot;response&quot;:&quot;Enabled&quot;,<o:p></o:p></span></p>

<p class=MsoNormal><span style='font-size:10.0pt;line-height:107%;color:black'><span
style='mso-spacerun:yes'>                 
</span>&quot;codice_fiscale&quot;:&quot;06530761219&quot;,<o:p></o:p></span></p>

<p class=MsoNormal><span style='font-size:10.0pt;line-height:107%;color:black'><span
style='mso-spacerun:yes'>                 
</span>&quot;esito_durc&quot;:&quot;RISULTA REGOLARE&quot;,<o:p></o:p></span></p>

<p class=MsoNormal><span style='font-size:10.0pt;line-height:107%;color:black'><span
style='mso-spacerun:yes'>                 
</span>&quot;data_scadenza&quot;:&quot;2018-09-19&quot;,<o:p></o:p></span></p>

<p class=MsoNormal><span style='font-size:10.0pt;line-height:107%;color:black'><span
style='mso-spacerun:yes'>                 
</span>&quot;pdf_file&quot;:&quot;http:\/\/durc.vincix.com\/downloader.php?streamfile=36b0d46137ee1cc5a4556a758fa8efea&quot;<o:p></o:p></span></p>

<p class=MsoNormal><span style='font-size:10.0pt;line-height:107%;color:black'><span
style='mso-spacerun:yes'>          </span>}<o:p></o:p></span></p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><a name="_gjdgxs"></a><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><b style='mso-bidi-font-weight:normal'>response<span
style='mso-spacerun:yes'>  </span></b>=&gt; <b style='mso-bidi-font-weight:
normal'><span style='mso-spacerun:yes'> </span></b>definisce il riscontro della
chiamata incorso </p>

<p class=MsoNormal>Il riscontro può essere del parametro di output response può
essere tra le seguenti opzioni:</p>

<p class=MsoNormal style='margin-left:35.4pt'><span style='font-size:10.0pt;
line-height:107%'>&quot;Access Key Not Valid&quot;<span
style='mso-spacerun:yes'>    </span>=&gt;<span style='mso-spacerun:yes'>    
</span>La chiave di accesso non risulta valida per la richiesta<o:p></o:p></span></p>

<p class=MsoNormal style='margin-left:35.4pt'><span style='font-size:10.0pt;
line-height:107%'>&quot;No Credit to spent&quot;<span
style='mso-spacerun:yes'>        </span>=&gt;<span
style='mso-spacerun:yes'>     </span>L’utente richiedente, proprietario
dell’accesskey, ha terminato il credito.<o:p></o:p></span></p>

<p class=MsoNormal style='margin-left:35.4pt'><span style='font-size:10.0pt;
line-height:107%'>&quot;Enabled&quot; =&gt;<span style='mso-spacerun:yes'> 
</span>Utente Abilitato (response dell’esito su campo esito_durc)<o:p></o:p></span></p>

<p class=MsoNormal style='margin-left:35.4pt'><span style='font-size:10.0pt;
line-height:107%'>&quot;accesskey is Required&quot; =&gt; Quando non viene
indicata alcuna accesskey nella request<o:p></o:p></span></p>

<p class=MsoNormal style='margin-left:35.4pt'><span style='font-size:10.0pt;
line-height:107%'>&quot;codice_fiscale wrong length&quot; =&gt; Lunghezza del
codice fiscale errata<o:p></o:p></span></p>

<p class=MsoNormal style='margin-left:35.4pt'><span style='font-size:10.0pt;
line-height:107%'>&quot;codice_fiscale is Required&quot; =&gt; Quando non viene
indicato alcun codice fiscale nella request<o:p></o:p></span></p>

<p class=MsoNormal style='margin-left:35.4pt'><span style='font-size:10.0pt;
line-height:107%'>&quot;purpose_id is wrong&quot; =&gt;L’indicazione nella
request risulta errata, inesistente (nei custom definiti dall’utente)<o:p></o:p></span></p>

<p class=MsoNormal style='margin-left:35.4pt'><span style='font-size:10.0pt;
line-height:107%'>&quot;purpose_id is Required&quot; =&gt; Quando non viene
indicato l’etichetta funzionalità chiamante nella request<o:p></o:p></span></p>

<p class=MsoNormal style='margin-left:35.4pt'><span style='font-size:10.0pt;
line-height:107%'>&quot;pdf_needed field is wrong value need to be 0 to know
only the result or 1 to download also the PDF&quot;<span
style='mso-spacerun:yes'>  </span>=&gt;<span style='mso-spacerun:yes'> 
</span>Quando viene indicato un valore richiesta pdf diverso da zero (pdf non
richiesto) o uno (pdf richiesto)<o:p></o:p></span></p>

<p class=MsoNormal style='margin-left:35.4pt'><span style='font-size:10.0pt;
line-height:107%'>&quot;pdf_needed field is Required 0 to know only the result
or 1 to download also the PDF&quot; =&gt; Quando non viene indicato la
richiesta del pdf (richiesto 1 o non richiesto 0) nella request<o:p></o:p></span></p>

<p class=MsoNormal style='margin-left:35.4pt'><span style='font-size:10.0pt;
line-height:107%'>&quot;email_richiedente field is in Invalid email
format&quot; =&gt;<span style='mso-spacerun:yes'>  </span>non è un campo obbligatorio
ma la servlet effettua un controllo sintattico del corretto formato validità
della email indicata.<o:p></o:p></span></p>

<p class=MsoNormal style='margin-left:35.4pt'><span style='font-size:10.0pt;
line-height:107%'>&quot;dettaglio_chiamante field is Required to make the
request&quot; =&gt; Quando non viene indicato alcun dettaglio_chiamante nella
request<o:p></o:p></span></p>

<p class=MsoNormal><span style='font-size:10.0pt;line-height:107%;color:black'><o:p>&nbsp;</o:p></span></p>

<p class=MsoNormal><b style='mso-bidi-font-weight:normal'>codice_fiscale <span
style='mso-spacerun:yes'> </span></b>=&gt; restituisce il codice_fiscale
richiesto nella request</p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><b style='mso-bidi-font-weight:normal'>esito_durc<span
style='mso-spacerun:yes'>  </span></b>=&gt; restituisce l’esito della richiesta
al momento dell’interrogazione. Risulterà in PENDING, quindi in attesa
innescando immediatamente nel backend una priorità della lavorazione del DURC
nel robot di riferimento in fase della presente richiesta, nel caso non sia
subito disponibile il DURC per il codice_fiscale indicato.</p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><b style='mso-bidi-font-weight:normal'>data_scadenza</b><span
style='mso-spacerun:yes'>   </span>=&gt; Questo campo indica la data di
scadenza del DURC, sarà presente solo nel caso l’ esito_durc sia differente da
stato PENDING</p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><b style='mso-bidi-font-weight:normal'>pdf_file<span
style='mso-spacerun:yes'>   </span></b>=&gt;<span style='mso-spacerun:yes'>  
</span>Questo campo indica il link dinamico per il download del file DURC in
formato pdf, sarà presente solo nel caso sia stato indicato il valore 1 nella
request del campo pdf_needed.</p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal>In conclusione La seconda web service (<a
href="http://durc.vincix.com/downloader.php"><span style='color:#0563C1'>http://durc.vincix.com/downloader.php</span></a>)
si compone dei seguenti parametri richiamabili da Client sia in modatilà HTTP a
scelta sia GET che POST:</p>

<p class=MsoNormal><u>Parametri di INPUT Obbligatori (solo in caso di richiesta
nella request precedentemente fatta)<span style='mso-spacerun:yes'>  
</span>downloader.php:<o:p></o:p></u></p>

<p class=MsoNormal><b style='mso-bidi-font-weight:normal'>streamfile</b><span
style='mso-spacerun:yes'>   </span>=&gt;<span style='mso-spacerun:yes'>  
</span>codice di accesso al download del file univoco (in risposta pdf_file)
della request fatta precedentemente</p>

<p class=MsoNormal>Nella request, in esempio precedente, possiamo utilizzare ad
esempio il seguente link decodificato, quindi, dal json client come segue:</p>

<p class=MsoNormal><span style='font-size:10.0pt;line-height:107%;color:black'>http://durc.vincix.com/downloader.php?streamfile=36</span><span
style='font-size:10.0pt;line-height:107%'>5<span style='color:black'>0d46</span>4<span
style='color:black'>37ee1cc5a</span>6<span style='color:black'>556a758fa</span>99<span
style='color:black'>fea</span></span></p>

<p class=MsoNormal>Di seguito la risposta della servlet in caso la chiave di
accesso al download sia inesistente o non fosse valida:</p>

<p class=MsoNormal style='margin-left:35.4pt'><span style='font-size:10.0pt;
line-height:107%;color:black'>{&quot;response&quot;:&quot;downloadkey field is
not valid&quot;}</span><span style='font-size:10.0pt;line-height:107%'><o:p></o:p></span></p>

<p class=MsoNormal>Nel caso invece la richiesta fosse valida, viene forzato il
download streaming al client del file pdf richiesto.</p>

<p class=MsoNormal><o:p>&nbsp;</o:p></p>

<p class=MsoNormal><span style='mso-spacerun:yes'>  </span><b style='mso-bidi-font-weight:
normal'><o:p></o:p></b></p>

</div>

</body>
			  
			  
			  
			  
			  
			  
			  
			  
			  
			  
			  
                 <!-- /. ROW  -->           
            </div>
             <!-- /. PAGE INNER  -->
        
             </div>
             <!-- /. PAGE WRAPPER  -->
		 
		 
<?php  require_once('../footer-pag.php');   ?>		 