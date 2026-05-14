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
	// YYY NON PIU USATA PER GRIGLIA - all stations workin time avg
	public function getAvgStationWorkingTime() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();


		$start = intval($this->input->post("start"));
		$limit = intval($this->input->post("limit"));

		$arrTemp = $this->getStationsUser($limit,$start);
		$arrStations = $arrTemp['records'];
		$totalCount = $arrTemp['totalCount'];


		$conditionsNull = "ts_in_dt IS NOT NULL AND ts_in_dt<>'' AND wsm_bsm_dt IS NOT NULL AND wsm_bsm_dt<>''";
		$conditionsStatus = "status=400 OR status=402";
		$conditionsSelect = "($conditionsNull) AND ($conditionsStatus)";
		$howTotalPratices = 0;
		foreach ($arrStations as $station) {
			$avgStation = 0;
			$daysStations = 0;
			$howStationPratices = 0;
			$this->db->select("ts_in_dt as time_start,wsm_bsm_dt as time_end");
			$this->db->from("ataraxia_main");
			$this->db->where($conditionsSelect);
			$this->db->where("codice_stazione",$station->station_code);
			$this->db->order_by("id","DESC");
			$arrStationPratices = $this->db->get()->result();
			foreach ($arrStationPratices as $el) {
				$dateStartDiff = date_create($el->time_start);
				$dateEndDiff = date_create($el->time_end);
				$timeDiff = date_diff($dateEndDiff,$dateStartDiff)->days;
				$daysDiffNoHolydays = $this->getHolidaysInRange($dateStartDiff,$dateEndDiff);
				if ($daysDiffNoHolydays<$timeDiff) {
					$timeDiff = $daysDiffNoHolydays;
				}
				if ($timeDiff!==FALSE) {
					$howStationPratices++;
					$howTotalPratices++;
					$timeDiff = ($timeDiff<1) ? 1 : $timeDiff;
					$daysStations += $timeDiff;
				}
			}
			if ($howStationPratices>0) {
				$avgStation = intval(ceil($daysStations / $howStationPratices));
				$r = Array(
					"station_code" => $station->station_code,
					"station_average" => $avgStation
				);
				array_push($arrData,$r);
			}
		}

		$this->sendOutput($arrData,$totalCount);
		return;
	}

	// tempo medio intero processo T0-T6
	public function getAvgStationWorkingTimeChart() {
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

		$arrStations = $this->getStationsUser();

		$conditionsNull = "ts_in_dt IS NOT NULL AND ts_in_dt<>'' AND wsm_bsm_dt IS NOT NULL AND wsm_bsm_dt<>''";
		$conditionsStatus = "status=400 OR status=402";
		$conditionsSelect = "($conditionsNull) AND ($conditionsStatus)";
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
			$this->db->select("ts_in_dt as time_start,wsm_bsm_dt as time_end");
			$this->db->from("ataraxia_main");
			$this->db->where($conditionsSelect);
			$this->db->where("codice_stazione",$station->station_code);
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

		$conditionsNull = "ts_in_dt IS NOT NULL AND ts_in_dt<>'' AND wsm_bsm_dt IS NOT NULL AND wsm_bsm_dt<>''";
		$conditionsStatus = "status=400 OR status=402";
		$conditionsSelect = "($conditionsNull) AND ($conditionsStatus)";

		$minAvgStation = null;
		$maxAvgStation = 0;
		$avgStation = 0;
		$daysStations = 0;
		$howStationPratices = 0;
		$this->db->select("ts_in_dt as time_start,wsm_bsm_dt as time_end");
		$this->db->from("ataraxia_main");
		$this->db->where($conditionsSelect);
		$this->db->where("codice_stazione",$id);
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
		$this->db->select("start_processing as time_start,end_processing as time_end");
		$this->db->from("ataraxia_main");
		$this->db->where($conditionsSelect);
		$this->db->where("codice_stazione",$id);
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

		$arrStations = $this->getStationsUser();

		$conditionsNull = "end_processing IS NOT NULL AND end_processing<>'' AND wsm_bsm_dt IS NOT NULL AND wsm_bsm_dt<>''";
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
			$this->db->select("end_processing as time_start,wsm_bsm_dt as time_end");
			$this->db->from("ataraxia_main");
			$this->db->where($conditionsSelect);
			$this->db->where("codice_stazione",$station->station_code);
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

		$conditionsNull = "end_processing IS NOT NULL AND end_processing<>'' AND wsm_bsm_dt IS NOT NULL AND wsm_bsm_dt<>''";
		$conditionsSelect = "($conditionsNull)";

		$minAvgStation = null;
		$maxAvgStation = 0;
		$avgStation = 0;
		$daysStations = 0;
		$howStationPratices = 0;
		$this->db->select("end_processing as time_start,wsm_bsm_dt as time_end");
		$this->db->from("ataraxia_main");
		$this->db->where($conditionsSelect);
		$this->db->where("codice_stazione",$id);
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
			$this->db->select("ts_in_dt as time_start,irm_dt as time_end");
			$this->db->from("ataraxia_main");
			$this->db->where($conditionsSelect);
			$this->db->where("codice_stazione",$station->station_code);
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

		$arrStations = $this->getStationsUser();

		$conditionsNull = "ts_in_dt IS NOT NULL AND ts_in_dt<>'' AND wsm_bsm_dt IS NOT NULL AND wsm_bsm_dt<>''";
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
			$this->db->select("ts_in_dt as time_start,wsm_bsm_dt as time_end");
			$this->db->from("ataraxia_main");
			$this->db->where($conditionsSelect);
			$this->db->where("codice_stazione",$station->station_code);
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

		$conditionsNull = "ts_in_dt IS NOT NULL AND ts_in_dt<>'' AND wsm_bsm_dt IS NOT NULL AND wsm_bsm_dt<>''";
		$conditionsSelect = "($conditionsNull)";

		$minAvgStation = null;
		$maxAvgStation = 0;
		$avgStation = 0;
		$daysStations = 0;
		$howStationPratices = 0;
		$this->db->select("ts_in_dt as time_start,wsm_bsm_dt as time_end");
		$this->db->from("ataraxia_main");
		$this->db->where($conditionsSelect);
		$this->db->where("codice_stazione",$id);
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

	// ################################### MANUALLY REPORTS ###################################
	public function manuallyIntervento() {
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


}
