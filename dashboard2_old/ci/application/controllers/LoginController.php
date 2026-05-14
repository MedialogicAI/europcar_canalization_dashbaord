<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require("MainController.php");

class LoginController extends MainController {

    public function __construct(){
        parent::__construct();
    }

    public function doLogin() {
    	if (is_null($this->input->post()) || is_null($this->input->post("login")) || is_null($this->input->post("pwd"))) {
            $this->sendError("accessFailed");
            return;
        }
        $login = $this->input->post("login");
        $pwd = $this->input->post("pwd");

        $this->db->select('users.*, profiles.profile, profiles.id as profile_id, profiles.type as profile_type');
        $this->db->from('users');
        $this->db->join('profiles', 'users.profile_id = profiles.id');
        $this->db->where('users.login',$login);
        $this->db->where('users.enabled',1);
        $arrData = $this->db->get()->result();

        if (count($arrData)>0) {
            // verify password
            $db_pwd = $arrData[0]->pwd;
            $blnSuccess = password_verify($pwd, $db_pwd);
            if ($blnSuccess) {
                $this->session->loggedUser = $arrData[0];
                // remove not necessary data
                unset($arrData[0]->pwd);
                unset($arrData[0]->email_confirm_code);
                $this->sendOutput($arrData);
            }
            else {
                $this->session->loggedUserId = null;
                $this->sendError("accessFailed");
            }
        }
        else {
            $this->session->loggedUserId = null;
            $this->sendError("accessFailed");
        }
	}

    public function doLogout() {
        $this->session->loggedUser = null;
        $this->sendSuccess('loggedOut');
        exit();
    }


}
