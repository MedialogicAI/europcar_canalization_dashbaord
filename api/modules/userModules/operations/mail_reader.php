<?php 
error_reporting(E_ALL); 
ini_set('display_errors', 1);

use userModules\controller\UserController;
ob_start();

set_time_limit(4000); 
require_once (__DIR__ . "/../controller/UserController.class.php");
$ctrl = UserController::getObject();
$object = new stdClass();
$imapPath = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'ramesh535kumarb@gmail.com';
$password = 'ramesh@9505720080';
phpinfo();
exit();

$inbox = imap_open($imapPath,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());
// $emails = imap_search($inbox, 'FROM "test@gmail.com"'); // UNSEEN
$emails = imap_search($inbox,'ALL');
// rsort($emails);
// print_r($emails);
foreach($emails as $mail) {
    $object->file_count = 0;
    $object->subject = "";
    $object->from = "";
    $object->to = "";
    $object->message_id = "";
    $object->date = "";
    $object->mail_body = "";
    // $headerInfo = imap_headerinfo($inbox,$mail);
    $overview = imap_fetch_overview($inbox, $mail, 0);
    // print_r($overview);
    $overview = $overview[0];
    $object->subject = $overview->subject;
    $object->from = $overview->from;
    $object->to = $overview->to;
    $object->message_id = $overview->message_id;
    $object->date = $overview->date;
    // $output .= $headerInfo->reply_toaddress.'<br/>';
    // $output .= $headerInfo->senderaddress.'<br/>';
    $object->mail_body = imap_fetchbody($inbox, $mail, 1);
    // $messageExcerpt = substr($message, 0, 150);
    // $pmsg = trim(quoted_printable_decode($messageExcerpt));
    // print_r($message);
    /* $emailStructure = imap_fetchstructure($inbox,$mail);
    if(!isset($emailStructure->parts)) {
         $output .= imap_body($inbox, $mail, FT_PEEK); 
    } else {
    }  */
   $structure = imap_fetchstructure($inbox, $mail);
   // echo "Count: ". count($structure->parts);
   $attachments = array();
   if(isset($structure->parts) && count($structure->parts))
   {
       $object->file_count = count($structure->parts);
       for($i = 0; $i < count($structure->parts); $i++)
       {
           $attachments[$i] = array(
               'is_attachment' => false,
               'filename' => '',
               'name' => '',
               'attachment' => ''
           );
           
           if($structure->parts[$i]->ifdparameters)
           {
               foreach($structure->parts[$i]->dparameters as $object)
               {
                   if(strtolower($object->attribute) == 'filename')
                   {
                       $attachments[$i]['is_attachment'] = true;
                       $attachments[$i]['filename'] = $object->value;
                   }
               }
           }
           
           if($structure->parts[$i]->ifparameters)
           {
               foreach($structure->parts[$i]->parameters as $object)
               {
                   if(strtolower($object->attribute) == 'name')
                   {
                       $attachments[$i]['is_attachment'] = true;
                       $attachments[$i]['name'] = $object->value;
                   }
               }
           }
           
           if($attachments[$i]['is_attachment'])
           {
               $attachments[$i]['attachment'] = imap_fetchbody($inbox, $mail, $i+1);
               
               if($structure->parts[$i]->encoding == 3)
               {
                   $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
               }
               elseif($structure->parts[$i]->encoding == 4)
               {
                   $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
               }
           }
       }
   }
   
   foreach($attachments as $attachment)
   {
       if($attachment['is_attachment'] == 1)
       {
           $filename = $attachment['name'];
           if(empty($filename)) $filename = $attachment['filename'];
           
           if(empty($filename)) $filename = time() . ".dat";
           $folder = "attachment";
           if(!is_dir($folder))
           {
               mkdir($folder);
           }
           $fp = fopen("./". $folder ."/". $mail . "-" . $filename, "w+");
           fwrite($fp, $attachment['attachment']);
           fclose($fp);
       }
   } 
   
   $addData = $ctrl->addMailDataToDB($object);
}
if($addData){
    echo "Successfully uploaded";
}else{
    echo "Something went wrong, Please try again";
}
imap_expunge($inbox);
imap_close($inbox);
?>