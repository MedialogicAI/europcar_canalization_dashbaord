<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require("MainController.php");

class UsersController extends MainController {

    public function __construct(){
        parent::__construct(true);
    }

    public function getUsers() {
		if (!$this->isLoggedManager()) {
            $this->sendNotAuthorized();
            return;
        }
        $this->db->select('users.*, profiles.profile, profiles.id as profile_id,  profiles.type as profile_type');
        $this->db->from('users');
        $this->db->join('profiles', 'users.profile_id = profiles.id');
        $this->db->order_by("users.lastname");
        $this->db->order_by("users.name");
        $arrData = $this->db->get()->result();

        // remove not necessary data
        foreach($arrData as &$el){
            unset($el->pwd);
            unset($el->email_confirm_code);
        }

        $this->sendOutput($arrData);
	}

    public function addUser() {
        if (!$this->isLoggedManager()) {
            $this->sendNotAuthorized();
            return;
        }
        if (is_null($this->input->post())) {
            $this->sendError("formInvalidData");
            return;
        }

        $profile_id = is_null($this->input->post("profile_id")) ? 0 : intval($this->input->post("profile_id"));
        if ($profile_id<1) {
            $this->sendError("formInvalidData");
            return;
        }
        $enabled = is_null($this->input->post("enabled")) ? 0 : intval($this->input->post("enabled"));
        if ($enabled!=0 && $enabled!=1) {
            $this->sendError("formInvalidData");
            return;
        }

        $lastname = is_null($this->input->post("lastname")) ? "" : ucwords(strtolower(trim($this->input->post("lastname"))));
        if ($lastname=="") {
            $this->sendError("formInvalidData");
            return;
        }

        $name = is_null($this->input->post("name")) ? "" : ucwords(strtolower(trim($this->input->post("name"))));
        if ($name=="") {
            $this->sendError("formInvalidData");
            return;
        }

        $login = is_null($this->input->post("login")) ? "" : strtolower(trim($this->input->post("login")));
        if ($login=="") {
            $this->sendError("formInvalidData");
            return;
        }
        $email = is_null($this->input->post("email")) ? "" : strtolower(trim($this->input->post("email")));
        if ($email=="" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->sendError("formInvalidData");
            return;
        }
        $station = $this->input->post("station");
		$station = (empty($station)) ? null : strtoupper(trim($station));
		if ($login=="") {
			$this->sendError("formInvalidData");
			return;
		}


        // verify login in db
        $this->db->select('id');
        $this->db->from('users');
        $this->db->where('login',$login);
        $arrData = $this->db->get()->result();
        if (count($arrData)>0) {
            $this->sendError("formUsernameExists");
            return;
        }
        // verify email in db
        $this->db->select('id');
        $this->db->from('users');
        $this->db->where('email',$email);
        $arrData = $this->db->get()->result();
        if (count($arrData)>0) {
            $this->sendError("formEmailExists");
            return;
        }
		// verify name and last name in db
		$this->db->select('id');
		$this->db->from('users');
		$this->db->where('name',$name);
		$this->db->where('lastname',$lastname);
		$arrData = $this->db->get()->result();
		if (count($arrData)>0) {
			$this->sendError("formNameExists");
			return;
		}

        // password control
        $password1 = is_null($this->input->post("password1")) ? "" : trim($this->input->post("password1"));
        $password2 = is_null($this->input->post("password2")) ? "" : trim($this->input->post("password2"));
        $blnPasswordAuto = true;
        if ($password1!="") {
            if ($password1!=$password2) {
                $this->sendError("formInvalidPassword");
                return;
            }
            else {
                $blnPasswordAuto = false;
            }
        }
        else {
            if ($password2!="") {
                $this->sendError("formInvalidPassword");
                return;
            }
        }

		if ($blnPasswordAuto) {
			$realPwd = ($this->appConfig["enableSubcriptionEmail"]) ? $this->getRandomString() : $this->appConfig["defaultUserPassword"];
		}
		else {
			$realPwd = $password1;
		}
        $pwd = password_hash($realPwd,PASSWORD_DEFAULT);

        // insert query
        $arrInsert = array(
            'name' => $name,
            'lastname' => $lastname,
            'login' => $login,
            'pwd' => $pwd,
            'email' => $email,
            'profile_id' => $profile_id,
            'station' => $station,
            'enabled' => $enabled
        );
        if ($this->db->insert('users', $arrInsert)) {
            if ($this->appConfig['enableSubcriptionEmail']) {
                $userData = array(
                    'name' => $name,
                    'lastname' => $lastname,
                    'login' => $login,
                    'pwd' => $realPwd
                );
                $emailBody = $this->createSubscriptionEmail($userData);
                if ($this->sendEmail($email,$this->appConfig['emailConfig']['subscriptionSubject'],$emailBody)) {
                    $this->writeLog("INSERT USER");
                    $this->sendSuccess("formUserCreated");
                }
                else {
                    // delete inserted user
                    $this->db->select('id');
                    $this->db->from('users');
                    $this->db->where('login',$login);
                    $arrData = $this->db->get()->result();
                    $lastId = $arrData[0]->id;
                    $this->db->delete('users', array('id' => $lastId));
                    $this->sendError("errSendingEmail");
                }
            }
            else {
                $this->sendSuccess("formUserCreated");
            }
        }
        else {
            $this->sendError("insertFailed");
        }

    }

    public function editUser() {
        if (!$this->isLoggedManager()) {
            $this->sendNotAuthorized();
            return;
        }
        if (is_null($this->input->post())) {
            $this->sendError("formInvalidData");
            return;
        }

        $id = is_null($this->input->post("id")) ? 0 : intval($this->input->post("id"));
        if ($id<1) {
            $this->sendError("formInvalidData");
            return;
        }

        $profile_id = is_null($this->input->post("profile_id")) ? 0 : intval($this->input->post("profile_id"));
        if ($profile_id<1) {
            $this->sendError("formInvalidData");
            return;
        }
        $enabled = is_null($this->input->post("enabled")) ? 0 : intval($this->input->post("enabled"));
        if ($enabled!=0 && $enabled!=1) {
            $this->sendError("formInvalidData");
            return;
        }

        $lastname = is_null($this->input->post("lastname")) ? "" : ucwords(strtolower(trim($this->input->post("lastname"))));
        if ($lastname=="") {
            $this->sendError("formInvalidData");
            return;
        }

        $name = is_null($this->input->post("name")) ? "" : ucwords(strtolower(trim($this->input->post("name"))));
        if ($name=="") {
            $this->sendError("formInvalidData");
            return;
        }

        $login = is_null($this->input->post("login")) ? "" : strtolower(trim($this->input->post("login")));
        if ($login=="") {
            $this->sendError("formInvalidData");
            return;
        }
        $email = is_null($this->input->post("email")) ? "" : strtolower(trim($this->input->post("email")));
        if ($email=="" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->sendError("formInvalidData");
            return;
        }
		$station = $this->input->post("station");
		$station = (empty($station)) ? null : strtoupper(trim($station));
		if ($login=="") {
			$this->sendError("formInvalidData");
			return;
		}

        // load user data
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('id',$id);
        $arrUserData = $this->db->get()->result();
        if (count($arrUserData)<1) {
            $this->sendError("recordNotFound");
            return;
        }

        // login modified
        $blnLoginUpdated = false;
        if ($login!=$arrUserData[0]->login) {
            $this->db->select('id');
            $this->db->from('users');
            $this->db->where('login',$login);
            $arrData = $this->db->get()->result();
            if (count($arrData)>0) {
                $this->sendError("formUsernameExists");
                return;
            }
            else {
                $blnLoginUpdated = true;
            }
        }

		// verify name and last name in db
		if ($name!=$arrUserData[0]->name || $lastname!=$arrUserData[0]->lastname) {
			$this->db->select('id');
			$this->db->from('users');
			$this->db->where('name',$name);
			$this->db->where('lastname',$lastname);
			$arrData = $this->db->get()->result();
			if (count($arrData)>0) {
				$this->sendError("formNameExists");
				return;
			}
		}

        // email modified
        $blnEmailUpdated = false;
        if ($email!=$arrUserData[0]->email) {
            $this->db->select('id');
            $this->db->from('users');
            $this->db->where('email',$email);
            $arrData = $this->db->get()->result();
            if (count($arrData)>0) {
                $this->sendError("formEmailExists");
                return;
            }
            else {
                $blnEmailUpdated = true;
            }
        }

        // password control
        $password1 = is_null($this->input->post("password1")) ? "" : trim($this->input->post("password1"));
        $password2 = is_null($this->input->post("password2")) ? "" : trim($this->input->post("password2"));
        $blnPasswordUpdated = false;
        $pwd = "";
        if ($password1!="") {
            if ($password1!=$password2) {
                $this->sendError("formInvalidPassword");
                return;
            }
            else {
                $blnPasswordUpdated = true;
            }
        }
        else {
            if ($password2!="") {
                $this->sendError("formInvalidPassword");
                return;
            }
        }
        if ($blnPasswordUpdated) {
            $realPwd = $password1;
            $pwd = password_hash($realPwd,PASSWORD_DEFAULT);
        }

        $arrUpdate = array(
            'name' => $name,
            'lastname' => $lastname,
            'login' => $login,
            'email' => $email,
            'profile_id' => $profile_id,
            'station' => $station,
            'enabled' => $enabled
        );
        if ($blnPasswordUpdated) {
            $arrUpdate['pwd'] = $pwd;
        }
        $this->db->where('id', $id);
        if ($this->db->update('users', $arrUpdate)) {
            $this->writeLog("EDIT USER");

            //check if the user is updating himself
            if ($this->getLoggedId()==$id) {
                $this->doLogout();
                return;
            }

            // TODO: GESTIRE INVIO EMAIL NEI VARI CASI
            $this->sendSuccess("formUserUpdated");
        }
        else {
            $this->sendError("updateFailed");
        }

    }

    public function editUserProfile() {
        $isLogged = $this->isLogged();
        if (!$isLogged) {
            $this->sendNotAuthorized();
            return;
        }
        if (is_null($this->input->post())) {
            $this->sendError("formInvalidData");
            return;
        }

        $id = is_null($this->input->post("id")) ? 0 : intval($this->input->post("id"));
        if ($id<1) {
            $this->sendError("formInvalidData");
            return;
        }
        if ($id!=$this->getLoggedId()) {
            $this->sendNotAuthorized();
            return;
        }

        $oldPwd = is_null($this->input->post("oldpwd")) ? "" : trim($this->input->post("oldpwd"));
        if ($oldPwd=="") {
            $this->sendError("formInvalidData");
            return;
        }

        $lastname = is_null($this->input->post("lastname")) ? "" : ucfirst(strtolower(trim($this->input->post("lastname"))));
        if ($lastname=="") {
            $this->sendError("formInvalidData");
            return;
        }

        $name = is_null($this->input->post("name")) ? "" : ucfirst(strtolower(trim($this->input->post("name"))));
        if ($name=="") {
            $this->sendError("formInvalidData");
            return;
        }

        $login = is_null($this->input->post("login")) ? "" : strtolower(trim($this->input->post("login")));
        if ($login=="") {
            $this->sendError("formInvalidData");
            return;
        }
        $email = is_null($this->input->post("email")) ? "" : strtolower(trim($this->input->post("email")));
        if ($email=="" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->sendError("formInvalidData");
            return;
        }

        // load user data
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('id',$id);
        $arrUserData = $this->db->get()->result();
        if (count($arrUserData)<1) {
            $this->sendError("recordNotFound");
            return;
        }

        // check current password
        $blnSuccess = password_verify($oldPwd, $arrUserData[0]->pwd);
        if (!$blnSuccess) {
            $this->session->loggedUserId = null;
            $this->sendError("formInvalidPassword");
            return;
        }

        // login modified verify exists in db
        $blnLoginUpdated = false;
        if ($login!=$arrUserData[0]->login) {
            $this->db->select('id');
            $this->db->from('users');
            $this->db->where('login',$login);
            $arrData = $this->db->get()->result();
            if (count($arrData)>0) {
                $this->sendError("formUsernameExists");
                return;
            }
            else {
                $blnLoginUpdated = true;
            }
        }

		// verify name and last name in db
		if ($name!=$arrUserData[0]->name || $lastname!=$arrUserData[0]->lastname) {
			$this->db->select('id');
			$this->db->from('users');
			$this->db->where('name',$name);
			$this->db->where('lastname',$lastname);
			$arrData = $this->db->get()->result();
			if (count($arrData)>0) {
				$this->sendError("formNameExists");
				return;
			}
		}

        // email modified
        $blnEmailUpdated = false;
        if ($email!=$arrUserData[0]->email) {
            $this->db->select('id');
            $this->db->from('users');
            $this->db->where('email',$email);
            $arrData = $this->db->get()->result();
            if (count($arrData)>0) {
                $this->sendError("formEmailExists");
                return;
            }
            else {
                $blnEmailUpdated = true;
            }
        }

        // password control
        $password1 = is_null($this->input->post("password1")) ? "" : trim($this->input->post("password1"));
        $password2 = is_null($this->input->post("password2")) ? "" : trim($this->input->post("password2"));
        $blnPasswordUpdated = false;
        $pwd = "";
        if ($password1!="") {
            if ($password1!=$password2) {
                $this->sendError("formInvalidPassword");
                return;
            }
            else {
                $blnPasswordUpdated = true;
            }
        }
        else {
            if ($password2!="") {
                $this->sendError("formInvalidPassword");
                return;
            }
        }
        if ($blnPasswordUpdated) {
            $realPwd = $password1;
            $pwd = password_hash($realPwd,PASSWORD_DEFAULT);
        }

        $arrUpdate = array(
            'name' => $name,
            'lastname' => $lastname,
            'login' => $login,
            'email' => $email
        );
        if ($blnPasswordUpdated) {
            $arrUpdate['pwd'] = $pwd;
        }
        $this->db->where('id', $id);
        if ($this->db->update('users', $arrUpdate)) {
            $this->writeLog("EDIT USER");

            // email changed: send confirmation mail
            if ($blnEmailUpdated) {
                $userData = array(
                    'id' => $id,
                    'name' => $name,
                    'lastname' => $lastname,
                    'login' => $login,
                    'pwd' => $realPwd
                );
                $emailBody = $this->createChangeEmail($userData);
                if ($this->sendEmail($email,$this->appConfig['emailConfig']['emailChangeSubject'],$emailBody)) {
                    $arrUpdate = array(
                        'enabled' => 0
                    );
                    $this->db->where('id', $id);
                    $this->db->update('users', $arrUpdate);
                }
                else {
                    $this->writeLog("EMAIL CHANGE SENDING FAILED");
                    $blnEmailUpdated = false;
                    $arrUpdate = array(
                        'email' => $arrUserData[0]->email
                    );
                    $this->db->where('id', $id);
                    $this->db->update('users', $arrUpdate);
                }
            }
            $this->sendSuccess("formProfileUpdated");
        }
        else {
            $this->sendError("updateFailed");
        }


    }

    public function delUser($userId=null) {
        if (!$this->isLoggedManager()) {
            $this->sendNotAuthorized();
            return;
        }
        $userId = intval($userId);
        if ($userId<1) {
            $this->sendError("formInvalidData");
            return;
        }

        // user exists?
        $this->db->select('id,profile_id');
        $this->db->from('users');
        $this->db->where('id',$userId);
        $arrData = $this->db->get()->result();
        if (count($arrData)<1) {
            $this->sendError("recordNotFound");
            return;
        }

        //delete user
        $bln = $this->db->delete('users', array('id' => $userId));
        if ($bln) {
            $this->writeLog("DELETE USER");
            // user self delete
            if ($userId == $this->getLoggedId()) {
                $this->session->loggedUser = null;
            }
            $this->sendSuccess("User deleted!");
        }
        else {
            $this->sendError("deleteFailed");
        }
        return;
    }

    public function confirmEmail($userId=null,$code=null) {
        if (is_null($userId) || is_null($code)) {
            $this->goHome();
            return;
        }
        $userId = intval($userId);
        $code = "".$code;
        if ($userId<1 || $code=="") {
            $this->goHome();
            return;
        }

        // user exists?
        $this->db->select('id');
        $this->db->from('users');
        $this->db->where('id',$userId);
        $this->db->where('email_confirm_code',$code);
        $arrData = $this->db->get()->result();
        if (count($arrData)<1) {
            $this->goHome();
            return;
        }
        else {
            $arrUpdate = array(
                'email_confirm_code' => '',
                'enabled' => 1
            );
            $this->db->where('id', $userId);
            if ($this->db->update('users', $arrUpdate)) {
                $this->htmlRelink("Email confirmed!"); // not in language
            }
            else {
                $this->htmlRelink("Email NOT confirmed!"); // not in language
            }
        }
        return;
    }


    /*
    * ################# PASSWORD RECOVERY #################
    */
    public function Recovery() {
        if (is_null($this->input->post()) || is_null($this->input->post("login"))) {
            $this->sendError("Invalid data!");
            return;
        }
        $login = "".$this->input->post("login");
        if ($login==""){
            $this->sendError("formInvalidData");
            return;
        }

        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('login',$login);
        $arrData = $this->db->get()->result();
        if (count($arrData)<1) {
            $this->db->select('*');
            $this->db->from('users');
            $this->db->where('email',$login);
            $arrData = $this->db->get()->result();
            if (count($arrData)<1) {
                $this->sendError("recordNotFound");
                return;
            }
        }
        $id = $arrData[0]->id;
        $login = $arrData[0]->login;
        $name = $arrData[0]->name;
        $lastname = $arrData[0]->lastname;
        $email = $arrData[0]->email;
        $realPwd = $this->getRandomString();
        $pwd = password_hash($realPwd,PASSWORD_DEFAULT);
        $arrUpdate = array(
            'pwd' => $pwd
        );
        $this->db->where('id', $id);
        if ($this->db->update('users', $arrUpdate)) {
            // send email
            $userData = array(
                'name' => $name,
                'lastname' => $lastname,
                'login' => $login,
                'pwd' => $realPwd
            );
            $emailBody = $this->createRecoveryEmail($userData);
            if ($this->sendEmail($email,$this->appConfig['emailConfig']['subscriptionSubject'],$emailBody)) {
                $this->sendSuccess("emailSent");
            }
            else {
                $this->sendError("operationFailed");
            }
        }
        else {
            $this->sendError("operationFailed");
        }
        return;
    }


    /*
    * ################# CREATE SUBSCRIPTION EMAIL #################
    */
    private function createSubscriptionEmail($userData=array()) {
        if (is_null($userData) || !is_array($userData)) {return false;}
        // app vars
        $app_url = $this->appConfig["url"];
        $app_name = $this->appConfig["name"];

        // message
        $greeting = "Salve ";
        $greeting .= $userData['name']." ".$userData['lastname'].",\n";
        $info = "queste sono le credenziali per accedere all'applicazione $app_name\n\n";
        $info .= "Nome utente: ".$userData['login']."\n";
        $info .= "Password: ".$userData['pwd']."\n";
        $info .= "Indirizzo web: $app_url\n\n";
        $noreply = "\n\nNon rispondere al mittente di questa email in quanto non abilitato a ricevere posta.";

        $msgBody  = $greeting.$info.$noreply;

        return $msgBody;
    }

    /*
    * ################# CREATE RCOVERY EMAIL #################
    */
    private function createRecoveryEmail($userData=array()) {
        if (is_null($userData) || !is_array($userData)) {return false;}
        // app vars
        $app_url = $this->appConfig["url"];
        $app_name = $this->appConfig["name"];

        // message
        $greeting = "Salve ";
        $greeting .= $userData['name']." ".$userData['lastname'].",\n";
        $info = "è stato richiesto il recupero della password per $app_name.\n\n";
        $info .= "Queste sono le credenziali per accedere all'applicazione: \n\n";
        $info .= "Nome utente: ".$userData['login']."\n";
        $info .= "Password: ".$userData['pwd']."\n";
        $info .= "Indirizzo web: $app_url\n\n";
        $noreply = "\n\nNon rispondere al mittente di questa email in quanto non abilitato a ricevere posta.";

        $msgBody  = $greeting.$info.$noreply;

        return $msgBody;
    }

    /*
    * ################# CREATE SUBSCRIPTION EMAIL #################
    */
    private function createChangeEmail($userData=array()) {
        if (is_null($userData) || !is_array($userData)) {return false;}
        // app vars
        $app_url = $this->appConfig["url"];
        $app_name = $this->appConfig["name"];
        $id = $userData['id'];
        $random = $this->getRandomString(32);
        $link = $this->getConfirmEmailPath().$id."/".$random;

        $arrUpdate = array(
            'email_confirm_code' => $random
        );
        $this->db->where('id', $id);
        $this->db->update('users', $arrUpdate);

        // message
        $greeting = "Salve ";
        $greeting .= $userData['name']." ".$userData['lastname'].",\n";
        $info = "è stata modificata l'email per accedere all'applicazione $app_name.\n";
        $info .= "Per poter eseguire nuovamente l'accesso è necessario cliccare sul seguente link:\n\n";
        $info .= "$link\n\n";
        $info .= "Grazie.";
        $noreply = "\n\nNon rispondere al mittente di questa email in quanto non abilitato a ricevere posta.";

        $msgBody  = $greeting.$info.$noreply;

        return $msgBody;
    }

    /*
    * ################# CREATE SUBSCRIPTION EMAIL #################
    */
    private function createProfileEmail($userData=array()) {
        if (is_null($userData) || !is_array($userData)) {return false;}
        // app vars
        $app_url = $this->appConfig["url"];
        $app_name = $this->appConfig["name"];

        // message
        $greeting = "Salve ";
        $greeting .= $userData['name']." ".$userData['lastname'].",\n";
        $info = "il profilo per l'accesso all'applicazione $app_name è stato aggiornato.\n";
        $info .= "Queste sono le credenziali per effettuare l'accesso:\n\n";
        $info .= "Nome utente (email): ".$userData['login']."\n";
        $info .= "Password: ".$userData['pwd']."\n";
        $info .= "Indirizzo web: $app_url\n\n";
        $noreply = "\n\n\n\nNon rispondere al mittente di questa email in quanto non abilitato a ricevere posta.";

        $msgBody  = $greeting.$info.$noreply;

        return $msgBody;
    }


}
