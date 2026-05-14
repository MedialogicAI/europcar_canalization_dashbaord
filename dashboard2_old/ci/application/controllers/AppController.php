<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require("MainController.php");

class AppController extends MainController {

    public function __construct(){
        parent::__construct(true);
    }

    public function getAppCfg() {
        // only public fields, the method has public access!
        $fields = "name,title,subtitle,url,owner,subowner,version,locale_default,contact_phone,contact_email,
        	enable_locale, contact_address,enable_email_recovery";
        $this->db->select($fields);
        $this->db->from('app_cfg');
        $arrData = $this->db->get()->result();
        $this->sendOutput($arrData);
        return;
    }

    public function getFunctions() {
        $isLogged = $this->isLogged();
        if (!$isLogged) {
            $this->sendNotAuthorized();
            return;
        }
        $isAdmin = $this->isLoggedAdmin();
        $profileId = $this->getLoggedProfileId();
        $locale = $this->appConfig['defaultLocale'];
        if (!is_null($this->input->post("locale")) && "".$this->input->post("locale")!="") {
			$locale = strtolower($this->input->post("locale"));
		}
        if (!$isAdmin) {
            $this->db->select('functions.*,functions_locale.name as name,functions_locale.view_alias_text as view_alias_text');
            $this->db->from('functions');
            $this->db->join('functions_profiles', 'functions_profiles.function_id = functions.id');
            $this->db->join('functions_locale', 'functions_locale.function_id = functions.id');
            $this->db->where('functions_profiles.profile_id',$profileId);
            $this->db->where('functions.enabled',1);
            $this->db->where('functions_locale.locale',$locale);
            $this->db->order_by("functions.n_order");
        }
        else {
            $this->db->select('functions.*,functions_locale.name as name,functions_locale.view_alias_text as view_alias_text');
			$this->db->from('functions');
			$this->db->join('functions_locale', 'functions_locale.function_id = functions.id');
			$this->db->where('functions.enabled',1);
			$this->db->where('functions_locale.locale',$locale);
			$this->db->order_by("functions.n_order");
        }

        $arrData = $this->db->get()->result();

        $this->sendOutput($arrData);
        return;
	}
}
