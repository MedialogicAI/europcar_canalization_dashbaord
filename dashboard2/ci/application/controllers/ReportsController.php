<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require("MainController.php");

class ReportsController extends MainController {

    public function __construct(){
		// set date intervals
		$today = date("Y-m-d");
		$month = date("m");

		$year = date("Y");
		$this->startDayDate = Array(
			"date" => "$today 00:00:00",
			"timestamp" => strtotime("$today 00:00:00")
		);
		$this->endDayDate = Array(
			"date" => "$today 23:59:59",
			"timestamp" => strtotime("$today 23:59:59")
		);
		$this->startMonthDate = Array(
			"date" => "$year-$month-01 00:00:00",
			"timestamp" => strtotime("$year-$month-01 00:00:00")
		);
		$this->endMonthDate = $this->endDayDate;

		$this->startYearDate = Array(
			"date" => "$year-01-01 00:00:00",
			"timestamp" => strtotime("$year-01-01 00:00:00")
		);
		$this->endYearDate = Array(
			"date" => "$year-12-31 23:59:59",
			"timestamp" => strtotime("$year-12-31 23:59:59")
		);
		// start date by moths ago
		$iMonthsAgo = 6; // how months
		$monthsAgo = date("Y-m-d", strtotime("-$iMonthsAgo months"));
		$this->startMonthsAgoDate = Array(
			"date" => "$monthsAgo 00:00:00",
			"timestamp" => strtotime("$monthsAgo 00:00:00")
		);

        parent::__construct(true);
    }


	// start date for the current day
	private $startDayDate = Array();

    // start date for the current month
	private $startMonthDate = Array();
	private $endMonthDate =  Array(); // ends with today

	// start date for the current year
	private $startYearDate = Array();
	private $endYearDate =  Array();

	// date starts by months ago
	private $startMonthsAgoDate = Array(); // start date for the practices, ie: 6 months ago


	// ################################### PRACTICES STATIONS AVERAGE WORKING TIME ###################################

	// Tempo Medio Globale (days) - Inserim. Richiesta Riparazione
	// tempo medio intero processo T0-T6
	public function getAvgStationWorkingTimeChart() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();

		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : $this->startMonthsAgoDate["date"];
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		$post = $this->input->post("defect");
		$defect = (!empty($post)) ? strtoupper(trim($post)) : '';

		$arrStations = $this->getStationsUser();
		$conditionStations = $this->buildSqlConditionStations($arrStations);
		$conditionsNull = "ts_in_dt IS NOT NULL AND ts_in_dt<>'' AND dt_t0 IS NOT NULL AND dt_t0<>''";
		$conditionsSelect = "($conditionsNull)";
		$howTotalPratices = 0;

		$this->db->where($conditionStations);

		$howTotalStation = 0;
		$minAvgTotal = null;
		$maxAvgTotal = 0;
		$avgTotal = 0;
		foreach ($arrStations as $station) {
			$minAvgStation = null;
			$maxAvgStation = 0;
			$avgStation = 0;
			$daysStations = 0;
			$howStationPratices = 0;
			$this->db->select("dt_t0 as time_start, ts_in_dt as time_end"); // select times
			$this->db->from("ataraxia_main");
			$this->db->where($conditionsSelect);
			$this->db->where("codice_stazione",$station->station_code);
			if ($defect !== '') {
				$this->db->where("defect_code", $defect);
			}
			if ($datestart!=="") {
				$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
			}
			if ($dateend!=="") { // dateend
				$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
			}
			$this->db->order_by("id","DESC");
			$arrStationPratices = $this->db->get()->result();
			foreach ($arrStationPratices as $el) {
				$dateStartDiff = strtotime($el->time_start);
				$dateEndDiff = strtotime($el->time_end);
				$secondsDiff = ($dateEndDiff - $dateStartDiff);
				$minutesDiff = ceil($secondsDiff / 60);
				$hoursDiff = ceil($minutesDiff / 60);
				$daysDiff = ceil($hoursDiff / 24);
				$daysDiffNoHolydays = $this->getHolidaysInRange($dateStartDiff,$dateEndDiff);
				if ($daysDiffNoHolydays<$daysDiff) {
					$daysDiff = $daysDiffNoHolydays;
				}
				$timeDiff = $daysDiff;
				if ($timeDiff<1) {
					$timeDiff = 1;
				}
				$howStationPratices++;
				$timeDiff = ($timeDiff<1) ? 1 : $timeDiff;
				$daysStations += $timeDiff;
				if (is_null($minAvgStation) || $timeDiff<$minAvgStation) {
					$minAvgStation = $timeDiff;
				}
				if ($timeDiff>$maxAvgStation) {
					$maxAvgStation = $timeDiff;
				}
			}
			if ($howStationPratices>0) {
				$howTotalStation++;
				$avgStation = intval(ceil($daysStations / $howStationPratices));
				$avgTotal += $avgStation;
				if (is_null($minAvgTotal) || $avgStation<$minAvgTotal) {
					$minAvgTotal = $avgStation;
				}
				if ($avgStation>$maxAvgTotal) {
					$maxAvgTotal = $avgStation;
				}
				$howTotalPratices += $howStationPratices;
			}
		}
		if ($howTotalStation>0) {
			$avgTotal = intval(ceil($avgTotal / $howTotalStation));
		}

		$r = Array(
			"type" => "textAvg",
			"value" => $avgTotal,
			"total" => $howTotalPratices
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMin",
			"value" => (is_null($minAvgTotal)) ? 0 : $minAvgTotal
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMax",
			"value" => $maxAvgTotal
		);
		array_push($arrData,$r);

		$this->sendOutput($arrData);
		return;

	}

	public function getSingleAvgStationWorkingTimeChart() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();
		$arrStations = Array();

		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : $this->startMonthsAgoDate["date"];
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		$post = $this->input->post("defect");
		$defect = (!empty($post)) ? strtoupper(trim($post)) : '';

		$post = $this->input->post("id");
		$id = (!empty($post)) ? $post : null;
		if (is_null($id) || $id==="") {
			$this->sendZero();
			return;
		}
		else {
			$r = new \stdClass();
			$r->station_code = $id;
			$arrStations[0] = $r;
		}

		$conditionsNull = "ts_in_dt IS NOT NULL AND ts_in_dt<>'' AND dt_t0 IS NOT NULL AND dt_t0<>''";
		$conditionsSelect = "($conditionsNull)";

		$minAvgStation = null;
		$maxAvgStation = 0;
		$avgStation = 0;
		$daysStations = 0;
		$howStationPratices = 0;
		$this->db->select("dt_t0 as time_start,ts_in_dt as time_end");
		$this->db->from("ataraxia_main");
		$this->db->where($conditionsSelect);
		$this->db->where("codice_stazione",$id);
		if ($defect !== '') {
			$this->db->where("defect_code", $defect);
		}
		if ($datestart!=="") {
			$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
		}
		if ($dateend!=="") { // dateend
			$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
		}
		$this->db->order_by("id","DESC");
		$arrStationPratices = $this->db->get()->result();

		foreach ($arrStationPratices as $el) {
			$dateStartDiff = strtotime($el->time_start);
			$dateEndDiff = strtotime($el->time_end);
			$secondsDiff = ($dateEndDiff - $dateStartDiff);
			$minutesDiff = ceil($secondsDiff / 60);
			$hoursDiff = ceil($minutesDiff / 60);
			$daysDiff = ceil($hoursDiff / 24);
			$daysDiffNoHolydays = $this->getHolidaysInRange($dateStartDiff,$dateEndDiff);
			if ($daysDiffNoHolydays<$daysDiff) {
				$daysDiff = $daysDiffNoHolydays;
			}
			$timeDiff = $daysDiff;
			if ($timeDiff<1) {
				$timeDiff = 1;
			}
			$howStationPratices++;
			$timeDiff = ($timeDiff<1) ? 1 : $timeDiff;
			$daysStations += $timeDiff;
			if (is_null($minAvgStation) || $timeDiff<$minAvgStation) {
				$minAvgStation = $timeDiff;
			}
			if ($timeDiff>$maxAvgStation) {
				$maxAvgStation = $timeDiff;
			}
		}

		if ($howStationPratices>0) {
			$avgStation = intval(ceil($daysStations / $howStationPratices));
		}

		$r = Array(
			"type" => "textAvg",
			"value" => $avgStation,
			"total" => $howStationPratices
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMin",
			"value" => (is_null($minAvgStation)) ? 0 : $minAvgStation
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMax",
			"value" => $maxAvgStation
		);
		array_push($arrData,$r);

		$this->sendOutput($arrData);
		return;

	}

	// Canalizzazione Robot (min)
	// tempo medio canalizzazione robot T2-T4
	public function getAllAvgStartEnd() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();
		$arrStations = Array();

		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : $this->startMonthsAgoDate["date"];
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		$post = $this->input->post("defect");
		$defect = (!empty($post)) ? strtoupper(trim($post)) : '';

		$arrStations = $this->getStationsUser();

		$conditionsNull = "start_processing IS NOT NULL AND start_processing<>'' AND end_processing IS NOT NULL AND end_processing<>''";
		$conditionsSelect = "($conditionsNull)";
		$howTotalPratices = 0;
		$howTotalStation = 0;
		$minAvgTotal = null;
		$maxAvgTotal = 0;
		$avgTotal = 0;
		foreach ($arrStations as $station) {
			$minAvgStation = null;
			$maxAvgStation = 0;
			$avgStation = 0;
			$daysStations = 0;
			$howStationPratices = 0;
			$this->db->select("start_processing as time_start,end_processing as time_end");
			$this->db->from("ataraxia_main");
			$this->db->where($conditionsSelect);
			$this->db->where("codice_stazione",$station->station_code);
			if ($defect !== '') {
				$this->db->where("defect_code", $defect);
			}
			if ($datestart!=="") {
				$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
			}
			if ($dateend!=="") { // dateend
				$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
			}
			$this->db->order_by("id","DESC");
			$arrStationPratices = $this->db->get()->result();
			foreach ($arrStationPratices as $el) {
				$dateStartDiff = strtotime($el->time_start);
				$dateEndDiff = strtotime($el->time_end);
				$secondsDiff = ($dateEndDiff - $dateStartDiff);
				$minutesDiff = ceil($secondsDiff / 60);
				$hoursDiff = ceil($minutesDiff / 60);
				$daysDiff = ceil($hoursDiff / 24);
				$timeDiff = $minutesDiff;
				if ($timeDiff<1) {
					$timeDiff = 1;
				}
				$howStationPratices++;
				$timeDiff = ($timeDiff<1) ? 1 : $timeDiff;
				$daysStations += $timeDiff;
				if (is_null($minAvgStation) || $timeDiff<$minAvgStation) {
					$minAvgStation = $timeDiff;
				}
				if ($timeDiff>$maxAvgStation) {
					$maxAvgStation = $timeDiff;
				}
			}
			if ($howStationPratices>0) {
				$howTotalStation++;
				$avgStation = intval(ceil($daysStations / $howStationPratices));
				$avgTotal += $avgStation;
				if (is_null($minAvgTotal) || $avgStation<$minAvgTotal) {
					$minAvgTotal = $avgStation;
				}
				if ($avgStation>$maxAvgTotal) {
					$maxAvgTotal = $avgStation;
				}
				$howTotalPratices += $howStationPratices;
			}
		}
		if ($howTotalStation>0) {
			$avgTotal = intval(ceil($avgTotal / $howTotalStation));
		}

		$r = Array(
			"type" => "textAvg",
			"value" => $avgTotal,
			"total" => $howTotalPratices
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMin",
			"value" => (is_null($minAvgTotal)) ? 0 : $minAvgTotal
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMax",
			"value" => $maxAvgTotal
		);
		array_push($arrData,$r);

		$this->sendOutput($arrData);
		return;

	}

	public function getSingleAvgStartEnd() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();
		$arrStations = Array();

		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : $this->startMonthsAgoDate["date"];
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		$post = $this->input->post("defect");
		$defect = (!empty($post)) ? strtoupper(trim($post)) : '';

		$post = $this->input->post("id");
		$id = (!empty($post)) ? $post : null;
		if (is_null($id) || $id==="") {
			$this->sendZero();
			return;
		}
		else {
			$r = new \stdClass();
			$r->station_code = $id;
			$arrStations[0] = $r;
		}

		$conditionsNull = "start_processing IS NOT NULL AND start_processing<>'' AND end_processing IS NOT NULL AND end_processing<>''";
		$conditionsSelect = "($conditionsNull)";

		$minAvgStation = null;
		$maxAvgStation = 0;
		$avgStation = 0;
		$daysStations = 0;
		$howStationPratices = 0;
		$this->db->select("start_processing as time_start, end_processing as time_end");
		$this->db->from("ataraxia_main");
		$this->db->where($conditionsSelect);
		$this->db->where("codice_stazione",$id);
		if ($defect !== '') {
			$this->db->where("defect_code", $defect);
		}
		if ($datestart!=="") {
			$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
		}
		if ($dateend!=="") { // dateend
			$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
		}
		$this->db->order_by("id","DESC");
		$arrStationPratices = $this->db->get()->result();

		foreach ($arrStationPratices as $el) {
			$dateStartDiff = strtotime($el->time_start);
			$dateEndDiff = strtotime($el->time_end);
			$secondsDiff = ($dateEndDiff - $dateStartDiff);
			$minutesDiff = ceil($secondsDiff / 60);
			$hoursDiff = ceil($minutesDiff / 60);
			$daysDiff = ceil($hoursDiff / 24);
			$timeDiff = $minutesDiff;
			if ($timeDiff<1) {
				$timeDiff = 1;
			}
			$howStationPratices++;
			$timeDiff = ($timeDiff<1) ? 1 : $timeDiff;
			$daysStations += $timeDiff;
			if (is_null($minAvgStation) || $timeDiff<$minAvgStation) {
				$minAvgStation = $timeDiff;
			}
			if ($timeDiff>$maxAvgStation) {
				$maxAvgStation = $timeDiff;
			}
		}

		if ($howStationPratices>0) {
			$avgStation = intval(ceil($daysStations / $howStationPratices));
		}
		$r = Array(
			"type" => "textAvg",
			"value" => $avgStation,
			"total" => $howStationPratices
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMin",
			"value" => (is_null($minAvgStation)) ? 0 : $minAvgStation
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMax",
			"value" => $maxAvgStation
		);
		array_push($arrData,$r);

		$this->sendOutput($arrData);
		return;

	}

	// Canalizzazione Robot Worked (min)
	// tempo medio canalizzazione robot T2-T4, status: 400, 401, 402, 403
	public function getAllAvgRobot() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();
		$arrStations = Array();

		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : $this->startMonthsAgoDate["date"];
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		$post = $this->input->post("defect");
		$defect = (!empty($post)) ? strtoupper(trim($post)) : '';

		$arrStations = $this->getStationsUser();

		$conditionsNull = "start_processing IS NOT NULL AND start_processing<>'' AND end_processing IS NOT NULL AND end_processing<>''";
		$conditionsSelect = "($conditionsNull)";
		$conditionsStatus = "(status IN (400,401,402,403))";
		$howTotalPratices = 0;
		$howTotalStation = 0;
		$minAvgTotal = null;
		$maxAvgTotal = 0;
		$avgTotal = 0;
		foreach ($arrStations as $station) {
			$minAvgStation = null;
			$maxAvgStation = 0;
			$avgStation = 0;
			$daysStations = 0;
			$howStationPratices = 0;
			$this->db->select("start_processing as time_start,end_processing as time_end");
			$this->db->from("ataraxia_main");
			$this->db->where($conditionsSelect);
			$this->db->where($conditionsStatus);
			$this->db->where("codice_stazione",$station->station_code);
			if ($defect !== '') {
				$this->db->where("defect_code", $defect);
			}
			if ($datestart!=="") {
				$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
			}
			if ($dateend!=="") { // dateend
				$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
			}
			$this->db->order_by("id","DESC");
			$arrStationPratices = $this->db->get()->result();
			foreach ($arrStationPratices as $el) {
				$dateStartDiff = strtotime($el->time_start);
				$dateEndDiff = strtotime($el->time_end);
				$secondsDiff = ($dateEndDiff - $dateStartDiff);
				$minutesDiff = ceil($secondsDiff / 60);
				$hoursDiff = ceil($minutesDiff / 60);
				$daysDiff = ceil($hoursDiff / 24);
				$timeDiff = $minutesDiff;
				if ($timeDiff<1) {
					$timeDiff = 1;
				}
				$howStationPratices++;
				$timeDiff = ($timeDiff<1) ? 1 : $timeDiff;
				$daysStations += $timeDiff;
				if (is_null($minAvgStation) || $timeDiff<$minAvgStation) {
					$minAvgStation = $timeDiff;
				}
				if ($timeDiff>$maxAvgStation) {
					$maxAvgStation = $timeDiff;
				}
			}
			if ($howStationPratices>0) {
				$howTotalStation++;
				$avgStation = intval(ceil($daysStations / $howStationPratices));
				$avgTotal += $avgStation;
				if (is_null($minAvgTotal) || $avgStation<$minAvgTotal) {
					$minAvgTotal = $avgStation;
				}
				if ($avgStation>$maxAvgTotal) {
					$maxAvgTotal = $avgStation;
				}
				$howTotalPratices += $howStationPratices;
			}
		}
		if ($howTotalStation>0) {
			$avgTotal = intval(ceil($avgTotal / $howTotalStation));
		}

		$r = Array(
			"type" => "textAvg",
			"value" => $avgTotal,
			"total" => $howTotalPratices
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMin",
			"value" => (is_null($minAvgTotal)) ? 0 : $minAvgTotal
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMax",
			"value" => $maxAvgTotal
		);
		array_push($arrData,$r);

		$this->sendOutput($arrData);
		return;

	}

	public function getSingleAvgRobot() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();
		$arrStations = Array();

		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : $this->startMonthsAgoDate["date"];
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		$post = $this->input->post("defect");
		$defect = (!empty($post)) ? strtoupper(trim($post)) : '';

		$post = $this->input->post("id");
		$id = (!empty($post)) ? $post : null;
		if (is_null($id) || $id==="") {
			$this->sendZero();
			return;
		}
		else {
			$r = new \stdClass();
			$r->station_code = $id;
			$arrStations[0] = $r;
		}

		$conditionsNull = "start_processing IS NOT NULL AND start_processing<>'' AND end_processing IS NOT NULL AND end_processing<>''";
		$conditionsSelect = "($conditionsNull)";
		$conditionsStatus = "(status IN (400,401,402,403))";

		$minAvgStation = null;
		$maxAvgStation = 0;
		$avgStation = 0;
		$daysStations = 0;
		$howStationPratices = 0;
		$this->db->select("start_processing as time_start, end_processing as time_end");
		$this->db->from("ataraxia_main");
		$this->db->where($conditionsSelect);
		$this->db->where($conditionsStatus);
		$this->db->where("codice_stazione",$id);
		if ($defect !== '') {
			$this->db->where("defect_code", $defect);
		}
		if ($datestart!=="") {
			$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
		}
		if ($dateend!=="") { // dateend
			$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
		}
		$this->db->order_by("id","DESC");
		$arrStationPratices = $this->db->get()->result();

		foreach ($arrStationPratices as $el) {
			$dateStartDiff = strtotime($el->time_start);
			$dateEndDiff = strtotime($el->time_end);
			$secondsDiff = ($dateEndDiff - $dateStartDiff);
			$minutesDiff = ceil($secondsDiff / 60);
			$hoursDiff = ceil($minutesDiff / 60);
			$daysDiff = ceil($hoursDiff / 24);
			$timeDiff = $minutesDiff;
			if ($timeDiff<1) {
				$timeDiff = 1;
			}
			$howStationPratices++;
			$timeDiff = ($timeDiff<1) ? 1 : $timeDiff;
			$daysStations += $timeDiff;
			if (is_null($minAvgStation) || $timeDiff<$minAvgStation) {
				$minAvgStation = $timeDiff;
			}
			if ($timeDiff>$maxAvgStation) {
				$maxAvgStation = $timeDiff;
			}
		}

		if ($howStationPratices>0) {
			$avgStation = intval(ceil($daysStations / $howStationPratices));
		}
		$r = Array(
			"type" => "textAvg",
			"value" => $avgStation,
			"total" => $howStationPratices
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMin",
			"value" => (is_null($minAvgStation)) ? 0 : $minAvgStation
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMax",
			"value" => $maxAvgStation
		);
		array_push($arrData,$r);

		$this->sendOutput($arrData);
		return;

	}

	// Global End Process/WSM (days) - Canalizzazione Manuale
	public function getAllAvgEndWsm() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();
		$arrStations = Array();

		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : $this->startMonthsAgoDate["date"];
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		$post = $this->input->post("defect");
		$defect = (!empty($post)) ? strtoupper(trim($post)) : '';

		$arrStations = $this->getStationsUser();

		$conditionsNull = "end_processing IS NOT NULL AND end_processing<>'' AND dt_t5 IS NOT NULL AND dt_t5<>''";
		$conditionsSelect = "($conditionsNull)";
		$howTotalPratices = 0;
		$howTotalStation = 0;
		$minAvgTotal = null;
		$maxAvgTotal = 0;
		$avgTotal = 0;
		foreach ($arrStations as $station) {
			$minAvgStation = null;
			$maxAvgStation = 0;
			$avgStation = 0;
			$daysStations = 0;
			$howStationPratices = 0;
			$this->db->select("end_processing as time_start,dt_t5 as time_end");
			$this->db->from("ataraxia_main");
			$this->db->where($conditionsSelect);
			$this->db->where("codice_stazione",$station->station_code);
			if ($defect !== '') {
				$this->db->where("defect_code", $defect);
			}
			if ($datestart!=="") {
				$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
			}
			if ($dateend!=="") { // dateend
				$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
			}
			$this->db->order_by("id","DESC");
			$arrStationPratices = $this->db->get()->result();
			foreach ($arrStationPratices as $el) {
				$dateStartDiff = strtotime($el->time_start);
				$dateEndDiff = strtotime($el->time_end);
				$secondsDiff = ($dateEndDiff - $dateStartDiff);
				$minutesDiff = ceil($secondsDiff / 60);
				$hoursDiff = ceil($minutesDiff / 60);
				$daysDiff = ceil($hoursDiff / 24);
				$daysDiffNoHolydays = $this->getHolidaysInRange($dateStartDiff,$dateEndDiff);
				if ($daysDiffNoHolydays<$daysDiff) {
					$daysDiff = $daysDiffNoHolydays;
				}
				$timeDiff = $daysDiff;
				if ($timeDiff<1) {
					$timeDiff = 1;
				}
				$howStationPratices++;
				$timeDiff = ($timeDiff<1) ? 1 : $timeDiff;
				$daysStations += $timeDiff;
				if (is_null($minAvgStation) || $timeDiff<$minAvgStation) {
					$minAvgStation = $timeDiff;
				}
				if ($timeDiff>$maxAvgStation) {
					$maxAvgStation = $timeDiff;
				}
			}
			if ($howStationPratices>0) {
				$howTotalStation++;
				$avgStation = intval(ceil($daysStations / $howStationPratices));
				$avgTotal += $avgStation;
				if (is_null($minAvgTotal) || $avgStation<$minAvgTotal) {
					$minAvgTotal = $avgStation;
				}
				if ($avgStation>$maxAvgTotal) {
					$maxAvgTotal = $avgStation;
				}
				$howTotalPratices += $howStationPratices;
			}
		}
		if ($howTotalStation>0) {
			$avgTotal = intval(ceil($avgTotal / $howTotalStation));
		}
		$r = Array(
			"type" => "textAvg",
			"value" => $avgTotal,
			"total" => $howTotalPratices
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMin",
			"value" => (is_null($minAvgTotal)) ? 0 : $minAvgTotal
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMax",
			"value" => $maxAvgTotal
		);
		array_push($arrData,$r);

		$this->sendOutput($arrData);
		return;

	}

	public function getSingleAvgEndWsm() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();
		$arrStations = Array();

		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : $this->startMonthsAgoDate["date"];
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		$post = $this->input->post("defect");
		$defect = (!empty($post)) ? strtoupper(trim($post)) : '';

		$post = $this->input->post("id");
		$id = (!empty($post)) ? $post : null;
		if (is_null($id) || $id==="") {
			$this->sendZero();
			return;
		}
		else {
			$r = new \stdClass();
			$r->station_code = $id;
			$arrStations[0] = $r;
		}

		$conditionsNull = "end_processing IS NOT NULL AND end_processing<>'' AND dt_t5 IS NOT NULL AND dt_t5<>''";
		$conditionsSelect = "($conditionsNull)";

		$minAvgStation = null;
		$maxAvgStation = 0;
		$avgStation = 0;
		$daysStations = 0;
		$howStationPratices = 0;
		$this->db->select("end_processing as time_start,dt_t5 as time_end");
		$this->db->from("ataraxia_main");
		$this->db->where($conditionsSelect);
		$this->db->where("codice_stazione",$id);
		if ($defect !== '') {
			$this->db->where("defect_code", $defect);
		}
		if ($datestart!=="") {
			$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
		}
		if ($dateend!=="") { // dateend
			$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
		}
		$this->db->order_by("id","DESC");
		$arrStationPratices = $this->db->get()->result();

		foreach ($arrStationPratices as $el) {
			$dateStartDiff = strtotime($el->time_start);
			$dateEndDiff = strtotime($el->time_end);
			$secondsDiff = ($dateEndDiff - $dateStartDiff);
			$minutesDiff = ceil($secondsDiff / 60);
			$hoursDiff = ceil($minutesDiff / 60);
			$daysDiff = ceil($hoursDiff / 24);
			$daysDiffNoHolydays = $this->getHolidaysInRange($dateStartDiff,$dateEndDiff);
			if ($daysDiffNoHolydays<$daysDiff) {
				$daysDiff = $daysDiffNoHolydays;
			}
			$timeDiff = $daysDiff;
			if ($timeDiff<1) {
				$timeDiff = 1;
			}
			$howStationPratices++;
			$timeDiff = ($timeDiff<1) ? 1 : $timeDiff;
			$daysStations += $timeDiff;
			if (is_null($minAvgStation) || $timeDiff<$minAvgStation) {
				$minAvgStation = $timeDiff;
			}
			if ($timeDiff>$maxAvgStation) {
				$maxAvgStation = $timeDiff;
			}
		}

		if ($howStationPratices>0) {
			$avgStation = intval(ceil($daysStations / $howStationPratices));
		}
		$r = Array(
			"type" => "textAvg",
			"value" => $avgStation,
			"total" => $howStationPratices
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMin",
			"value" => (is_null($minAvgStation)) ? 0 : $minAvgStation
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMax",
			"value" => $maxAvgStation
		);
		array_push($arrData,$r);

		$this->sendOutput($arrData);
		return;

	}

	// Global Checkin/IRM Open (days) - Apertura IRM
	// tempo medio apertura T0-T3
	public function getAllAvgCiIrm() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();
		$arrStations = Array();

		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : $this->startMonthsAgoDate["date"];
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		$post = $this->input->post("defect");
		$defect = (!empty($post)) ? strtoupper(trim($post)) : '';

		$arrStations = $this->getStationsUser();

		$conditionsNull = "ts_in_dt IS NOT NULL AND ts_in_dt<>'' AND irm_dt IS NOT NULL AND irm_dt<>''";
		$conditionsSelect = "($conditionsNull)";
		$howTotalPratices = 0;
		$howTotalStation = 0;
		$minAvgTotal = null;
		$maxAvgTotal = 0;
		$avgTotal = 0;
		foreach ($arrStations as $station) {
			$minAvgStation = null;
			$maxAvgStation = 0;
			$avgStation = 0;
			$daysStations = 0;
			$howStationPratices = 0;
			$this->db->select("ts_in_dt as time_start, irm_dt as time_end");
			$this->db->from("ataraxia_main");
			$this->db->where($conditionsSelect);
			$this->db->where("codice_stazione",$station->station_code);
			if ($defect !== '') {
				$this->db->where("defect_code", $defect);
			}
			if ($datestart!=="") {
				$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
			}
			if ($dateend!=="") { // dateend
				$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
			}
			$this->db->where("irm_status", 5);
			$this->db->order_by("id","DESC");
			$arrStationPratices = $this->db->get()->result();
			foreach ($arrStationPratices as $el) {
				$dateStartDiff = strtotime($el->time_start);
				$dateEndDiff = strtotime($el->time_end);
				$secondsDiff = ($dateEndDiff - $dateStartDiff);
				$minutesDiff = ceil($secondsDiff / 60);
				$hoursDiff = ceil($minutesDiff / 60);
				$daysDiff = ceil($hoursDiff / 24);
//				$daysDiffNoHolydays = $this->getHolidaysInRange($dateStartDiff,$dateEndDiff);
////				if ($daysDiffNoHolydays<$daysDiff) {
////					$daysDiff = $daysDiffNoHolydays;
////				}
				$timeDiff = $minutesDiff;
				if ($timeDiff<1) {
					$timeDiff = 1;
				}
				$howStationPratices++;
				$timeDiff = ($timeDiff<1) ? 1 : $timeDiff;
				$daysStations += $timeDiff;
				if (is_null($minAvgStation) || $timeDiff<$minAvgStation) {
					$minAvgStation = $timeDiff;
				}
				if ($timeDiff>$maxAvgStation) {
					$maxAvgStation = $timeDiff;
				}
			}
			if ($howStationPratices>0) {
				$howTotalStation++;
				$avgStation = intval(ceil($daysStations / $howStationPratices));
				$avgTotal += $avgStation;
				if (is_null($minAvgTotal) || $avgStation<$minAvgTotal) {
					$minAvgTotal = $avgStation;
				}
				if ($avgStation>$maxAvgTotal) {
					$maxAvgTotal = $avgStation;
				}
				$howTotalPratices += $howStationPratices;
			}
		}
		if ($howTotalStation>0) {
			$avgTotal = intval(ceil($avgTotal / $howTotalStation));
		}
		$r = Array(
			"type" => "textAvg",
			"value" => $avgTotal,
			"total" => $howTotalPratices
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMin",
			"value" => (is_null($minAvgTotal)) ? 0 : $minAvgTotal
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMax",
			"value" => $maxAvgTotal
		);
		array_push($arrData,$r);

		$this->sendOutput($arrData);
		return;

	}

	public function getSingleAvgCiIrm() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();
		$arrStations = Array();

		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : $this->startMonthsAgoDate["date"];
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		$post = $this->input->post("defect");
		$defect = (!empty($post)) ? strtoupper(trim($post)) : '';

		$post = $this->input->post("id");
		$id = (!empty($post)) ? $post : null;
		if (is_null($id) || $id==="") {
			$this->sendZero();
			return;
		}
		else {
			$r = new \stdClass();
			$r->station_code = $id;
			$arrStations[0] = $r;
		}

		$conditionsNull = "ts_in_dt IS NOT NULL AND ts_in_dt<>'' AND irm_dt IS NOT NULL AND irm_dt<>''";
		$conditionsSelect = "($conditionsNull)";

		$minAvgStation = null;
		$maxAvgStation = 0;
		$avgStation = 0;
		$daysStations = 0;
		$howStationPratices = 0;
		$this->db->select("ts_in_dt as time_start,irm_dt as time_end");
		$this->db->from("ataraxia_main");
		$this->db->where($conditionsSelect);
		$this->db->where("codice_stazione",$id);
		if ($defect !== '') {
			$this->db->where("defect_code", $defect);
		}
		if ($datestart!=="") {
			$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
		}
		if ($dateend!=="") { // dateend
			$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
		}
		$this->db->order_by("id","DESC");
		$arrStationPratices = $this->db->get()->result();

		foreach ($arrStationPratices as $el) {
			$dateStartDiff = strtotime($el->time_start);
			$dateEndDiff = strtotime($el->time_end);
			$secondsDiff = ($dateEndDiff - $dateStartDiff);
			$minutesDiff = ceil($secondsDiff / 60);
			$hoursDiff = ceil($minutesDiff / 60);
			$daysDiff = ceil($hoursDiff / 24);
//			$daysDiffNoHolydays = $this->getHolidaysInRange($dateStartDiff,$dateEndDiff);
//			if ($daysDiffNoHolydays<$daysDiff) {
//				$daysDiff = $daysDiffNoHolydays;
//			}
			$timeDiff = $minutesDiff;
			if ($timeDiff<1) {
				$timeDiff = 1;
			}
			$howStationPratices++;
			$timeDiff = ($timeDiff<1) ? 1 : $timeDiff;
			$daysStations += $timeDiff;
			if (is_null($minAvgStation) || $timeDiff<$minAvgStation) {
				$minAvgStation = $timeDiff;
			}
			if ($timeDiff>$maxAvgStation) {
				$maxAvgStation = $timeDiff;
			}
		}

		if ($howStationPratices>0) {
			$avgStation = intval(ceil($daysStations / $howStationPratices));
		}
		$r = Array(
			"type" => "textAvg",
			"value" => $avgStation,
			"total" => $howStationPratices
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMin",
			"value" => (is_null($minAvgStation)) ? 0 : $minAvgStation
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMax",
			"value" => $maxAvgStation
		);
		array_push($arrData,$r);

		$this->sendOutput($arrData);
		return;

	}

	// Global Checkin/Repair Request (days) - Permanenza Presso Stazione
	public function getAllAvgCiWsm() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();
		$arrStations = Array();

		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : $this->startMonthsAgoDate["date"];
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		$post = $this->input->post("defect");
		$defect = (!empty($post)) ? strtoupper(trim($post)) : '';

		$arrStations = $this->getStationsUser();

		$conditionsNull = "dt_t0 IS NOT NULL AND dt_t0<>'' AND dt_t6 IS NOT NULL AND dt_t6<>''";
		$conditionsSelect = "($conditionsNull)";
		$howTotalPratices = 0;
		$howTotalStation = 0;
		$minAvgTotal = null;
		$maxAvgTotal = 0;
		$avgTotal = 0;
		foreach ($arrStations as $station) {
			$minAvgStation = null;
			$maxAvgStation = 0;
			$avgStation = 0;
			$daysStations = 0;
			$howStationPratices = 0;
			$this->db->select("dt_t0 as time_start,dt_t6 as time_end");
			$this->db->from("ataraxia_main");
			$this->db->where($conditionsSelect);
			$this->db->where("codice_stazione",$station->station_code);
			if ($defect !== '') {
				$this->db->where("defect_code", $defect);
			}
			if ($datestart!=="") {
				$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
			}
			if ($dateend!=="") { // dateend
				$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
			}
			$this->db->order_by("id","DESC");
			$arrStationPratices = $this->db->get()->result();
			foreach ($arrStationPratices as $el) {
				$dateStartDiff = strtotime($el->time_start);
				$dateEndDiff = strtotime($el->time_end);
				$secondsDiff = ($dateEndDiff - $dateStartDiff);
				$minutesDiff = ceil($secondsDiff / 60);
				$hoursDiff = ceil($minutesDiff / 60);
				$daysDiff = ceil($hoursDiff / 24);
				$daysDiffNoHolydays = $this->getHolidaysInRange($dateStartDiff,$dateEndDiff);
				if ($daysDiffNoHolydays<$daysDiff) {
					$daysDiff = $daysDiffNoHolydays;
				}
				$timeDiff = intval($daysDiff);
				if ($timeDiff<1) {
					$timeDiff = 1;
				}
				$howStationPratices++;
				$timeDiff = ($timeDiff<1) ? 1 : $timeDiff;
				$daysStations += $timeDiff;
				if (is_null($minAvgStation) || $timeDiff<$minAvgStation) {
					$minAvgStation = $timeDiff;
				}
				if ($timeDiff>$maxAvgStation) {
					$maxAvgStation = $timeDiff;
				}
			}
			if ($howStationPratices>0) {
				$howTotalStation++;
				$avgStation = intval(ceil($daysStations / $howStationPratices));
				$avgTotal += $avgStation;
				if (is_null($minAvgTotal) || $avgStation<$minAvgTotal) {
					$minAvgTotal = $avgStation;
				}
				if ($avgStation>$maxAvgTotal) {
					$maxAvgTotal = $avgStation;
				}
				$howTotalPratices += $howStationPratices;
			}
		}
		if ($howTotalStation>0) {
			$avgTotal = intval(ceil($avgTotal / $howTotalStation));
		}
		$r = Array(
			"type" => "textAvg",
			"value" => $avgTotal,
			"total" => $howTotalPratices
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMin",
			"value" => (is_null($minAvgTotal)) ? 0 : $minAvgTotal
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMax",
			"value" => $maxAvgTotal
		);
		array_push($arrData,$r);

		$this->sendOutput($arrData);
		return;

	}

	public function getSingleAvgCiWsm() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();
		$arrStations = Array();

		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : $this->startMonthsAgoDate["date"];
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		$post = $this->input->post("defect");
		$defect = (!empty($post)) ? strtoupper(trim($post)) : '';

		$post = $this->input->post("id");
		$id = (!empty($post)) ? $post : null;
		if (is_null($id) || $id==="") {
			$this->sendZero();
			return;
		}
		else {
			$r = new \stdClass();
			$r->station_code = $id;
			$arrStations[0] = $r;
		}

		$conditionsNull = "dt_t0 IS NOT NULL AND dt_t0<>'' AND dt_t6 IS NOT NULL AND dt_t6<>''";
		$conditionsSelect = "($conditionsNull)";

		$minAvgStation = null;
		$maxAvgStation = 0;
		$avgStation = 0;
		$daysStations = 0;
		$howStationPratices = 0;
		$this->db->select("dt_t0 as time_start,dt_t6 as time_end");
		$this->db->from("ataraxia_main");
		$this->db->where($conditionsSelect);
		$this->db->where("codice_stazione",$id);
		if ($defect !== '') {
			$this->db->where("defect_code", $defect);
		}
		if ($datestart!=="") {
			$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
		}
		if ($dateend!=="") { // dateend
			$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
		}
		$this->db->order_by("id","DESC");
		$arrStationPratices = $this->db->get()->result();

		foreach ($arrStationPratices as $el) {
			$dateStartDiff = strtotime($el->time_start);
			$dateEndDiff = strtotime($el->time_end);
			$secondsDiff = ($dateEndDiff - $dateStartDiff);
			$minutesDiff = ceil($secondsDiff / 60);
			$hoursDiff = ceil($minutesDiff / 60);
			$daysDiff = ceil($hoursDiff / 24);
			$daysDiffNoHolydays = $this->getHolidaysInRange($dateStartDiff,$dateEndDiff);
			if ($daysDiffNoHolydays<$daysDiff) {
				$daysDiff = $daysDiffNoHolydays;
			}
			$timeDiff = $daysDiff;
			if ($timeDiff<1) {
				$timeDiff = 1;
			}
			$howStationPratices++;
			$timeDiff = ($timeDiff<1) ? 1 : $timeDiff;
			$daysStations += $timeDiff;
			if (is_null($minAvgStation) || $timeDiff<$minAvgStation) {
				$minAvgStation = $timeDiff;
			}
			if ($timeDiff>$maxAvgStation) {
				$maxAvgStation = $timeDiff;
			}
		}

		if ($howStationPratices>0) {
			$avgStation = intval(ceil($daysStations / $howStationPratices));
		}
		$r = Array(
			"type" => "textAvg",
			"value" => $avgStation,
			"total" => $howStationPratices
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMin",
			"value" => (is_null($minAvgStation)) ? 0 : $minAvgStation
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMax",
			"value" => $maxAvgStation
		);
		array_push($arrData,$r);

		$this->sendOutput($arrData);
		return;

	}

	// Repair (days)
	public function getAllAvgRepair() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();
		$arrStations = Array();

		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : $this->startMonthsAgoDate["date"];
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		$post = $this->input->post("defect");
		$defect = (!empty($post)) ? strtoupper(trim($post)) : '';

		$arrStations = $this->getStationsUser();

		$conditionsNull = "dt_t6 IS NOT NULL AND dt_t6<>'' AND dt_t7 IS NOT NULL AND dt_t7<>''";
		$conditionsSelect = "($conditionsNull)";
		$howTotalPratices = 0;
		$howTotalStation = 0;
		$minAvgTotal = null;
		$maxAvgTotal = 0;
		$avgTotal = 0;
		foreach ($arrStations as $station) {
			$minAvgStation = null;
			$maxAvgStation = 0;
			$avgStation = 0;
			$daysStations = 0;
			$howStationPratices = 0;
			$this->db->select("dt_t6 as time_start,dt_t7 as time_end");
			$this->db->from("ataraxia_main");
			$this->db->where($conditionsSelect);
			$this->db->where("codice_stazione",$station->station_code);
			if ($defect !== '') {
				$this->db->where("defect_code", $defect);
			}
			if ($datestart!=="") {
				$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
			}
			if ($dateend!=="") { // dateend
				$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
			}
			$this->db->order_by("id","DESC");
			$arrStationPratices = $this->db->get()->result();
			foreach ($arrStationPratices as $el) {
				$dateStartDiff = strtotime($el->time_start);
				$dateEndDiff = strtotime($el->time_end);
				$secondsDiff = ($dateEndDiff - $dateStartDiff);
				$minutesDiff = ceil($secondsDiff / 60);
				$hoursDiff = ceil($minutesDiff / 60);
				$daysDiff = ceil($hoursDiff / 24);
				$daysDiffNoHolydays = $this->getHolidaysInRange($dateStartDiff,$dateEndDiff);
				if ($daysDiffNoHolydays<$daysDiff) {
					$daysDiff = $daysDiffNoHolydays;
				}
				$timeDiff = intval($daysDiff);
				if ($timeDiff<1) {
					$timeDiff = 1;
				}
				$howStationPratices++;
				$timeDiff = ($timeDiff<1) ? 1 : $timeDiff;
				$daysStations += $timeDiff;
				if (is_null($minAvgStation) || $timeDiff<$minAvgStation) {
					$minAvgStation = $timeDiff;
				}
				if ($timeDiff>$maxAvgStation) {
					$maxAvgStation = $timeDiff;
				}
			}
			if ($howStationPratices>0) {
				$howTotalStation++;
				$avgStation = intval(ceil($daysStations / $howStationPratices));
				$avgTotal += $avgStation;
				if (is_null($minAvgTotal) || $avgStation<$minAvgTotal) {
					$minAvgTotal = $avgStation;
				}
				if ($avgStation>$maxAvgTotal) {
					$maxAvgTotal = $avgStation;
				}
				$howTotalPratices += $howStationPratices;
			}
		}
		if ($howTotalStation>0) {
			$avgTotal = intval(ceil($avgTotal / $howTotalStation));
		}
		$r = Array(
			"type" => "textAvg",
			"value" => $avgTotal,
			"total" => $howTotalPratices
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMin",
			"value" => (is_null($minAvgTotal)) ? 0 : $minAvgTotal
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMax",
			"value" => $maxAvgTotal
		);
		array_push($arrData,$r);

		$this->sendOutput($arrData);
		return;

	}

	public function getSingleAvgRepair() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();
		$arrStations = Array();

		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : $this->startMonthsAgoDate["date"];
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		$post = $this->input->post("defect");
		$defect = (!empty($post)) ? strtoupper(trim($post)) : '';

		$post = $this->input->post("id");
		$id = (!empty($post)) ? $post : null;
		if (is_null($id) || $id==="") {
			$this->sendZero();
			return;
		}
		else {
			$r = new \stdClass();
			$r->station_code = $id;
			$arrStations[0] = $r;
		}

		$conditionsNull = "dt_t6 IS NOT NULL AND dt_t6<>'' AND dt_t7 IS NOT NULL AND dt_t7<>''";
		$conditionsSelect = "($conditionsNull)";

		$minAvgStation = null;
		$maxAvgStation = 0;
		$avgStation = 0;
		$daysStations = 0;
		$howStationPratices = 0;
		$this->db->select("dt_t6 as time_start,dt_t7 as time_end");
		$this->db->from("ataraxia_main");
		$this->db->where($conditionsSelect);
		$this->db->where("codice_stazione",$id);
		if ($defect !== '') {
			$this->db->where("defect_code", $defect);
		}
		if ($datestart!=="") {
			$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
		}
		if ($dateend!=="") { // dateend
			$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
		}
		$this->db->order_by("id","DESC");
		$arrStationPratices = $this->db->get()->result();

		foreach ($arrStationPratices as $el) {
			$dateStartDiff = strtotime($el->time_start);
			$dateEndDiff = strtotime($el->time_end);
			$secondsDiff = ($dateEndDiff - $dateStartDiff);
			$minutesDiff = ceil($secondsDiff / 60);
			$hoursDiff = ceil($minutesDiff / 60);
			$daysDiff = ceil($hoursDiff / 24);
			$daysDiffNoHolydays = $this->getHolidaysInRange($dateStartDiff,$dateEndDiff);
			if ($daysDiffNoHolydays<$daysDiff) {
				$daysDiff = $daysDiffNoHolydays;
			}
			$timeDiff = $daysDiff;
			if ($timeDiff<1) {
				$timeDiff = 1;
			}
			$howStationPratices++;
			$timeDiff = ($timeDiff<1) ? 1 : $timeDiff;
			$daysStations += $timeDiff;
			if (is_null($minAvgStation) || $timeDiff<$minAvgStation) {
				$minAvgStation = $timeDiff;
			}
			if ($timeDiff>$maxAvgStation) {
				$maxAvgStation = $timeDiff;
			}
		}

		if ($howStationPratices>0) {
			$avgStation = intval(ceil($daysStations / $howStationPratices));
		}
		$r = Array(
			"type" => "textAvg",
			"value" => $avgStation,
			"total" => $howStationPratices
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMin",
			"value" => (is_null($minAvgStation)) ? 0 : $minAvgStation
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMax",
			"value" => $maxAvgStation
		);
		array_push($arrData,$r);

		$this->sendOutput($arrData);
		return;

	}

	// Fermo (days)
	public function getAllAvgFermo() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();
		$arrStations = Array();

		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : $this->startMonthsAgoDate["date"];
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		$post = $this->input->post("defect");
		$defect = (!empty($post)) ? strtoupper(trim($post)) : '';

		$arrStations = $this->getStationsUser();

		$conditionsNull = "dt_t0 IS NOT NULL AND dt_t0<>'' AND dt_t7 IS NOT NULL AND dt_t7<>''";
		$conditionsSelect = "($conditionsNull)";
		$howTotalPratices = 0;
		$howTotalStation = 0;
		$minAvgTotal = null;
		$maxAvgTotal = 0;
		$avgTotal = 0;
		foreach ($arrStations as $station) {
			$minAvgStation = null;
			$maxAvgStation = 0;
			$avgStation = 0;
			$daysStations = 0;
			$howStationPratices = 0;
			$this->db->select("dt_t0 as time_start,dt_t7 as time_end");
			$this->db->from("ataraxia_main");
			$this->db->where($conditionsSelect);
			$this->db->where("codice_stazione",$station->station_code);
			if ($defect !== '') {
				$this->db->where("defect_code", $defect);
			}
			if ($datestart!=="") {
				$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
			}
			if ($dateend!=="") { // dateend
				$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
			}
			$this->db->order_by("id","DESC");
			$arrStationPratices = $this->db->get()->result();
			foreach ($arrStationPratices as $el) {
				$dateStartDiff = strtotime($el->time_start);
				$dateEndDiff = strtotime($el->time_end);
				$secondsDiff = ($dateEndDiff - $dateStartDiff);
				$minutesDiff = ceil($secondsDiff / 60);
				$hoursDiff = ceil($minutesDiff / 60);
				$daysDiff = ceil($hoursDiff / 24);
				$daysDiffNoHolydays = $this->getHolidaysInRange($dateStartDiff,$dateEndDiff);
				if ($daysDiffNoHolydays<$daysDiff) {
					$daysDiff = $daysDiffNoHolydays;
				}
				$timeDiff = intval($daysDiff);
				if ($timeDiff<1) {
					$timeDiff = 1;
				}
				$howStationPratices++;
				$timeDiff = ($timeDiff<1) ? 1 : $timeDiff;
				$daysStations += $timeDiff;
				if (is_null($minAvgStation) || $timeDiff<$minAvgStation) {
					$minAvgStation = $timeDiff;
				}
				if ($timeDiff>$maxAvgStation) {
					$maxAvgStation = $timeDiff;
				}
			}
			if ($howStationPratices>0) {
				$howTotalStation++;
				$avgStation = intval(ceil($daysStations / $howStationPratices));
				$avgTotal += $avgStation;
				if (is_null($minAvgTotal) || $avgStation<$minAvgTotal) {
					$minAvgTotal = $avgStation;
				}
				if ($avgStation>$maxAvgTotal) {
					$maxAvgTotal = $avgStation;
				}
				$howTotalPratices += $howStationPratices;
			}
		}
		if ($howTotalStation>0) {
			$avgTotal = intval(ceil($avgTotal / $howTotalStation));
		}
		$r = Array(
			"type" => "textAvg",
			"value" => $avgTotal,
			"total" => $howTotalPratices
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMin",
			"value" => (is_null($minAvgTotal)) ? 0 : $minAvgTotal
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMax",
			"value" => $maxAvgTotal
		);
		array_push($arrData,$r);

		$this->sendOutput($arrData);
		return;

	}

	public function getSingleAvgFermo() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();
		$arrStations = Array();

		$post = $this->input->post("datestart");
		$datestart = (!empty($post)) ? $post : $this->startMonthsAgoDate["date"];
		$post = $this->input->post("dateend");
		$dateend = (!empty($post)) ? $post : '';
		$post = $this->input->post("defect");
		$defect = (!empty($post)) ? strtoupper(trim($post)) : '';

		$post = $this->input->post("id");
		$id = (!empty($post)) ? $post : null;
		if (is_null($id) || $id==="") {
			$this->sendZero();
			return;
		}
		else {
			$r = new \stdClass();
			$r->station_code = $id;
			$arrStations[0] = $r;
		}

		$conditionsNull = "dt_t0 IS NOT NULL AND dt_t0<>'' AND dt_t7 IS NOT NULL AND dt_t7<>''";
		$conditionsSelect = "($conditionsNull)";

		$minAvgStation = null;
		$maxAvgStation = 0;
		$avgStation = 0;
		$daysStations = 0;
		$howStationPratices = 0;
		$this->db->select("dt_t0 as time_start,dt_t7 as time_end");
		$this->db->from("ataraxia_main");
		$this->db->where($conditionsSelect);
		$this->db->where("codice_stazione",$id);
		if ($defect !== '') {
			$this->db->where("defect_code", $defect);
		}
		if ($datestart!=="") {
			$this->db->where("(dt_insert >= TIMESTAMP('$datestart'))");
		}
		if ($dateend!=="") { // dateend
			$this->db->where("(dt_insert <= TIMESTAMP('$dateend'))");
		}
		$this->db->order_by("id","DESC");
		$arrStationPratices = $this->db->get()->result();

		foreach ($arrStationPratices as $el) {
			$dateStartDiff = strtotime($el->time_start);
			$dateEndDiff = strtotime($el->time_end);
			$secondsDiff = ($dateEndDiff - $dateStartDiff);
			$minutesDiff = ceil($secondsDiff / 60);
			$hoursDiff = ceil($minutesDiff / 60);
			$daysDiff = ceil($hoursDiff / 24);
			$daysDiffNoHolydays = $this->getHolidaysInRange($dateStartDiff,$dateEndDiff);
			if ($daysDiffNoHolydays<$daysDiff) {
				$daysDiff = $daysDiffNoHolydays;
			}
			$timeDiff = $daysDiff;
			if ($timeDiff<1) {
				$timeDiff = 1;
			}
			$howStationPratices++;
			$timeDiff = ($timeDiff<1) ? 1 : $timeDiff;
			$daysStations += $timeDiff;
			if (is_null($minAvgStation) || $timeDiff<$minAvgStation) {
				$minAvgStation = $timeDiff;
			}
			if ($timeDiff>$maxAvgStation) {
				$maxAvgStation = $timeDiff;
			}
		}

		if ($howStationPratices>0) {
			$avgStation = intval(ceil($daysStations / $howStationPratices));
		}
		$r = Array(
			"type" => "textAvg",
			"value" => $avgStation,
			"total" => $howStationPratices
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMin",
			"value" => (is_null($minAvgStation)) ? 0 : $minAvgStation
		);
		array_push($arrData,$r);
		$r = Array(
			"type" => "textMax",
			"value" => $maxAvgStation
		);
		array_push($arrData,$r);

		$this->sendOutput($arrData);
		return;

	}





	// ----------------------------------------------------------------------------------------------------------------
	private function getHolidaysInRange($dateStart,$dateEnd) {
		$how = 0;
		$secPerDay = 86400;

		$dateStartString = date("Y-m-d",$dateStart);
		$dateStartString .= " 00:00:00";
		$dateStartZero = new DateTime($dateStartString);
		$dateStartZero = $dateStartZero->getTimeStamp();

		$dateEndString = date("Y-m-d",$dateEnd);
		$dateEndString .= " 00:00:00";
		$dateEndZero = new DateTime($dateEndString);
		$dateEndZero = $dateEndZero->getTimeStamp();

		$currentDate = $dateStartZero;
		while ($currentDate<=$dateEndZero) {
			if (!$this->isHoliday($currentDate)) {
				$how++;
			}
			$currentDate += $secPerDay;
		}

		return $how;
	}

	// date: timestamp format
	private function isHoliday($dateTime) {
		// giorno/mese
		$arrHolidays = Array(
			'01/01',
			'06/01',
			'25/04',
			'01/05',
			'02/06',
			'15/08',
			'01/11',
			'08/12',
			'25/12',
			'26/12'
		);
		$dateString = date("d/m/Y",$dateTime);
		$arrDay = explode("/",$dateString);
		$year = $arrDay[2];
		$month = (strlen($arrDay[1])<2) ? "0".$arrDay[1] : $arrDay[1];
		$day = (strlen($arrDay[0])<2) ? "0".$arrDay[0] : $arrDay[0];
		$day_month = "$day/$month";
		if (in_array($day_month,$arrHolidays)) {
			return true;
		}
		$dayWeek = date('w',$dateTime);
		if ($dayWeek==0 || $dayWeek==6) {
			return true;
		}
		return false;
	}

}
