<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require("MainController.php");

class RobotController extends MainController {

	public function __construct(){
		parent::__construct(true);
	}

	// ################################### ROBOT ###################################

	// GET CURRENT ROBOT STATUS
	// AND REQUESTED ACTION BUTTON STATUS
	public function getRobotStatus() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$status = -1;
		$arrRefresh = Array(
			"canalization" => 0,
			"capacity" => 0,
			"bbfile" => 0,
			"movement" => 0,
			"flotta" => 0,
		);

		$this->db->select('*');
		$this->db->from('refresh_file');
		$arrTemp = $this->db->get()->result();
		if (count($arrTemp)>0) {
			$arrRefresh = Array(
				"canalization" => $arrTemp[0]->canaliz_file,
				"capacity" => $arrTemp[0]->capacity_file,
				"bbfile" => $arrTemp[0]->listabb_file,
				"movement" => $arrTemp[0]->movement_file,
				"flotta" => $arrTemp[0]->flotta_file
			);
		}


		$this->db->select('flagstopalltask');
		$this->db->from('stop-extremis');
		$this->db->where('idstop',1);
		$arrData = $this->db->get()->result();
		$db_status = intval($arrData[0]->flagstopalltask);
		if (count($arrData)>0 && ($db_status===1 || $db_status===0)) {
			$status = ($db_status==0) ? 1 : 0; // invert value, the view manages if robot is active (1) or not (0)
		}
		$arrData = Array(
			Array("status" => $status, "refresh" => $arrRefresh)
		);
		$this->sendOutput($arrData);
		return;
	}


	// ################################### ACTIONS ###################################

	// POWER ON ROBOT
	public function robotPowerOn() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}

		$arrUpdate = array(
			'flagstopalltask' => 0
		);
		$this->db->where('idstop',1);
		if ($this->db->update('stop-extremis', $arrUpdate)) {
			$this->writeLog("ROBOT POWER ON");
			$this->sendSuccess("robotPoweredOn");
		}
		else {
			$this->sendError();
		}

		return;
	}

	// POWER OFF ROBOT
	public function robotPowerOff() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}

		$arrUpdate = array(
			'flagstopalltask' => 1
		);
		$this->db->where('idstop',1);
		if ($this->db->update('stop-extremis', $arrUpdate)) {
			$this->writeLog("ROBOT POWER OFF");
			$this->sendSuccess("robotPoweredOff");
		}
		else {
			$this->sendError();
		}

		return;
	}

	// EXEC ACTION
	public function doAction($field=null) {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		if (empty($field)) {
			$this->sendError();
			return;
		}
		$arrTabField = Array(
			"canalization" => "canaliz_file",
			"bbfile" => "listabb_file",
			"capacity" => "capacity_file",
			"flotta" => "flotta_file",
			"movement" => "movement_file"
		);
		$field = $arrTabField[$field];

		$this->db->select($field);
		$this->db->from('refresh_file');
		$this->db->where($field,1);
		if ($this->db->count_all_results()>0) {
			$this->sendError();
			return;
		}

		$arrUpdate = array(
			$field => 1
		);
		if ($this->db->update('refresh_file', $arrUpdate)) {
			$sql =
				$this->writeLog("REFRESH ACTION - field: $field");
			$this->sendSuccess();
		}
		else {
			$this->sendError();
		}
		return;
	}

}
