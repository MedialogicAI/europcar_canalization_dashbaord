<?php
namespace userModules\dao;

use classes\dao\super\SuperDAO;
use Exception;
require_once(__DIR__."/../../../classes/dao/super/SuperDAO.class.php");

class UserDAO extends SuperDAO
{
    
    /* for ec project mail data insert */
    public function addMailDataToDB($conn, $object){
        try {
            $query = "INSERT INTO gestione_manuale 
                        	(body, subject, message_id, dt_recived, email_to, attachments) 
                        VALUES 
                        	('$object->mail_body', '$object->subject', '$object->message_id', '$object->date', '$object->to', '$object->file_count')";
            $conn->beginTransaction();
            $exe = $conn->prepare($query);
            $exe->execute();
            $last_id = $conn->lastInsertId();
            $conn->commit();
            return $last_id;
        } catch (Exception $exception) {
            $conn->rollBack();
            $exception->getMessage();
            return false;
        }
    }
    
}