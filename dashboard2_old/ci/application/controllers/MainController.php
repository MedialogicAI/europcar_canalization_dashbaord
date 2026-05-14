<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// php ini set
ini_set("memory_limit", -1);
ini_set("max_execution_time", 3600);
set_time_limit(0);

class MainController extends CI_Controller {

    // $loadConfig:true for controllers that use app config object
    public function __construct($loadConfig=false){
        parent::__construct();
        // ############################# APP CONFIG #############################
        if ($loadConfig) {
            $this->db->select('*');
            $this->db->from('app_cfg');
            $arrData = $this->db->get()->result();
            if (count($arrData) != 1) {
                die("ERROR LOADING APP CONFIG FROM DATABASE");
            } else {
                $cfg = $arrData[0];
                $appUrl = sprintf('%s/', rtrim($cfg->url, '/'));
                $this->appConfig = array(
                    "instDir" => 'dashboard2', // base app installation dir, '' if it's installed on server root dir
                    "uploadDir" => 'uploads', // base app upload dir
                    "uploadTempDirName" => 'temp', // base app upload dir
                    "uploadUserDir" => 'users', // users upload dir name
                    "ci" => 'ci', // codeIgniter dir
					"defaultUserPassword" => "password", // default password if email register disabled
                    "name" => $cfg->name, // the app name
                    "owner" => $cfg->owner, // the app owner/producer
                    "email" => ($cfg->contact_email != "") ? $cfg->contact_email : null, // general contact email for info, ecc...
                    "url" => $appUrl, // forntend app start page url - ends with =/"
                    "confirm_route" => 'confirm', // route name for confirm email script
                    "enableSubcriptionEmail" => ($cfg->enable_email_register===1) ? true : false, // true: send email link after subscription with activation link
					"enableLogger" => ($cfg->enable_logger===1) ? true : false, // true: use logger tracking
                    "confirmSubscriptionPage" => "ci/users/confirm", // base name for confirmation link, to add get param
                    "enableRecoveryPassword" => $cfg->enable_email_recovery, // true: enable recovery password system
					"defaultLocale" => $cfg->locale_default,
                    // EMAIL CONFIG
                    "emailConfig" => array(
                        "subscriptionSubject" => "Subscription - " . $cfg->title,
                        "profileEditSubject" => "Profile Change - " . $cfg->title,
                        "emailChangeSubject" => "Confirm email - " . $cfg->title,
                        "fromName" => $cfg->title . " - " . $cfg->owner,
                        "serverCfg" => array(
                            "protocol" => "smtp",
                            "smtp_host" => $cfg->email_smtp,
                            "smtp_user" => $cfg->email_sender,
                            "smtp_pass" => ($cfg->email_sender_pwd != "") ? $cfg->email_sender_pwd : null,
                            "smtp_crypto" => "ssl",
                            "mail_type" => "text",
                            "charset" => "utf-8",
                            "newline" => "\r\n",
                            "smtp_port" => 465
                        )
                    )
                );
            }
        }
    }

    public $appConfig; // set in constructor

    // USER PROFILES/ROLES - must match in database 'profiles' table
	// profile.type = 0 app user role
	// profile.type = 1 app manager role
	// profile.type = 2 sys/app admin role
    public $profiles = array(
        "admin_type" => 300,
        "manager_type" => 200, // manager is Europcar Maintenance
        "insurance_type" => 150, // manager is Europcar Maintenance
		// europcar profiles
		"fleet_manager_type" => 100, // fleet coordinator
		"fleet_coordinator_type" => 90, // fleet coordinator
		"stations_type" => 80,
    );


    // values in DB table 'tab_status'
	// index: status code
    public $statusPratices = array(
		400 => array("short_desc" => "word", "long_desc" => "word"),
		401 => array("short_desc" => "word", "long_desc" => "word"),
		402 => array("short_desc" => "word", "long_desc" => "word"),
		403 => array("short_desc" => "word", "long_desc" => "word"),
		405 => array("short_desc" => "word", "long_desc" => "word"),
		410 => array("short_desc" => "word", "long_desc" => "word"),
		420 => array("short_desc" => "word", "long_desc" => "word"),
		440 => array("short_desc" => "word", "long_desc" => "word"),
		460 => array("short_desc" => "word", "long_desc" => "word"),
		430 => array("short_desc" => "word", "long_desc" => "word"),
		450 => array("short_desc" => "word", "long_desc" => "word"),
		480 => array("short_desc" => "word", "long_desc" => "word"),
		490 => array("short_desc" => "word", "long_desc" => "word"),
		540 => array("short_desc" => "word", "long_desc" => "word"),
		560 => array("short_desc" => "word", "long_desc" => "word"),
		570 => array("short_desc" => "word", "long_desc" => "word"),
		600 => array("short_desc" => "word", "long_desc" => "word")
	);

	public function getStatusDesc($status,$blnLong=false) {
		$fld = ($blnLong) ? "long_desc" : "short_desc";
		return $this->statusPratices[$status][$fld];
	}

    public function getSessionField($fld=null) {
        if (is_null($fld)) {
            return $this->session->loggedUser;
        }
        else {
            return $this->session->loggedUser->$fld;
        }
    }

    public function getLoggedId() {
        if (!$this->isLogged()) {
            return 0;
        }
        return intval($this->getSessionField("id"));
    }

    public function getLoggedProfileId() {
        if (!$this->isLogged()) {
            return 0;
        }
        return intval($this->getSessionField("profile_id"));
    }

    public function isLogged() {
        return !is_null($this->session->loggedUser);
    }

	public function getAdminProfileType() {
		return $this->profiles['admin_type'];
	}

    public function isLoggedAdmin() {
        return $this->isLogged() && (intval($this->getSessionField("profile_type")) == $this->profiles['admin_type']);
    }

    public function isLoggedManager() {
    	return $this->isLogged() &&
			(
				$this->isLoggedAdmin() ||
				$this->isLoggedInsurance() ||
				intval($this->getSessionField("profile_type")) == $this->profiles['manager_type']
			);
	}

	public function isLoggedInsurance() {
		return $this->isLogged() &&
			(
				intval($this->getSessionField("profile_type")) == $this->profiles['insurance_type']
			);
	}

	// Europcar profiles manager = maintance !!!
//	public function isLoggedMaintenance() {
//		return $this->isLoggedManager();
//	}
//	public function getMaintenanceProfileId() {
//		return $this->profiles['manager_type'];
//	}

	public function isLoggedFleetManager() {
		return $this->isLogged() &&
			(
				intval($this->getSessionField("profile_type")) == $this->profiles['fleet_manager_type']
			);
	}

	public function isLoggedFleetCoordinator() {
		return $this->isLogged() &&
			(
				intval($this->getSessionField("profile_type")) == $this->profiles['fleet_coordinator_type']
			);
	}

	public function isLoggedStation() {
		return $this->isLogged() &&
			(
				intval($this->getSessionField("profile_type")) == $this->profiles['stations_type']
			);
	}

	public function getCoordinatorProfileId() {
		return $this->profiles['coordinator_type'];
	}


	public function getStationsProfileId() {
		return $this->profiles['stations_type'];
	}

	public function getLoggedCompleteName() {
		return $this->getSessionField("name")." ".$this->getSessionField("lastname");
	}



    // ################## UPLOAD PATHS & APP LINKS ##################
    // all end with '/'

    public function getBasePath() {
        $docRoot = $_SERVER['DOCUMENT_ROOT']; // CONFIG PARAM - document root
        $basePath  = sprintf('%s/', rtrim($docRoot, '/'));
        if ($this->appConfig['instDir']!="") {
			$basePath .= $this->appConfig['instDir']."/";
        }
        return $basePath;
    }

	public function getUploadTempPath($blnMakeDir=true) {
		$path = $this->getBasePath().$this->appConfig['uploadDir']."/".$this->appConfig['uploadTempDirName']."/";
		if (!file_exists($path)) {
			if ($blnMakeDir) {
				$bln = mkdir($path, 0750);
				if (!$bln) {return false;}
			}
			else {
				return false;
			}
		}
		return $path;
	}

    public function getUploadDir($blnMakeDir=true) {
        $path = $this->getBasePath().$this->appConfig['uploadDir']."/";
        if (!file_exists($path)) {
            if ($blnMakeDir) {
                $bln = mkdir($path, 0750);
                if (!$bln) {return false;}
            }
            else {
                return false;
            }
        }
        return $path;
    }

	public function getUploadUsersDir($blnMakeDir=true) {
		$path = $this->getUploadDir($blnMakeDir);
		if (!$path) {
			return false;
		}
		$path .= $this->appConfig['uploadUserDir']."/";
		if (!file_exists($path)) {
			if ($blnMakeDir) {
				$bln = mkdir($path, 0750);
				if (!$bln) {return false;}
			}
			else {
				return false;
			}
		}
		return $path;
	}

	// returns upload dir for a user by userId
	public function getUploadUserDir($userId=0,$blnMakeDir=true) {
		$userId = intval($userId);
    	if ($userId<1) {
    		return false;
		}
    	$path = $this->getUploadUsersDir($blnMakeDir);
    	if (!$path) {
    		return false;
		}
    	$path .= "$userId/";
		if (!file_exists($path)) {
			if ($blnMakeDir) {
				$bln = mkdir($path, 0750);
				if (!$bln) {return false;}
			}
			else {
				return false;
			}
		}
		return $path;
	}

	// returns upload dir for a user by userId
	public function getUploadLoggedUserDir($blnMakeDir=true) {
		return $this->getUploadUserDir($this->getLoggedId(),$blnMakeDir);
	}


    // APPLICATION PATHS
    // --- app links
    public function getCiPath() {
        return $this->appConfig['url'].$this->appConfig['ci']."/";
    }

    // confirm route path
    public function getConfirmEmailPath() {
        return $this->getCiPath().$this->appConfig['confirm_route']."/";
    }


    // ################## LOGGER ##################

    public function writeLog($operation="GENERAL",$query="") {
        if (!$this->appConfig['enableLogger']) {
        	return;
		}
		$operation = strtoupper($operation);
        $user_id = $this->getLoggedId();
        $name = $this->getSessionField('name');
        $lastname = $this->getSessionField('lastname');
        $timelog = time();
        if ($query=="") {
            $query = $this->db->last_query();
        }
        // insert query
        $arrInsert = array(
            'operation' => $operation,
            'user_id' => $user_id,
            'user' => $lastname." ".$name,
            'timelog' => $timelog,
            'query' => $query
        );
        $this->db->insert('logger', $arrInsert);

        return;
    }


    // ################## SENDING OUTPUT ##################

    public function sendZero($blnSendData=false,$blnSuccess=true) {
        if ($blnSendData) { // invia con formato data
            $this->sendOutput(Array(),$blnSuccess);
        }
        else {
            $this->output->set_content_type('application/json')->set_output(json_encode(Array()));
        }
        return;
    }

    // send data OUTPUT
    // fields: array (field:'field_name',value:'field_value'), add fields and values to json data
    public function sendOutput($arrData,$totRecord=null,$blnSuccess=true,$fields=null) {
        // default data
        $totalRecord = ($totRecord) ? $totRecord : count($arrData);
        $output = array(
            'success' => $blnSuccess,
            'data' => $arrData,
            'totalCount' => $totalRecord
        );
        if ($fields) {
            foreach($fields as $el) {
                $output[$el['field']] = $el['value'];
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($output));
        return;
    }

    public function sendSuccess($msg='',$arrData=array()) {
        // default data
        $output = array(
            'success' => true,
            'data' => $arrData,
            'msg' => $msg
        );
        $this->output->set_content_type('application/json')->set_output(json_encode($output));
        return;
    }

    // Send OK output with msg
    public function sendOk($msg='') {
        // default data
        $output = array(
            'success' => true,
            'msg' => $msg
        );
        $this->output->set_content_type('application/json')->set_output(json_encode($output));
        return;
    }

    // Send error output with msg
    public function sendError($msg='') {
        // default data
        $output = array(
            'success' => false,
            'msg' => $msg
        );
        $this->output->set_content_type('application/json')->set_output(json_encode($output));
        return;
    }

    // Send unauthorized
    public function sendNotAuthorized($msg='permissionDenied') {
        $this->session->loggedUser = null;
        http_response_code(403);
        return;
    }

    // exec logout
    public function doLogout() {
        $this->sendNotAuthorized('loggedOut');
        exit();
    }

    // go home link
    public function goHome() {
        $url = $this->appConfig['url'];
        header("location: ".$url,200);
    }

    // go home link
    public function htmlRelink($textOperation="",$secWait=5,$linkTo="",$textTitle="",$textRedirect="",$blnAddClickHere=true) {
        $url = $this->appConfig['url'];
        $appName = $this->appConfig['name'];
        $textTitle = ($textTitle=="") ? $appName : $textTitle;
        $textOperation = ($textOperation=="") ? "" : $textOperation;
        $textRedirect = ($textRedirect=="") ? "Automatic redirect..." : $textRedirect;
        $linkTo = ($linkTo=="") ? $url : $linkTo;
        $secWait = 1000*intval($secWait);
        $pageTitle = $this->appConfig['name'];
        $html = '<html>';
        $html .= '    <head>';
        $html .= '        <title>'.$pageTitle.'</title>';
        $html .= '        <script>';
        $html .= '          setTimeout(function(){';
        $html .= '              location.href= "'.$linkTo.'"';
        $html .= '          }, '.$secWait.');';
        $html .= '        </script>';
        $html .= '    </head>';
        $html .= '    <body>';
        $html .= '        <div style="width:100%;text-align:center;font-family: Calibri,Arial;">';
        $html .= '          <h1 style="display:block;font-weight:bold;color: green;">'.$textTitle.'</h1>';
        if ($textOperation!="") {
            $html .= '          <h1 style="display:block;font-weight:bold;color: red;">'.$textOperation.'</h1>';
        }
        $html .= '          <h3 style="display:block;">'.$textRedirect.'</h3>';
        if ($blnAddClickHere) {
            $html .= '          <div style="display:block;">';
            $html .= '          <div style="display:block;"> <a href="'.$linkTo.'">or click here</a></div>';
            $html .= '          </div>';
        }
        $html .= '        </div>';
        $html .= '    </body>';
        $html .= '</html>';

        header('Content-Type: text/html; charset=utf-8');
        echo $html;
    }

    /*
    * ########## RANDOM STRING ##########
    */
    public function getRandomString($length=10,$blnChars=true,$blnNumbers=true,$blnLower=true,$blnUpper=true) {
        $characters = '';
        if ($length<1) {$length = 1;}
        if ($blnNumbers) {$characters .= '0123456789';}
        if ($blnChars && $blnLower) {$characters .= 'abcdefghijklmnopqrstuvwxyz';}
        if ($blnChars && $blnUpper) {$characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';}
        if ($characters == '') {return '';}
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /*
    * ########## SEND EMAIL ##########
    */
    public function sendEmail($to,$subject,$body,$format='text') {
        $cfg = $this->appConfig['emailConfig'];
        $cfgInit = $this->appConfig['emailConfig']["serverCfg"];
        $this->email->initialize($cfgInit);
        $this->email->from($cfgInit['smtp_user'],$cfg['fromName']);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($body);
        $blnOk = false;
        try {
            $blnOk = @$this->email->send();
        }
        catch (Exception $e){
            $blnOk = false;
        }
        return $blnOk;
    }


	/*
	* ########## BUILD SELECT FIELDS FOR QUERY ##########
	 * TO DO comments
	*/
	public function buildSelectFields($arrFields=Array(),$blnApplyFunctions=true) {
		$strFields = "";
		$blnFirst = true;
		$v = "";
		foreach ($arrFields as $field) {
			$fun = ($blnApplyFunctions && !empty($field['function'])) ? $field['function'] : null;
			if (empty($field['fieldName'])) {
				return("*");
			}
			$field = $field['fieldName'];
			if (!is_null($fun)) {
				$field = $fun."(".$field.") as $field";
			}
			$strFields .= $v."$field";
			if ($blnFirst) {
				$blnFirst = false;
				$v = ",";
			}
		}
		return $strFields;
	}

	/*
	* ########## GET EUROPCAR PROVINCES BY REGION ##########
	 * $arrRegion: region list, field name "id"
	 * $type: string: direct | network
	 * return array: $el[i] = Array("id" => province_name)
	*/
//	public function getRegionsDistinct($type) {
//		if ((strtolower($type)!=="direct" && strtolower($type)!=="network")) {
//			return Array();
//		}
//		// direct
//		$arrResult = Array();
//		if ($type==="direct") {
//			$this->db->select('REGIONE as region');
//			$this->db->distinct();
//			$this->db->from('canaliz_rete');
//			$this->db->where('NETWORK','diretti');
//			$this->db->order_by('REGIONE','asc');
//			$arrResult = $this->db->get()->result();
//		}
//		// network
//		if ($type==="network") {
//			$this->db->select('REGIONE as region');
//			$this->db->distinct();
//			$this->db->from('canaliz_total');
//			$this->db->order_by('REGIONE', 'asc');
//			$arrResult = $this->db->get()->result();
//		}
//		$arrReturn = Array();
//		foreach ($arrResult as $el) {
//			$r = (object) Array(
//				"id" => $el->region
//			);
//			array_push($arrReturn,$r);
//		}
//		return $arrReturn;
//	}

	// ################################### REGIONS ###################################
	// GET REGIONS PER USER
	public function getRegionsUser() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$userField = ($this->isLoggedFleetManager()) ? "REGIONAL_FLEET_MGR" : "FLEET_COORDINATOR";
		$userCompleteName = $this->getLoggedCompleteName();
		$arrData = Array();
		$arrRegions = Array();
		$this->db->select('REGIONE as region');
		$this->db->distinct();
		$this->db->from('canaliz_total');
		$this->db->where("REGIONE is not null and REGIONE<>''");
		if (!$this->isLoggedManager()) {
			$this->db->where($userField,$userCompleteName);
		}
		$this->db->order_by('REGIONE','asc');
		$arrRegions = $this->db->get()->result();
		$i= 0;
		foreach($arrRegions as $el) {
			if (!empty($el->region)) {
				$value = $el->region;
				$region = strtoupper($el->region);
				$r = Array(
					"id" => $value,
					"name" => $region
				);
				array_push($arrData,$r);
			}
		}
		sort($arrData);
		return $arrData;
	}

	// ################################### PROVINCES ###################################
	// GET LOGGED USER PROVINCES LIST
	public function getProvincesByRegion($arrRegions=Array(),$isObj=true) {
		if (count($arrRegions)<1) {
			return Array();
		}
		$arrData = Array();
		$conditions = "1=1";
		$conditionUser = "";
		if (!$this->isLoggedManager()) {
			$userField = ($this->isLoggedFleetManager()) ? "REGIONAL_FLEET_MGR" : "FLEET_COORDINATOR";
			$userCompleteName = str_replace("'", "''", $this->getLoggedCompleteName());
			$conditionUser = "$userField = '$userCompleteName'";
			$conditions = $conditionUser;
		}

		$sqlSelect = "SELECT DISTINCT `PROV.` as province FROM canaliz_total";
		$or = "";
		$blnFirst = true;
		$conditionsRegions = "";
		foreach ($arrRegions as $el) {
			$item = ($isObj) ? $el->id : $el['id'];
			$prov = str_replace("'", "''", $item);
			$conditionsRegions .= $or . " REGIONE='$prov'";
			if ($blnFirst) {
				$blnFirst = false;
				$or = ' OR ';
			}
		}
		if ($conditionsRegions == "") {
			$conditionsRegions = "(1=1)";
		}
		$order = "ORDER BY `PROV.`";
		$sql = "$sqlSelect where ($conditions) AND ($conditionsRegions) $order";
		$arrProvinces = $this->db->query($sql)->result();
		foreach($arrProvinces as $el) {
			if (!empty($el->province)) {
				$value = $el->province;
				$province = strtoupper($el->province);
				$r = Array(
					"id" => $value,
					"name" => $province
				);
				array_push($arrData,$r);
			}
		}
		return $arrData;
	}

	// ################################### USER STATIONS LIST ###################################
	// GET STATIONS LIST FOR LOGGED USER
	public function getStationsUser($limit=null, $start=null, $search=null) {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$blnPaging = (!is_null($start) && !is_null($limit)) ? true : false;
		$arrStations = Array();

		if ($this->isLoggedStation()) {
			$r = new \stdClass();
			$r->station_code = $this->getSessionField("login");
			$arrStations[0] = $r;
			return $arrStations;
		}
		$userField = ($this->isLoggedFleetManager()) ? "REGIONAL_FLEET_MGR" : "FLEET_COORDINATOR";
		$userCompleteName = $this->getLoggedCompleteName();
		$this->db->select('MAIN_COD_GWY_ as station_code');
		$this->db->distinct();
		$this->db->from('canaliz_total');
		$this->db->order_by("MAIN_COD_GWY_","ASC");
		if (!$this->isLoggedManager()) {
			$this->db->where($userField,$userCompleteName);
		}
		$arrStations = $this->db->get()->result();

		if ($blnPaging && (count($arrStations))>0) {
			$totalCount = count($arrStations);
			$userField = ($this->isLoggedFleetManager()) ? "REGIONAL_FLEET_MGR" : "FLEET_COORDINATOR";
			$userCompleteName = $this->getLoggedCompleteName();
			$this->db->select('MAIN_COD_GWY_ as station_code');
			$this->db->distinct();
			$this->db->from('canaliz_total');
			$this->db->limit($limit,$start);
			$this->db->order_by("MAIN_COD_GWY_","ASC");
			if (!$this->isLoggedManager()) {
				$this->db->where($userField,$userCompleteName);
			}
			$arrStations = $this->db->get()->result();
			return Array(
				"records" => $arrStations,
				"totalCount" => $totalCount
			);
		}


		return (count($arrStations)>0) ? $arrStations : Array();
	}

	public function buildSqlConditions($arrData,$keyfield,$queryField,$isObj=true,$operator="=",$logic="OR",$type="string") {
		$condition = "";
		$blnFirst = true;
		$opLogic = "";
		$apex = ($type==="numeric") ? "" : "'";
		foreach($arrData as $el) {
			$item = ($isObj) ? $el->$keyfield : $el[$keyfield];
			$val = str_replace("'","''",$item);
			$condition .= $opLogic."(".$queryField.$operator.$apex.$val.$apex.")";
			if ($blnFirst) {
				$blnFirst = false;
				$opLogic = " $logic ";
			}
		}
		return "($condition)";
	}


}
