<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require("MainController.php");

class PraticesController extends MainController {

    public function __construct(){
    	// set date intervals
		$today = date("Y-m-d");
    	$todayHour = date("Y-m-d H:i:s");
    	$year = date("Y");

		$this->nowDate = Array(
			"date" => "$today",
			"date_hour" => $todayHour,
			"timestamp" => strtotime($todayHour)
		);

		$this->startDayDate = Array(
			"date" => "$today",
			"date_hour" => "$today 00:00:00",
			"timestamp" => strtotime("$today 00:00:00")
		);
		$this->endDayDate = Array(
			"date" => "$today",
			"date_hour" => "$today 23:59:59",
			"timestamp" => strtotime("$today 23:59:59")
		);
		$this->startYearDate = Array(
			"date" => "$year",
			"date_hour" => "$year-01-01 00:00:00",
			"timestamp" => strtotime("$year-01-01 00:00:00")
		);
		$this->endYearDate = Array(
			"date" => "$year",
			"date_hour" => "$year-12-31 23:59:59",
			"timestamp" => strtotime("$year-12-31 23:59:59")
		);
		// start date by moths ago
		$iMonthsAgo = 6; // how months
		$monthsAgo = date("Y-m-d", strtotime("-$iMonthsAgo months"));
		$this->startMonthsAgoDate = Array(
			"date" => "$monthsAgo",
			"date_hour" => "$monthsAgo 00:00:00",
			"timestamp" => strtotime("$monthsAgo 00:00:00")
		);
		// start date last 1 hour
		$hours = 1;
		$lastHour = date('Y-m-d H:i:s', strtotime("-$hours hour"));
		$lastDateHour = date('Y-m-d', strtotime("-$hours hour"));
		$this->startLast1HourDate = Array(
			"date" => $lastDateHour,
			"date_hour" => $lastHour,
			"timestamp" => strtotime($lastHour)
		);
		// start date last 12 hours
		$hours = 12;
		$lastHour = date('Y-m-d H:i:s', strtotime("-$hours hour"));
		$lastDateHour = date('Y-m-d', strtotime("-$hours hour"));
		$this->startLast12HourDate = Array(
			"date" => $lastDateHour,
			"date_hour" => $lastHour,
			"timestamp" => strtotime($lastHour)
		);

		parent::__construct(true);
    }

	// start date for the current day
	private $nowDate = Array();
	private $startDayDate = Array();
	private $endDayDate =  Array();

	// start date for the current year
	private $startYearDate = Array();
	private $endYearDate =  Array();

	// date starts by months ago
	private $startMonthsAgoDate = Array(); // start date for the practices, ie: 6 months ago

	// last 1 hour
	private $startLast1HourDate = Array(); // last 1 h

	// last 12 hours
	private $startLast12HourDate = Array(); // last 12 h

	// ################################### GLOBAL PRATICES ###################################

	/*
	 * PARAMS:
	 * description: Move pratice to a Manually managed status
	 * id: int, pratice id
	 */
	public function setManually($id=null) {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		if (empty($id)) {
			$this->sendError("updateFailed");
			return;
		}
		$id = intval($id);
		$now = date("Y-m-d H:i:s");

		// check if status is 0 for concurrency
		$this->db->select("carico");
		$this->db->from("ataraxia_main");
		$this->db->where("id", $id);
		$this->db->where("status <>", 0);

		if ($this->db->count_all_results()>0) {
			$this->sendError("praticesStatusChangedErr");
			return;
		}

		$arrUpdate = array(
			"status" => 560,
			"start_processing" => $now,
			"end_processing" => $now,
			"observations" => "Processed Manually by the operator"
		);
		$this->db->where('id', $id);
		if ($this->db->update('ataraxia_main', $arrUpdate)) {
			$this->writeLog("PRATICE ID $id moved in manually managed");
			$this->sendSuccess("operationCompleted");
		}
		else {
			$this->sendError("updateFailed");
		}
		return;
	}

    // ################################### DAILY ###################################

    /*
     * LIST TODAY PRATICES
     */
	public function getDailyList() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}

		// POST DATA FOR PAGING STATION
		$start = intval($this->input->post("start"));
		$limit = intval($this->input->post("limit"));

		// serach params
		$post = $this->input->post("searchtext");
		$searchext = (!empty($post)) ? $post : '';
		$datestart = $this->startDayDate['date_hour'];
		$dateend = $this->endDayDate['date_hour'];
		// select all records, 1 page
		$all = intval($this->input->post("all"));
		// search cfg
		$searchParams = Array(
			"start" => $start,
			"limit" => $limit,
			"searchtext" => $searchext,
			"ts_in_dt_start" => $datestart,
			"ts_in_dt_end" => $dateend,
			"all" => $all,
			"status" => Array()
		);
		// list
		$arrResultSearch = $this->praticesSearch($searchParams);
		if ($arrResultSearch['totalCount']>0) {
			$this->sendOutput($arrResultSearch['records'],$arrResultSearch['totalCount']);
		}
		else {
			$this->sendZero();
		}
		return;
	}

	// ################################### PRATICE ARCHIVE ###################################

    /*
     * LIST ALL PRATICES
     */
	public function getArchiveAll() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}

		// POST DATA FOR LIST SEARCH
		$start = intval($this->input->post("start"));
		$limit = intval($this->input->post("limit"));
		// serach params
		$post = $this->input->post("searchtext");
		$searchext = (!empty($post)) ? $post : '';
		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : '';
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		// select all records, 1 page
		$all = intval($this->input->post("all"));
		// search cfg
		$searchParams = Array(
			"start" => $start,
			"limit" => $limit,
			"searchtext" => $searchext,
			"datestart" => $datestart,
			"dateend" => $dateend,
			"all" => $all
		);
		// list
		$arrResultSearch = $this->praticesSearch($searchParams);
		if ($arrResultSearch['totalCount']>0) {
			$this->sendOutput($arrResultSearch['records'],$arrResultSearch['totalCount']);
		}
		else {
			$this->sendZero();
		}
		return;
	}

	/*
     * LIST AUTOMATED PRATICES
     */
	public function getArchiveAutomated() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}

		// POST DATA FOR LIST SEARCH
		$start = intval($this->input->post("start"));
		$limit = intval($this->input->post("limit"));
		// serach params
		$post = $this->input->post("searchtext");
		$searchext = (!empty($post)) ? $post : '';
		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : '';
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		// select all records, 1 page
		$all = intval($this->input->post("all"));
		// search cfg
		$searchParams = Array(
			"start" => $start,
			"limit" => $limit,
			"searchtext" => $searchext,
			"datestart" => $datestart,
			"dateend" => $dateend,
			"all" => $all,
			"status" => Array(400,401,402,403)
		);
		// list
		$arrResultSearch = $this->praticesSearch($searchParams);
		if ($arrResultSearch['totalCount']>0) {
			$this->sendOutput($arrResultSearch['records'],$arrResultSearch['totalCount']);
		}
		else {
			$this->sendZero();
		}
		return;
	}

	/*
     * LIST MANUALLY PRATICES
     */
	public function getArchiveManually() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}

		// POST DATA FOR LIST SEARCH
		$start = intval($this->input->post("start"));
		$limit = intval($this->input->post("limit"));
		// serach params
		$post = $this->input->post("searchtext");
		$searchext = (!empty($post)) ? $post : '';
		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : '';
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		// select all records, 1 page
		$all = intval($this->input->post("all"));
		// search cfg
		$searchParams = Array(
			"start" => $start,
			"limit" => $limit,
			"searchtext" => $searchext,
			"datestart" => $datestart,
			"dateend" => $dateend,
			"all" => $all,
			"status" => Array(490,540,450,570,600,405,560,480)
		);
		// list
		$arrResultSearch = $this->praticesSearch($searchParams);
		if ($arrResultSearch['totalCount']>0) {
			$this->sendOutput($arrResultSearch['records'],$arrResultSearch['totalCount']);
		}
		else {
			$this->sendZero();
		}
		return;
	}

	/*
     * LIST REJECTED PRATICES
     */
	public function getArchiveRejected() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}

		// POST DATA FOR LIST SEARCH
		$start = intval($this->input->post("start"));
		$limit = intval($this->input->post("limit"));
		// serach params
		$post = $this->input->post("searchtext");
		$searchext = (!empty($post)) ? $post : '';
		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : '';
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		// select all records, 1 page
		$all = intval($this->input->post("all"));
		// search cfg
		$searchParams = Array(
			"start" => $start,
			"limit" => $limit,
			"searchtext" => $searchext,
			"datestart" => $datestart,
			"dateend" => $dateend,
			"all" => $all,
			"status" => Array(410,440,460,430,420)
		);
		// list
		$arrResultSearch = $this->praticesSearch($searchParams);
		if ($arrResultSearch['totalCount']>0) {
			$this->sendOutput($arrResultSearch['records'],$arrResultSearch['totalCount']);
		}
		else {
			$this->sendZero();
		}
		return;
	}

	// ################################### PICKED / NOT PICKED / WAITING ###################################

	/*
     * LIST PICKED
     */
	public function getPicked() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}

		// POST DATA FOR LIST SEARCH
		$start = intval($this->input->post("start"));
		$limit = intval($this->input->post("limit"));
		// serach params
		$post = $this->input->post("searchtext");
		$searchext = (!empty($post)) ? $post : '';
		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : '';
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		// select all records, 1 page
		$all = intval($this->input->post("all"));
		// search cfg
		$searchParams = Array(
			"start" => $start,
			"limit" => $limit,
			"searchtext" => $searchext,
			"datestart" => $datestart,
			"dateend" => $dateend,
			"all" => $all,
			"status" => Array(402)
		);
		// list
		$arrResultSearch = $this->praticesSearch($searchParams);
		if ($arrResultSearch['totalCount']>0) {
			$this->sendOutput($arrResultSearch['records'],$arrResultSearch['totalCount']);
		}
		else {
			$this->sendZero();
		}
		return;
	}

	/*
     * LIST NOT PICKED
     */
	public function getNotPicked() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}

		// POST DATA FOR LIST SEARCH
		$start = intval($this->input->post("start"));
		$limit = intval($this->input->post("limit"));
		// serach params
		$post = $this->input->post("searchtext");
		$searchext = (!empty($post)) ? $post : '';
		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : '';
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		// select all records, 1 page
		$all = intval($this->input->post("all"));

		$searchParams = Array(
			"start" => $start,
			"limit" => $limit,
			"searchtext" => $searchext,
			"datestart" => $datestart,
			"dateend" => $dateend,
			"all" => $all,
			"status" => Array(401)
		);

		// list
		$arrResultSearch = $this->praticesSearch($searchParams);
		if ($arrResultSearch['totalCount']>0) {
			$this->sendOutput($arrResultSearch['records'],$arrResultSearch['totalCount']);
		}
		else {
			$this->sendZero();
		}
		return;
	}

	/*
     * LIST WAITING
     */
	public function getWaiting() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}

		// POST DATA FOR LIST SEARCH
		$start = intval($this->input->post("start"));
		$limit = intval($this->input->post("limit"));
		// serach params
		$post = $this->input->post("searchtext");
		$searchext = (!empty($post)) ? $post : '';
		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : '';
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		// select all records, 1 page
		$all = intval($this->input->post("all"));

		// search cfg
		$searchParams = Array(
			"start" => $start,
			"limit" => $limit,
			"searchtext" => $searchext,
			"datestart" => $datestart,
			"dateend" => $dateend,
			"all" => $all,
			"status" => Array(400),
			"wsm_bsm_dt_start" => $this->startLast1HourDate["date_hour"],
			"end_processing" => $this->startLast12HourDate["date_hour"],
			"is_waiting" => true
		);
		// list
		$arrResultSearch = $this->praticesSearch($searchParams);
		if ($arrResultSearch['totalCount']>0) {
			$this->sendOutput($arrResultSearch['records'],$arrResultSearch['totalCount']);
		}
		else {
			$this->sendZero();
		}
		return;
	}



	// ################################### CONTROLLER PRIVATE METHODS & UTILS ###################################

	/*
     * PRATICES GLOBAL SEARCH
	 * searchParams: Config Array
     */
	private function praticesSearch($searchParams=Array()) {
		if (empty($searchParams)) {
			return Array();
		}

		$start = $searchParams['start'];
		$limit = $searchParams['limit'];

		// search params
		$searchext = (!empty($searchParams['searchtext'])) ? $searchParams['searchtext'] : '';
		$datestart = (!empty($searchParams['datestart'])) ? $searchParams['datestart']." 00:00:00" : $this->startMonthsAgoDate["date_hour"];
		$dateend = (!empty($searchParams['dateend'])) ? $searchParams['dateend']." 23:59:59" : '';

		$start_processing = (!empty($searchParams['start_processing'])) ? $searchParams['start_processing'] : '';
		$end_processing = (!empty($searchParams['end_processing'])) ? $searchParams['end_processing'] : '';

		$wsm_bsm_dt_start = (!empty($searchParams['wsm_bsm_dt_start'])) ? $searchParams['wsm_bsm_dt_start'] : '';
		$wsm_bsm_dt_end = (!empty($searchParams['wsm_bsm_dt_end'])) ? $searchParams['wsm_bsm_dt_end'] : '';
		
		$ts_in_dt_start = (!empty($searchParams['ts_in_dt_start'])) ? $searchParams['ts_in_dt_start'] : '';
		$ts_in_dt_end = (!empty($searchParams['ts_in_dt_end'])) ? $searchParams['ts_in_dt_end'] : '';

		$blnSelectAll = ($searchParams['all']===1) ? true : false;
		$blnFilterStatus = (array_key_exists('status',$searchParams) && is_array($searchParams['status']) && count($searchParams['status'])>0) ? true : false;
		$arrStatus = ($blnFilterStatus) ? $searchParams['status'] : Array();
		$blnFilterIsWating = (array_key_exists('is_waiting',$searchParams) && $searchParams['is_waiting']) ? true : false;

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

		// build query
		$arrFields = Array(
			Array("fieldType" => "int", "fieldName" => "ataraxia_main.id", "function" => null, "textsearch" => false),
			Array("fieldType" => "string", "fieldName" => "codice_stazione", "function" => "UPPER", "textsearch" => true),
			Array("fieldType" => "date", "fieldName" => "ts_in_dt", "function" => null, "textsearch" => false),
			Array("fieldType" => "date", "fieldName" => "start_processing", "function" => null, "textsearch" => false),
			Array("fieldType" => "date", "fieldName" => "end_processing", "function" => null, "textsearch" => false),
			Array("fieldType" => "string", "fieldName" => "targa_veicolo", "function" => "UPPER", "textsearch" => true),
			Array("fieldType" => "string", "fieldName" => "observations", "function" => "TRIM", "textsearch" => true),
			Array("fieldType" => "string", "fieldName" => "atar_file", "function" => null, "textsearch" => true),
			Array("fieldType" => "int", "fieldName" => "status", "function" => null, "textsearch" => false),
			Array("fieldType" => "string", "fieldName" => "defect_code", "function" => "TRIM", "textsearch" => true),
			Array("fieldType" => "string", "fieldName" => "flottatab.ACTIVITY", "function" => null, "textsearch" => false),
			Array("fieldType" => "string", "fieldName" => "autofficina_name", "function" => "TRIM", "textsearch" => true),
			Array("fieldType" => "string", "fieldName" => "email_inviata_autofficina", "function" => "TRIM", "textsearch" => true)
		);

		// get total for paging
		$selectFieldsTotal = $this->buildSelectFields($arrFields,false);
		$this->db->select("ataraxia_main.id");
		$this->db->from("ataraxia_main");
		$this->db->join("flottatab", "ataraxia_main.targa_veicolo = flottatab.REGISTRATION_NUMBER", "left");

		// status filter
		if ($blnFilterStatus) {
			$blnFirst = true;
			$v = "";
			$s = "";
			foreach ($arrStatus as $el) {
				$s .= $v."status=$el";
				if ($blnFirst) {
					$blnFirst = false;
					$v = ' OR ';
				}
			}
			$cond = "($s)";
			$this->db->where($cond);
		}

		// date filters dt_insert
		if ($datestart!=="" && $dateend!=="") {
			$this->db->where("(dt_insert >= TIMESTAMP('$datestart') AND dt_insert <= TIMESTAMP('$dateend'))");
		}
		else {
			if ($datestart!=="") {
				$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
			}
			if ($dateend!=="") { // dateend
				$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
			}
		}

		// date filters ts_in_dt
		if ($ts_in_dt_start!=="" && $ts_in_dt_end!=="") {
			$this->db->where("(ts_in_dt >= TIMESTAMP('$ts_in_dt_start') AND ts_in_dt <= TIMESTAMP('$ts_in_dt_end'))");
		}
		else {
			if ($ts_in_dt_start!=="") {
				$this->db->where("(ts_in_dt >= TIMESTAMP('$ts_in_dt_start'))");
			}
			if ($ts_in_dt_end!=="") { // dateend
				$this->db->where("(ts_in_dt <= TIMESTAMP('$ts_in_dt_end'))");
			}
		}

		// date processing filters
		if ($start_processing!=="" && $end_processing!=="") {
			$this->db->where("(start_processing >= TIMESTAMP('$start_processing') AND end_processing <= TIMESTAMP('$end_processing'))");
		}
		else {
			if ($start_processing!=="") {
				$this->db->where("(start_processing >= TIMESTAMP('$start_processing'))");
			}
			if ($end_processing!=="") {
				if (!$blnFilterIsWating) {
					$this->db->where("(end_processing <= TIMESTAMP('$end_processing'))");
				}
				else {
					$this->db->where("(end_processing >= TIMESTAMP('$end_processing'))");
				}
			}
		}

		// is waiting picked
		if ($blnFilterIsWating) {
			$sqlFilterWaiting = "(wsm_bsm_status LIKE '0'";
			$sqlFilterWaiting .= " OR wsm_bsm_status LIKE '%Reminder Sent'";
			$sqlFilterWaiting .= " OR wsm_bsm_status LIKE '%12hr%')";
			$sqlFilterWaiting .= " AND (email_inviata_autofficina IS NOT NULL AND email_inviata_autofficina<>'')";
			$this->db->where($sqlFilterWaiting);
		}

		// date wsm_bsm_dt
		if ($wsm_bsm_dt_start!=="" && $wsm_bsm_dt_end!=="") {
			$this->db->where("(wsm_bsm_dt >= TIMESTAMP('$wsm_bsm_dt_start') AND wsm_bsm_dt <= TIMESTAMP('$wsm_bsm_dt_end'))");
		}
		else {
			if ($wsm_bsm_dt_start!=="") {
				if ($blnFilterIsWating) {
					$this->db->where("(wsm_bsm_dt >= TIMESTAMP('$wsm_bsm_dt_start') OR wsm_bsm_dt IS NULL)");
				}
				else {
					$this->db->where("(wsm_bsm_dt >= TIMESTAMP('$wsm_bsm_dt_start'))");
				}
			}
			if ($wsm_bsm_dt_end!=="") { // dateend
				$this->db->where("(wsm_bsm_dt <= TIMESTAMP('$wsm_bsm_dt_end'))");
			}
		}

		// text filter
		if ($searchext!=="") {
			$words = explode(" ",$searchext);
			$blnFirst = true;
			$v = "";
			$s = "";
			foreach ($arrFields as $el) {
				if ($el['textsearch']) {
					$blnFirstIn = true;
					$sIn = "";
					$vIn = "";
					foreach ($words as $word) {
						$sIn .= $vIn.$el['fieldName']." LIKE '%".$word."%'";
						if ($blnFirstIn) {
							$blnFirstIn = false;
							$vIn = ' OR ';
						}
					}
					$s .= $v."(".$sIn.")";
					if ($blnFirst) {
						$blnFirst = false;
						$v = ' OR ';
					}
				}
			}
			$cond = "($s)";
			$this->db->where($cond);
		}

		// filter for user stations
		if ($conditionStations!=="") {
			$this->db->where($conditionStations);
		}
		// get the total for paging
		$totalCount = $this->db->count_all_results();
		//------------------------------------------------

		// get list
		$selectFields = $this->buildSelectFields($arrFields);
		if (intval($totalCount)>0) {
			$this->db->select($selectFields);
			$this->db->from("ataraxia_main");
			$this->db->join("flottatab", "ataraxia_main.targa_veicolo = flottatab.REGISTRATION_NUMBER", "left");
			// status filter
			if ($blnFilterStatus) {
				$blnFirst = true;
				$v = "";
				$s = "";
				foreach ($arrStatus as $el) {
					$s .= $v."status=$el";
					if ($blnFirst) {
						$blnFirst = false;
						$v = ' OR ';
					}
				}
				$cond = "($s)";
				$this->db->where($cond);
			}

			// date filters
			if ($datestart!=="" && $dateend!=="") {
				$this->db->where("(dt_insert >= TIMESTAMP('$datestart') AND dt_insert <= TIMESTAMP('$dateend'))");
			}
			else {
				if ($datestart!=="") {
					$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
				}
				if ($dateend!=="") { // dateend
					$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
				}
			}

			// date filters ts_in_dt
			if ($ts_in_dt_start!=="" && $ts_in_dt_end!=="") {
				$this->db->where("(ts_in_dt >= TIMESTAMP('$ts_in_dt_start') AND ts_in_dt <= TIMESTAMP('$ts_in_dt_end'))");
			}
			else {
				if ($ts_in_dt_start!=="") {
					$this->db->where("(ts_in_dt >= TIMESTAMP('$ts_in_dt_start'))");
				}
				if ($ts_in_dt_end!=="") { // dateend
					$this->db->where("(ts_in_dt <= TIMESTAMP('$ts_in_dt_end'))");
				}
			}

			// date processing filters
			if ($start_processing!=="" && $end_processing!=="") {
				$this->db->where("(start_processing >= TIMESTAMP('$start_processing') AND end_processing <= TIMESTAMP('$end_processing'))");
			}
			else {
				if ($start_processing!=="") {
					$this->db->where("(start_processing >= TIMESTAMP('$start_processing'))");
				}
				if ($end_processing!=="") {
					if (!$blnFilterIsWating) {
						$this->db->where("(end_processing <= TIMESTAMP('$end_processing'))");
					}
					else {
						$this->db->where("(end_processing >= TIMESTAMP('$end_processing'))");
					}
				}
			}

			// is waiting picked
			if ($blnFilterIsWating) {
				$sqlFilterWaiting = "(wsm_bsm_status LIKE '0'";
				$sqlFilterWaiting .= " OR wsm_bsm_status LIKE '%Reminder Sent'";
				$sqlFilterWaiting .= " OR wsm_bsm_status LIKE '%12hr%')";
				$sqlFilterWaiting .= " AND (email_inviata_autofficina IS NOT NULL AND email_inviata_autofficina<>'')";
				$this->db->where($sqlFilterWaiting);
			}

			// date wsm_bsm_dt
			if ($wsm_bsm_dt_start!=="" && $wsm_bsm_dt_end!=="") {
				$this->db->where("(wsm_bsm_dt >= TIMESTAMP('$wsm_bsm_dt_start') AND wsm_bsm_dt <= TIMESTAMP('$wsm_bsm_dt_end'))");
			}
			else {
				if ($wsm_bsm_dt_start!=="") {
					if ($blnFilterIsWating) {
						$this->db->where("(wsm_bsm_dt >= TIMESTAMP('$wsm_bsm_dt_start') OR wsm_bsm_dt IS NULL)");
					}
					else {
						$this->db->where("(wsm_bsm_dt >= TIMESTAMP('$wsm_bsm_dt_start'))");
					}
				}
				if ($wsm_bsm_dt_end!=="") { // dateend
					$this->db->where("(wsm_bsm_dt <= TIMESTAMP('$wsm_bsm_dt_end'))");
				}
			}

			// text filter
			if ($searchext!=="") {
				$words = explode(" ",$searchext);
				$blnFirst = true;
				$v = "";
				$s = "";
				foreach ($arrFields as $el) {
					if ($el['textsearch']) {
						$blnFirstIn = true;
						$sIn = "";
						$vIn = "";
						foreach ($words as $word) {
							$sIn .= $vIn.$el['fieldName']." LIKE '%".$word."%'";
							if ($blnFirstIn) {
								$blnFirstIn = false;
								$vIn = ' OR ';
							}
						}
						$s .= $v."(".$sIn.")";
						if ($blnFirst) {
							$blnFirst = false;
							$v = ' OR ';
						}
					}
				}
				$cond = "($s)";
				$this->db->where($cond);
			}

			// filter for user stations
			if ($conditionStations!=="") {
				$this->db->where($conditionStations);
			}

			$this->db->order_by("ataraxia_main.id","desc");
			if (!$blnSelectAll) {
				$this->db->limit($limit,$start);
			}

			// get record list
			$records = $this->db->get()->result();

			return Array(
				"records" => $records,
				"totalCount" => $totalCount
			);
		}
		else {
			return Array(
				"records" => Array(),
				"totalCount" => 0
			);
		}
	}

}
