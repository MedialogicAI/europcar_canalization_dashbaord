<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require("MainController.php");

class ProfilesController extends MainController {

    public function __construct(){
        parent::__construct(true);
    }

	public function getProfilesAll() { // for combo
		if (!$this->isLoggedManager()) {
			$this->sendNotAuthorized();
			return;
		}
		$adminProfileId = $this->getAdminProfileType();
		$this->db->select('*');
		$this->db->from('profiles');
		if (!$this->isLoggedAdmin()) {
			$this->db->where('type <',$adminProfileId);
		}
		$this->db->order_by("type");
		$this->db->order_by("profile");
		$arrData = $this->db->get()->result();
		$this->sendOutput($arrData);

	}

	public function getProfilesList() { // for grid
		if (!$this->isLoggedAdmin()) {
			$this->sendNotAuthorized();
			return;
		}
		$adminProfileId = $this->getAdminProfileType();
    	$this->db->select('*');
		$this->db->from('profiles');
		$this->db->where('type <',$adminProfileId);
		$this->db->order_by("type");
		$this->db->order_by("profile");
		$arrData = $this->db->get()->result();
		$this->sendOutput($arrData);

	}

	public function getFunctions($profileId=0) {
		if (!$this->isLoggedAdmin()) {
			$this->sendNotAuthorized();
			return;
		}
		$profileId = intval($profileId); // left join
		if ($profileId<1) {
			$this->sendZero();
			return;
		}
		$locale = $this->appConfig['defaultLocale'];
		$this->db->select('functions.*,functions_locale.name as name,functions_locale.view_alias_text as view_alias_text');
		$this->db->from('functions');
		$this->db->join('functions_locale', 'functions_locale.function_id = functions.id');
		$this->db->where('functions_locale.locale',$locale);
		$this->db->order_by("functions_locale.name");

		$arrData = $this->db->get()->result();
		foreach ($arrData as &$el) {
			$el->checked = ($this->profileHasFunction($profileId,$el->id)) ? 1 : 0;
		}

		$this->sendOutput($arrData);

	}

	public function addProfile() {
		if (!$this->isLoggedAdmin()) {
			$this->sendNotAuthorized();
			return;
		}
		if (is_null($this->input->post())) {
			$this->sendError("formInvalidData");
			return;
		}
		$type = 0;

		$profile = is_null($this->input->post("profile")) ? "" : $this->input->post("profile");
		if ($profile=="") {
			$this->sendError("formInvalidFields");
			return;
		}

		// check profile name exists
		$this->db->select('id');
		$this->db->from('profiles');
		$this->db->where("profile",$profile);
		$arrData = $this->db->get()->result();
		if (count($arrData)>0) {
			$this->sendError("profileExists");
			return;
		}

		// insert query
		$arrInsert = array(
			'profile' => $profile,
			'type' => $type
		);
		if ($this->db->insert('profiles', $arrInsert)) {
			$this->writeLog("CREATE ROLE");
			$this->sendSuccess("profileCreated");
		}
		else {
			$this->sendError("insertFailed");
		}
		return;
	}

	public function editProfile() {
		if (!$this->isLoggedAdmin()) {
			$this->sendNotAuthorized();
			return;
		}
		if (is_null($this->input->post())) {
			$this->sendError("formInvalidData");
			return;
		}
		$type = 0;

		$id = is_null($this->input->post("id")) ? 0 : $this->input->post("id");
		if ($id<1) {
			$this->sendError("formInvalidFields");
			return;
		}

		$profile = is_null($this->input->post("profile")) ? "" : $this->input->post("profile");
		if ($profile=="") {
			$this->sendError("formInvalidFields");
			return;
		}

		// check profile name exists
		$this->db->select('id');
		$this->db->from('profiles');
		$this->db->where("profile",$profile);
		$this->db->where("id <>",$id);
		$arrData = $this->db->get()->result();
		if (count($arrData)>0) {
			$this->sendError("profileExists");
			return;
		}

		$arrUpdate = array(
			'profile' => $profile
		);
		$this->db->where('id', $id);
		if ($this->db->update('profiles', $arrUpdate)) {
			$this->writeLog("EDIT ROLE");
			$this->sendSuccess("profileUpdated");
		}
		else {
			$this->sendError("updateFailed");
		}

		return;
	}

	public function delProfile($id=null) {
		if (!$this->isLoggedAdmin()) {
			$this->sendNotAuthorized();
			return;
		}
		$id = intval($id);
		if ($id<1) {
			$this->sendError("formInvalidData");
			return;
		}

		// record exists?
		$this->db->select('id,profile,type');
		$this->db->from('profiles');
		$this->db->where('id',$id);
		$arrData = $this->db->get()->result();
		if (count($arrData)<1) {
			$this->sendError("recordNotFound");
			return;
		}
		$profile = $arrData[0]->profile;
		$type = $arrData[0]->type;
		if ($type>0) {
			$this->sendError("profileDeleteSys");
			return;
		}

		// check if there are users with the roles to delete
		$this->db->select('id');
		$this->db->from('users');
		$this->db->where('profile_id',$id);
		$arrData = $this->db->get()->result();
		if (count($arrData)>0) {
			$this->sendError("profileDeleteUsers");
			return;
		}

		// delete record
		$bln = $this->db->delete('profiles', array('id' => $id));
		if ($bln) {
			$this->writeLog("DELETE ROLE: $profile");
			$bln = $this->db->delete('functions_profiles', array('profile_id' => $id));
			$this->sendSuccess("profileDeleted");
		}
		else {
			$this->sendError("deleteFailed");
		}
		return;
	}

	public function enableProfile($functionId,$profileId) {
		if (!$this->isLoggedManager()) {
			$this->sendNotAuthorized();
			return;
		}
		$profileId = intval($profileId);
		$functionId = intval($functionId);
		if ($profileId<1 || $functionId<1) {
			$this->sendError("formInvalidData");
			return;
		}
		// record exists?
		$this->db->select('profile_id');
		$this->db->from('functions_profiles');
		$this->db->where('profile_id',$profileId);
		$this->db->where('function_id',$functionId);
		$arrData = $this->db->get()->result();
		if (count($arrData)>0) {
			$this->sendError("profileExistsAssociation");
			return;
		}
		// insert query
		$arrInsert = array(
			'profile_id' => $profileId,
			'function_id' => $functionId
		);
		if ($this->db->insert('functions_profiles', $arrInsert)) {
			$this->writeLog("ENABLE ROLE/FUNCTION");
			$this->sendSuccess("operationCompleted");
		}
		else {
			$this->sendError("operationFailed");
		}
		return;
	}

	public function disableProfile($functionId,$profileId) {
		if (!$this->isLoggedManager()) {
			$this->sendNotAuthorized();
			return;
		}
		$profileId = intval($profileId);
		$functionId = intval($functionId);
		if ($profileId<1 || $functionId<1) {
			$this->sendError("formInvalidData");
			return;
		}
		// record exists?
		$this->db->select('profile_id');
		$this->db->from('functions_profiles');
		$this->db->where('profile_id',$profileId);
		$this->db->where('function_id',$functionId);
		$arrData = $this->db->get()->result();
		if (count($arrData)<1) {
			$this->sendError("formInvalidData");
			return;
		}
		// delete query
		$arrDelete = array(
			'profile_id' => $profileId,
			'function_id' => $functionId
		);
		$bln = $this->db->delete('functions_profiles', $arrDelete);
		if ($bln) {
			$this->writeLog("DISABLE ROLE/FUNCTION");
			$this->sendSuccess("operationCompleted");
		}
		else {
			$this->sendError("operationFailed");
		}
		return;
	}

	private function profileHasFunction($profileId,$functionId) {
    	$this->db->select('function_id');
		$this->db->from('functions_profiles');
		$this->db->where('profile_id',$profileId);
		$this->db->where('function_id',$functionId);
		$arrData = $this->db->get()->result();
		return (count($arrData)>0) ? true : false;
	}

}
