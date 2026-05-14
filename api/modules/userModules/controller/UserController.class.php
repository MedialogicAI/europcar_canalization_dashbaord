<?php
namespace userModules\controller;

use DBConnector;
use Exception;
use userModules\dao\UserDAO;
require_once (__DIR__ . "/../dao/UserDAO.class.php");
require_once (__DIR__ . "/../../../classes/dao/parent/DBConnector.class.php");

class UserController extends DBConnector
{

    private $dao = null;

    private static $obj = null;

    private function __construct()
    {
        $this->dao = new UserDAO();
    }

    public static function getObject()
    {
        if (self::$obj === null) {
            self::$obj = new UserController();
        }
        return self::$obj;
    }
    
    /* for ec project mail data insert */
    public function addMailDataToDB($object){
        $conn = parent::connectPDO();
        try {
            $return_value = $this->dao->addMailDataToDB($conn, $object);
            parent::closePDO();
            return $return_value;
        } catch (Exception $exception) {
            /* $con->rollBack(); */
            $exception->getMessage();
            return false;
        }
    }
    
}

