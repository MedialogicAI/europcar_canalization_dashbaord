<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require("MainController.php");

class DashboardController extends MainController {

	public function __construct(){
		$this->startDate = date("Y")."-01-01";
		parent::__construct(true);
	}

	// start date for the current year
	private $startDate = "2000-01-01";

	// ################################### DAILY ###################################

	/*
	 * JSON PARAMS: 2 records
	 * name: string, processed/notProcessed, match language view model, ie:[0]['name'] = "processed",[1]['name'] = "notProcessed"
	 * value: int, default: 0, the numeric value for the record
	 * total: int, default: 0, total value, the same for all records
	 */
	public function getDailyTotal() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}

		$totProcessed = $totNotProcessed = $totProcessing = 0;
		$processedManually = $processedAutomatic = $processedRejected = 0;
		$processedIrmManually = $processedIrmAutomatic = $processedIrmRejected = 0;

		// filter for user stations
		$conditionStations = "";
		$arrStationsUser = $this->getStationsUser();
		if (!$this->isLoggedAdmin() && count($arrStationsUser)<1) {
			$this->sendZero();
			return;
		}
		if (!$this->isLoggedManager()) {
			$conditionStations = $this->buildSqlConditions($arrStationsUser,"station_code","ataraxia_main.codice_stazione");
		}
		// get total pratices
		$this->db->select('ataraxia_main.id');
		$this->db->from('ataraxia_main');
		$this->db->join('tab_status', 'ataraxia_main.status = tab_status.actual_status','left');
		if ($conditionStations!=="") {
			$this->db->where($conditionStations);
		}
		$this->db->where("ataraxia_main.start_processing BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d') AND NOW()");
		$totalPratices = $this->db->count_all_results();

		if ($totalPratices>0) {
			// ### PRATICES
			// get automatic addressed
			$processedAutomatic += $this->getRowsByStatus(400);
			$processedAutomatic += $this->getRowsByStatus(401);
			$processedAutomatic += $this->getRowsByStatus(402);
			$processedAutomatic += $this->getRowsByStatus(403);

			// get manually
			$processedManually += $this->getRowsByStatus(490);
			$processedManually += $this->getRowsByStatus(540);
			$processedManually += $this->getRowsByStatus(450);
			$processedManually += $this->getRowsByStatus(570);
			$processedManually += $this->getRowsByStatus(600);
			$processedManually += $this->getRowsByStatus(405);
			$processedManually += $this->getRowsByStatus(560);
			$processedManually += $this->getRowsByStatus(480);
			// get rejected
			$processedRejected += $this->getRowsByStatus(410);
			$processedRejected += $this->getRowsByStatus(440);
			$processedRejected += $this->getRowsByStatus(460);
			$processedRejected += $this->getRowsByStatus(430);
			$processedRejected += $this->getRowsByStatus(420);

			$totProcessed = $processedAutomatic + $processedManually + $processedRejected;
			$totNotProcessed = $this->getNotProcessed(false);
			$totProcessing = $this->getProcessing(false);
			$totalPratices = $totProcessed + $totNotProcessed + $totProcessing;
		}

		$arrData = array();
		$arrData[0] = Array(
			"name" => "processed",
			"value" => $totProcessed,
			"total" => $totalPratices
		);
		$arrData[1] = Array(
			"name" => "processing",
			"value" => $totProcessing,
			"total" => $totalPratices
		);
		$arrData[2] = Array(
			"name" => "notProcessed",
			"value" => $totNotProcessed,
			"total" => $totalPratices
		);
		$this->sendOutput($arrData);
	}

	/*
     * JSON PARAMS: 1 record
     * name: string, blank space " ", y-axes setted by the view
     * automated: int, default: 0, the numeric value for the record
     * manually: int, default: 0, the numeric value for the record
     * rejected: int, default: 0, the numeric value for the record
     * total: int, default: 0, total value, the same for all records
     */
	public function getDailyPratices() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$total = $processedAutomatic = $notProcessedAutomatic = 0;
		$processedManually = $notProcessedManually = $processedRejected = $notProcessedRejected = 0;

		// get automatic
		$processedAutomatic += $this->getRowsByStatus(400);
		$processedAutomatic += $this->getRowsByStatus(401);
		$processedAutomatic += $this->getRowsByStatus(402);
		$processedAutomatic += $this->getRowsByStatus(403);

		// get manually
		$processedManually += $this->getRowsByStatus(490);
		$processedManually += $this->getRowsByStatus(540);
		$processedManually += $this->getRowsByStatus(450);
		$processedManually += $this->getRowsByStatus(570);
		$processedManually += $this->getRowsByStatus(600);
		$processedManually += $this->getRowsByStatus(405);
		$processedManually += $this->getRowsByStatus(560);
		$processedManually += $this->getRowsByStatus(480);

		// get rejected
		$processedRejected += $this->getRowsByStatus(410);
		$processedRejected += $this->getRowsByStatus(440);
		$processedRejected += $this->getRowsByStatus(460);
		$processedRejected += $this->getRowsByStatus(430);
		$processedRejected += $this->getRowsByStatus(420);

		$total = $processedAutomatic + $processedManually + $processedRejected;
		$arrData = array();
		$arrData[0] = Array(
			"name" => " ",
			"automated" => $processedAutomatic,
			"manually" => $processedManually,
			"rejected" => $processedRejected,
			"total" => $total
		);
		$this->sendOutput($arrData);
	}

	/*
     * JSON PARAMS: 1 record
     * name: string, blank space " ", y-axes setted by the view
     * automated: int, default: 0, the numeric value for the record
     * manually: int, default: 0, the numeric value for the record
     * rejected: int, default: 0, the numeric value for the record
     * total: int, default: 0, total value, the same for all records
     */
	public function getDailyIrm() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$totalIrm = $automaticIrm = $manuallyIrm = $rejectedIrm = 0;
		// filter for user stations
		$conditionStations = "";
		$arrStationsUser = $this->getStationsUser();
		if (!$this->isLoggedAdmin() && count($arrStationsUser)<1) {
			$this->sendZero();
			return;
		}
		if (!$this->isLoggedManager()) {
			$conditionStations = $this->buildSqlConditions($arrStationsUser,"station_code","codice_stazione");
		}

		// get automatic
		$this->db->select("id");
		$this->db->from("ataraxia_main");
		if ($conditionStations!=="") {
			$this->db->where($conditionStations);
		}
		$this->db->where("irm_status",5);
		$this->db->where("start_processing BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d') AND NOW()");
		$automaticIrm = $this->db->count_all_results();
		// get manually
		$this->db->select("id");
		$this->db->from("ataraxia_main");
		if ($conditionStations!=="") {
			$this->db->where($conditionStations);
		}
		$this->db->where_in("irm_status",array(6,7));
		$this->db->where("start_processing BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d') AND NOW()");
		$manuallyIrm = $this->db->count_all_results();
		// get rejected
		$this->db->select("id");
		$this->db->from("ataraxia_main");
		$this->db->like("wsm_bsm_status","Reminder Sent");
		if ($conditionStations!=="") {
			$this->db->where($conditionStations);
		}
		$this->db->where("status >=",400);
		$this->db->where("start_processing BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d') AND NOW()");
		$rejectedIrm = $this->db->count_all_results();
		// total
		$totalIrm = $automaticIrm + $manuallyIrm + $rejectedIrm;

		$arrData = array();
		$arrData[0] = Array(
			"name" => " ",
			"automated" => $automaticIrm,
			"manually" => $manuallyIrm,
			"rejected" => $rejectedIrm,
			"total" => $totalIrm
		);
		$this->sendOutput($arrData);
	}
	//------------------------------------------------------------------------------------------------------------------

	// ################################### YEAR ###################################

	/*
	 * JSON PARAMS: 2 records
	 * name: string, processed/notProcessed, match language view model, ie:[0]['name'] = "processed",[1]['name'] = "notProcessed"
	 * value: int, default: 0, the numeric value for the record
	 * total: int, default: 0, total value, the same for all records
	 */
	public function getYearTotal() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}

		$totProcessed = $totNotProcessed = $totProcessing = 0;
		$processedManually = $processedAutomatic = $processedRejected = 0;
		$processedIrmManually = $processedIrmAutomatic = $processedIrmRejected = 0;

		// filter for user stations
		$conditionStations = "";
		$arrStationsUser = $this->getStationsUser();
		if (!$this->isLoggedAdmin() && count($arrStationsUser)<1) {
			$this->sendZero();
			return;
		}
		if (!$this->isLoggedManager()) {
			$conditionStations = $this->buildSqlConditions($arrStationsUser,"station_code","ataraxia_main.codice_stazione");
		}

		// get total pratices
		$this->db->select('ataraxia_main.id');
		$this->db->from('ataraxia_main');
		$this->db->join('tab_status', 'ataraxia_main.status = tab_status.actual_status','left');
		if ($conditionStations!=="") {
			$this->db->where($conditionStations);
		}
		$this->db->where("ataraxia_main.start_processing >=",$this->startDate);
		$totalPratices = $this->db->count_all_results();

		if ($totalPratices>0) {
			// ### PRATICES
			// get automatic addressed
			$processedAutomatic += $this->getRowsByStatus(400,true);
			$processedAutomatic += $this->getRowsByStatus(401,true);
			$processedAutomatic += $this->getRowsByStatus(402,true);
			$processedAutomatic += $this->getRowsByStatus(403,true);
			// get manually
			$processedManually += $this->getRowsByStatus(490,true);
			$processedManually += $this->getRowsByStatus(540,true);
			$processedManually += $this->getRowsByStatus(450,true);
			$processedManually += $this->getRowsByStatus(570,true);
			$processedManually += $this->getRowsByStatus(600,true);
			$processedManually += $this->getRowsByStatus(405,true);
			$processedManually += $this->getRowsByStatus(560,true);
			$processedManually += $this->getRowsByStatus(480,true);
			// get rejected
			$processedRejected += $this->getRowsByStatus(410,true);
			$processedRejected += $this->getRowsByStatus(440,true);
			$processedRejected += $this->getRowsByStatus(460,true);
			$processedRejected += $this->getRowsByStatus(430,true);
			$processedRejected += $this->getRowsByStatus(420,true);

			$totProcessed = $processedAutomatic + $processedManually + $processedRejected;
			$totNotProcessed = $this->getNotProcessed(true);
			$totProcessing = $this->getProcessing(true);
			$totalPratices = $totProcessed + $totNotProcessed + $totProcessing;
		}

		$arrData = array();
		$arrData[0] = Array(
			"name" => "processed",
			"value" => $totProcessed,
			"total" => $totalPratices
		);
		$arrData[1] = Array(
			"name" => "processing",
			"value" => $totProcessing,
			"total" => $totalPratices
		);
		$arrData[2] = Array(
			"name" => "notProcessed",
			"value" => $totNotProcessed,
			"total" => $totalPratices
		);
		$this->sendOutput($arrData);
	}

	/*
     * JSON PARAMS: 1 record
     * name: string, blank space " ", y-axes setted by the view
     * automated: int, default: 0, the numeric value for the record
     * manually: int, default: 0, the numeric value for the record
     * rejected: int, default: 0, the numeric value for the record
     * total: int, default: 0, total value, the same for all records
     */
	public function getYearPratices() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$total = $processedAutomatic = $notProcessedAutomatic = 0;
		$processedManually = $notProcessedManually = $processedRejected = $notProcessedRejected = 0;

		// get automated
		$processedAutomatic += $this->getRowsByStatus(400,true);
		$processedAutomatic += $this->getRowsByStatus(401,true);
		$processedAutomatic += $this->getRowsByStatus(402,true);
		$processedAutomatic += $this->getRowsByStatus(403,true);
		// get manually
		$processedManually += $this->getRowsByStatus(490,true);
		$processedManually += $this->getRowsByStatus(540,true);
		$processedManually += $this->getRowsByStatus(450,true);
		$processedManually += $this->getRowsByStatus(570,true);
		$processedManually += $this->getRowsByStatus(600,true);
		$processedManually += $this->getRowsByStatus(405,true);
		$processedManually += $this->getRowsByStatus(560,true);
		$processedManually += $this->getRowsByStatus(480,true);
		// get rejected
		$processedRejected += $this->getRowsByStatus(410,true);
		$processedRejected += $this->getRowsByStatus(440,true);
		$processedRejected += $this->getRowsByStatus(460,true);
		$processedRejected += $this->getRowsByStatus(430,true);
		$processedRejected += $this->getRowsByStatus(420,true);

		$total = $processedAutomatic + $processedManually + $processedRejected;
		$arrData = array();
		$arrData[0] = Array(
			"name" => " ",
			"automated" => $processedAutomatic,
			"manually" => $processedManually,
			"rejected" => $processedRejected,
			"total" => $total
		);
		$this->sendOutput($arrData);
	}

	/*
     * JSON PARAMS: 1 record
     * name: string, blank space " ", y-axes setted by the view
     * automated: int, default: 0, the numeric value for the record
     * manually: int, default: 0, the numeric value for the record
     * rejected: int, default: 0, the numeric value for the record
     * total: int, default: 0, total value, the same for all records
     */
	public function getYearIrm() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$totalIrm = $automaticIrm = $manuallyIrm = $rejectedIrm = 0;
		// filter for user stations
		$conditionStations = "";
		$arrStationsUser = $this->getStationsUser();
		if (!$this->isLoggedAdmin() && count($arrStationsUser)<1) {
			$this->sendZero();
			return;
		}
		if (!$this->isLoggedManager()) {
			$conditionStations = $this->buildSqlConditions($arrStationsUser,"station_code","codice_stazione");
		}

		// get automatic
		$this->db->select("id");
		$this->db->from("ataraxia_main");
		if ($conditionStations!=="") {
			$this->db->where($conditionStations);
		}
		$this->db->where("irm_status",5);
		$this->db->where("start_processing >=",$this->startDate);
		$automaticIrm = $this->db->count_all_results();
		// get manually
		$this->db->select("id");
		$this->db->from("ataraxia_main");
		if ($conditionStations!=="") {
			$this->db->where($conditionStations);
		}
		$this->db->where_in("irm_status",array(6,7));
		$this->db->where("start_processing >=",$this->startDate);
		$manuallyIrm = $this->db->count_all_results();
		// get rejected
		$this->db->select("id");
		$this->db->from("ataraxia_main");
		if ($conditionStations!=="") {
			$this->db->where($conditionStations);
		}
		$this->db->like("wsm_bsm_status","Reminder Sent");
		$this->db->where("status >=",400);
		$this->db->where("start_processing >=",$this->startDate);
		$rejectedIrm = $this->db->count_all_results();
		// total
		$totalIrm = $automaticIrm + $manuallyIrm + $rejectedIrm;

		$arrData = array();
		$arrData[0] = Array(
			"name" => " ",
			"automated" => $automaticIrm,
			"manually" => $manuallyIrm,
			"rejected" => $rejectedIrm,
			"total" => $totalIrm
		);
		$this->sendOutput($arrData);
	}
	//------------------------------------------------------------------------------------------------------------------


	private function getRowsByStatus($status=null,$blnAll=false) {
		if (empty($status)) {
			return 0;
		}
		// filter for user stations
		$conditionStations = "";
		$arrStationsUser = $this->getStationsUser();
		if (!$this->isLoggedAdmin() && count($arrStationsUser)<1) {
			$this->sendZero();
			return;
		}
		if (!$this->isLoggedManager()) {
			$conditionStations = $this->buildSqlConditions($arrStationsUser,"station_code","ataraxia_main.codice_stazione");
		}
		$status = intval($status);
		$this->db->select("ataraxia_main.id");
		$this->db->from("ataraxia_main");
		$this->db->join("tab_status", "ataraxia_main.status = tab_status.actual_status");
		if ($conditionStations!=="") {
			$this->db->where($conditionStations);
		}
		$this->db->where("status",$status);
		if ($blnAll) {
			$this->db->where("start_processing >=",$this->startDate);
		}
		else {
			$this->db->where("start_processing BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d') AND NOW()");
		}
		return $this->db->count_all_results();
	}

	private function getNotProcessed($blnAll=false) {
		// filter for user stations
		$conditionStations = "";
		$arrStationsUser = $this->getStationsUser();
		if (!$this->isLoggedAdmin() && count($arrStationsUser)<1) {
			$this->sendZero();
			return;
		}
		if (!$this->isLoggedManager()) {
			$conditionStations = $this->buildSqlConditions($arrStationsUser,"station_code","ataraxia_main.codice_stazione");
		}
		$this->db->select("ataraxia_main.id");
		$this->db->from("ataraxia_main");
		if ($conditionStations!=="") {
			$this->db->where($conditionStations);
		}
		$this->db->where("status",0);
		$this->db->where("start_processing",NULL);
		$this->db->where("end_processing",NULL);
		if ($blnAll) {
			$this->db->where("dt_insert >=",$this->startDate);
		}
		else {
			$this->db->where("dt_insert BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d') AND NOW()");
		}
		return $this->db->count_all_results();
	}

	private function getProcessing($blnAll=false) {
		// filter for user stations
		$conditionStations = "";
		$arrStationsUser = $this->getStationsUser();
		if (!$this->isLoggedAdmin() && count($arrStationsUser)<1) {
			$this->sendZero();
			return;
		}
		if (!$this->isLoggedManager()) {
			$conditionStations = $this->buildSqlConditions($arrStationsUser,"station_code","ataraxia_main.codice_stazione");
		}
		$this->db->select("ataraxia_main.id");
		$this->db->from("ataraxia_main");
		if ($conditionStations!=="") {
			$this->db->where($conditionStations);
		}
		$this->db->where("status",1);
		$this->db->where("end_processing",NULL);
		$this->db->where("start_processing IS NOT NULL");
		if ($blnAll) {
			$this->db->where("start_processing >=",$this->startDate);
		}
		else {
			$this->db->where("start_processing BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d') AND NOW()");
		}
		return $this->db->count_all_results();
	}

}
