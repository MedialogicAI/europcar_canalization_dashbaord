<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require("MainController.php");

class HomeController extends MainController {

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

	public function getMessages() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$this->sendZero();
		return;
		$arrData = Array();


		$r = Array(
			"id" => 1,
			"title" => 'Lorem',
			"body" => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
			"date" => '2020/04/10 15:50:00'
		);
		array_push($arrData,$r);
		$r = Array(
			"id" => 2,
			"title" => 'Mauris',
			"body" => 'Nam aliquam erat vehicula nisi tempus ultrices',
			"date" => '2020/04/09 5:23:57'
		);
		array_push($arrData,$r);
		$r = Array(
			"id" => 3,
			"title" => 'Duis',
			"body" => 'Suspendisse eu metus nec sem cursus convallis',
			"date" => '2020/04/07 2:14:31'
		);
		array_push($arrData,$r);
		$r = Array(
			"id" => 4,
			"title" => 'Lorem',
			"body" => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
			"date" => '2020/04/10 15:50:00'
		);
		array_push($arrData,$r);
		$r = Array(
			"id" => 5,
			"title" => 'Mauris',
			"body" => 'Nam aliquam erat vehicula nisi tempus ultrices',
			"date" => '2020/04/09 5:23:57'
		);
		array_push($arrData,$r);
		$r = Array(
			"id" => 6,
			"title" => 'Duis',
			"body" => 'Suspendisse eu metus nec sem cursus convallis',
			"date" => '2020/04/07 2:14:31'
		);
		array_push($arrData,$r);
		$r = Array(
			"id" => 7,
			"title" => 'Lorem',
			"body" => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
			"date" => '2020/04/10 15:50:00'
		);
		array_push($arrData,$r);
		$r = Array(
			"id" => 8,
			"title" => 'Mauris',
			"body" => 'Nam aliquam erat vehicula nisi tempus ultrices',
			"date" => '2020/04/09 5:23:57'
		);
		array_push($arrData,$r);
		$r = Array(
			"id" => 9,
			"title" => 'Duis',
			"body" => 'Suspendisse eu metus nec sem cursus convallis',
			"date" => '2020/04/07 2:14:31'
		);
		array_push($arrData,$r);


		$this->sendOutput($arrData);
	}

	public function getHomeReports() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();


		// actual cars at the stations
		$arrCarsInStations = $this->getCarsInStations();
		$r = Array("text" => "carsNetwork", "value" => $arrCarsInStations['network']);
		array_push($arrData, $r);
		$r = Array("text" => "carsDirect", "value" => $arrCarsInStations['direct']);
		array_push($arrData, $r);
		$r = Array("text" => "carsTotal", "value" => $arrCarsInStations['total']);
		array_push($arrData, $r);

		// empty
		$r = Array("text" => "empty", "value" => "");
		array_push($arrData, $r);

		// picked
		$searchParams = Array(
			"status" => Array(402)
		);
		$pickedTotal = $this->praticesSearch($searchParams);
		$r = Array("text" => "pickedTotal", "value" => $pickedTotal);
		array_push($arrData, $r);

		// not picked
		$searchParams = Array(
			"status" => Array(401)
		);
		$notPickedTotal = $this->praticesSearch($searchParams);
		$r = Array("text" => "notPickedTotal", "value" => $notPickedTotal);
		array_push($arrData, $r);

		// waiting
		$searchParams = Array(
			"status" => Array(400),
			"wsm_bsm_dt_start" => $this->startLast1HourDate["date_hour"],
			"end_processing" => $this->startLast12HourDate["date_hour"],
			"is_waiting" => true
		);
		$waitingTotal = $this->praticesSearch($searchParams);
		$r = Array("text" => "waitingTotal", "value" => $waitingTotal);
		array_push($arrData, $r);


		$this->sendOutput($arrData);
	}

	private function getCarsInStations_old() {
		$arrUserRegions = $this->getRegionsUser();
		$conditionsRegions = "(1=1)";
		if (!$this->isLoggedManager()) {
			$conditionsRegions = $this->buildSqlConditions($arrUserRegions,"id","canaliz_rete.REGIONE", false);
		}

		// ----- filter network
		$sqlSelect = "SELECT
			DISTINCT movement.id,
			movement.REPAIRER_ID as repairer_id,
			canaliz_canaliz.COD_GWY as gwy,
			canaliz_rete.RAGIONE_SOCIALE as ragione_sociale,
			canaliz_rete.REGIONE as regione,
			canaliz_rete.PROVINCIA as provincia,
			canaliz_rete.CARROZZERIA as carrozzeria,
			canaliz_rete.CRISTALLI as cristalli,
			canaliz_rete.GOMME as gomme,
			canaliz_rete.INTERVENTO_IN_GARANZIA as garanzia,
			canaliz_rete.MECCANICA as meccanica,
			canaliz_rete.TAGLIANDO as tagliando,
			canaliz_rete.WINTER_PROGRAMME_ as winter
			FROM movement
			INNER JOIN canaliz_rete ON movement.REPAIRER_ID = canaliz_rete.CODICE_NETWORK ,
			canaliz_canaliz ";
		$sqlWhereSelect = "WHERE
			(canaliz_rete.CODICE_NETWORK IS NOT NULL AND canaliz_rete.CODICE_NETWORK <> '') AND
			(canaliz_rete.CODICE_FORNITORE IS NOT NULL AND canaliz_rete.CODICE_FORNITORE <> '') AND
			(canaliz_rete.REGIONE IS NOT NULL AND canaliz_rete.REGIONE <> '') AND
			(canaliz_rete.PROVINCIA IS NOT NULL AND canaliz_rete.PROVINCIA <> '') AND
			canaliz_rete.EMAIL IS NOT NULL AND movement.REPAIRER_ID IS NOT NULL AND 
			movement.CHECKOUT_STATION = canaliz_canaliz.COD_GWY ";

		$sqlWhereNetwork = "(

			(canaliz_rete.CARROZZERIA = 'x' AND ((canaliz_canaliz.CARROZZERIE1 = canaliz_rete.EMAIL)
					OR (canaliz_canaliz.CARROZZERIE2 = canaliz_rete.EMAIL) OR (canaliz_canaliz.CARROZZERIE3 = canaliz_rete.EMAIL)
					OR (canaliz_canaliz.CARROZZERIE4 = canaliz_rete.EMAIL)
					OR (canaliz_canaliz.CARROZZERIE5 = canaliz_rete.EMAIL)))
			OR
			((canaliz_rete.MECCANICA = 'x' OR canaliz_rete.TAGLIANDO = 'x') AND ((canaliz_canaliz.OFFICINE1 = canaliz_rete.EMAIL)
					OR (canaliz_canaliz.OFFICINE2 = canaliz_rete.EMAIL) OR (canaliz_canaliz.OFFICINE3 = canaliz_rete.EMAIL)
					OR (canaliz_canaliz.OFFICINE4 = canaliz_rete.EMAIL)
					OR (canaliz_canaliz.OFFICINE5 = canaliz_rete.EMAIL)))
			OR
			((canaliz_rete.GOMME = 'x' OR canaliz_rete.WINTER_PROGRAMME_ = 'x') AND ((canaliz_canaliz.GOMME1 = canaliz_rete.EMAIL) OR (canaliz_canaliz.GOMME2 = canaliz_rete.EMAIL)))
			OR
			(canaliz_rete.CRISTALLI = 'x' AND ((canaliz_canaliz.CRISTALLI = canaliz_rete.EMAIL)))
			
			) ";
		$sqlOrder = "ORDER BY canaliz_rete.REGIONE";
		$sqlWhere = "$sqlWhereSelect AND $sqlWhereNetwork AND $conditionsRegions";
		$sql = "$sqlSelect $sqlWhere $sqlOrder";
		$arrMovements = $this->db->query($sql)->result();

		$totalCarsNetwork = count($arrMovements);

		// ----- filter direct
		$sqlSelect = "SELECT
			DISTINCT movement.id,
			movement.REPAIRER_ID as repairer_id,
			canaliz_canaliz.COD_GWY as gwy,
			canaliz_rete.RAGIONE_SOCIALE as ragione_sociale,
			canaliz_rete.REGIONE as regione,
			canaliz_rete.PROVINCIA as provincia,
			canaliz_rete.CARROZZERIA as carrozzeria,
			canaliz_rete.CRISTALLI as cristalli,
			canaliz_rete.GOMME as gomme,
			canaliz_rete.INTERVENTO_IN_GARANZIA as garanzia,
			canaliz_rete.MECCANICA as meccanica,
			canaliz_rete.TAGLIANDO as tagliando,
			canaliz_rete.WINTER_PROGRAMME_ as winter
			FROM movement
			INNER JOIN canaliz_rete ON movement.REPAIRER_ID = canaliz_rete.CODICE_FORNITORE ,
			canaliz_canaliz ";
		$sqlWhereSelect = "WHERE
			(canaliz_rete.CODICE_NETWORK IS NULL OR canaliz_rete.CODICE_NETWORK = '') AND
			(canaliz_rete.CODICE_FORNITORE IS NOT NULL AND canaliz_rete.CODICE_FORNITORE <> '') AND
			canaliz_rete.EMAIL IS NOT NULL AND movement.REPAIRER_ID IS NOT NULL AND 
			(canaliz_rete.REGIONE IS NOT NULL AND canaliz_rete.REGIONE <> '') AND
			(canaliz_rete.PROVINCIA IS NOT NULL AND canaliz_rete.PROVINCIA <> '') AND 
			movement.CHECKOUT_STATION = canaliz_canaliz.COD_GWY ";

		$sqlWhereDirect = "(

			(canaliz_rete.CARROZZERIA = 'x' AND ((canaliz_canaliz.CARROZZERIE1 = canaliz_rete.EMAIL)
					OR (canaliz_canaliz.CARROZZERIE2 = canaliz_rete.EMAIL) OR (canaliz_canaliz.CARROZZERIE3 = canaliz_rete.EMAIL)
					OR (canaliz_canaliz.CARROZZERIE4 = canaliz_rete.EMAIL)
					OR (canaliz_canaliz.CARROZZERIE5 = canaliz_rete.EMAIL)))
			OR
			((canaliz_rete.MECCANICA = 'x' OR canaliz_rete.TAGLIANDO = 'x') AND ((canaliz_canaliz.OFFICINE1 = canaliz_rete.EMAIL)
					OR (canaliz_canaliz.OFFICINE2 = canaliz_rete.EMAIL) OR (canaliz_canaliz.OFFICINE3 = canaliz_rete.EMAIL)
					OR (canaliz_canaliz.OFFICINE4 = canaliz_rete.EMAIL)
					OR (canaliz_canaliz.OFFICINE5 = canaliz_rete.EMAIL)))
			OR
			((canaliz_rete.GOMME = 'x' OR canaliz_rete.WINTER_PROGRAMME_ = 'x') AND ((canaliz_canaliz.GOMME1 = canaliz_rete.EMAIL) OR (canaliz_canaliz.GOMME2 = canaliz_rete.EMAIL)))
			OR
			(canaliz_rete.CRISTALLI = 'x' AND ((canaliz_canaliz.CRISTALLI = canaliz_rete.EMAIL)))
			
			)";
		$sqlOrder = "ORDER BY canaliz_rete.REGIONE";
		$sqlWhere = "$sqlWhereSelect AND $sqlWhereDirect AND $conditionsRegions";
		$sql = "$sqlSelect $sqlWhere $sqlOrder";
		$arrMovements = $this->db->query($sql)->result();
		$totalCarsDirect = count($arrMovements);
		return Array(
			"network" => $totalCarsNetwork,
			"direct" => $totalCarsDirect,
			"total" => ($totalCarsNetwork + $totalCarsDirect)
		);
	}

	private function getCarsInStations()
	{
		$arrUserRegions = $this->getRegionsUser();
		$conditionsRegions = "(1=1)";
		if (!$this->isLoggedManager()) {
			$conditionsRegions = $this->buildSqlConditions($arrUserRegions, "id", "canaliz_rete.REGIONE", false);
		}

		// Get dynamic column conditions for each service type
		$carrozzerieCond = $this->buildDynamicServiceCondition('CARROZZERIE', 'CARROZZERIA');
		$officineCond = $this->buildDynamicServiceCondition('OFFICINE', 'MECCANICA', 'TAGLIANDO');
		$gommeCond = $this->buildDynamicServiceCondition('GOMME', 'GOMME', 'WINTER_PROGRAMME_');
		$cristalliCond = $this->buildDynamicServiceCondition('CRISTALLI', 'CRISTALLI');

		// ----- filter network
		$sqlSelect = "SELECT
			DISTINCT movement.id,
			movement.REPAIRER_ID as repairer_id,
			canaliz_canaliz.COD_GWY as gwy,
			canaliz_rete.RAGIONE_SOCIALE as ragione_sociale,
			canaliz_rete.REGIONE as regione,
			canaliz_rete.PROVINCIA as provincia,
			canaliz_rete.CARROZZERIA as carrozzeria,
			canaliz_rete.CRISTALLI as cristalli,
			canaliz_rete.GOMME as gomme,
			canaliz_rete.INTERVENTO_IN_GARANZIA as garanzia,
			canaliz_rete.MECCANICA as meccanica,
			canaliz_rete.TAGLIANDO as tagliando,
			canaliz_rete.WINTER_PROGRAMME_ as winter
			FROM movement
			INNER JOIN canaliz_rete ON movement.REPAIRER_ID = canaliz_rete.CODICE_NETWORK ,
			canaliz_canaliz ";
		$sqlWhereSelect = "WHERE
			(canaliz_rete.CODICE_NETWORK IS NOT NULL AND canaliz_rete.CODICE_NETWORK <> '') AND
			(canaliz_rete.CODICE_FORNITORE IS NOT NULL AND canaliz_rete.CODICE_FORNITORE <> '') AND
			(canaliz_rete.REGIONE IS NOT NULL AND canaliz_rete.REGIONE <> '') AND
			(canaliz_rete.PROVINCIA IS NOT NULL AND canaliz_rete.PROVINCIA <> '') AND
			canaliz_rete.EMAIL IS NOT NULL AND movement.REPAIRER_ID IS NOT NULL AND 
			movement.CHECKOUT_STATION = canaliz_canaliz.COD_GWY ";

		$sqlWhereNetwork = "($carrozzerieCond OR $officineCond OR $gommeCond OR $cristalliCond)";
		$sqlOrder = "ORDER BY canaliz_rete.REGIONE";
		$sqlWhere = "$sqlWhereSelect AND $sqlWhereNetwork AND $conditionsRegions";
		$sql = "$sqlSelect $sqlWhere $sqlOrder";
		$arrMovements = $this->db->query($sql)->result();

		$totalCarsNetwork = count($arrMovements);

		// ----- filter direct
		$sqlSelect = "SELECT
			DISTINCT movement.id,
			movement.REPAIRER_ID as repairer_id,
			canaliz_canaliz.COD_GWY as gwy,
			canaliz_rete.RAGIONE_SOCIALE as ragione_sociale,
			canaliz_rete.REGIONE as regione,
			canaliz_rete.PROVINCIA as provincia,
			canaliz_rete.CARROZZERIA as carrozzeria,
			canaliz_rete.CRISTALLI as cristalli,
			canaliz_rete.GOMME as gomme,
			canaliz_rete.INTERVENTO_IN_GARANZIA as garanzia,
			canaliz_rete.MECCANICA as meccanica,
			canaliz_rete.TAGLIANDO as tagliando,
			canaliz_rete.WINTER_PROGRAMME_ as winter
			FROM movement
			INNER JOIN canaliz_rete ON movement.REPAIRER_ID = canaliz_rete.CODICE_FORNITORE ,
			canaliz_canaliz ";
		$sqlWhereSelect = "WHERE
			(canaliz_rete.CODICE_NETWORK IS NULL OR canaliz_rete.CODICE_NETWORK = '') AND
			(canaliz_rete.CODICE_FORNITORE IS NOT NULL AND canaliz_rete.CODICE_FORNITORE <> '') AND
			canaliz_rete.EMAIL IS NOT NULL AND movement.REPAIRER_ID IS NOT NULL AND 
			(canaliz_rete.REGIONE IS NOT NULL AND canaliz_rete.REGIONE <> '') AND
			(canaliz_rete.PROVINCIA IS NOT NULL AND canaliz_rete.PROVINCIA <> '') AND 
			movement.CHECKOUT_STATION = canaliz_canaliz.COD_GWY ";

		$sqlWhereDirect = "($carrozzerieCond OR $officineCond OR $gommeCond OR $cristalliCond)";
		$sqlOrder = "ORDER BY canaliz_rete.REGIONE";
		$sqlWhere = "$sqlWhereSelect AND $sqlWhereDirect AND $conditionsRegions";
		$sql = "$sqlSelect $sqlWhere $sqlOrder";
		$arrMovements = $this->db->query($sql)->result();
		$totalCarsDirect = count($arrMovements);

		return array(
			"network" => $totalCarsNetwork,
			"direct" => $totalCarsDirect,
			"total" => ($totalCarsNetwork + $totalCarsDirect)
		);
	}

	private function buildDynamicServiceCondition($columnPrefix, $serviceType1, $serviceType2 = null)
	{
		$availableColumns = $this->getAvailableServiceColumns($columnPrefix);

		if (empty($availableColumns)) {
			return "1=0"; 
		}

		$columnConditions = array();
		foreach ($availableColumns as $column) {
			$columnConditions[] = "(canaliz_canaliz.$column = canaliz_rete.EMAIL)";
		}
		$emailConditions = "(" . implode(" OR ", $columnConditions) . ")";

		$serviceCondition = "(canaliz_rete.$serviceType1 = 'x'";
		if ($serviceType2) {
			$serviceCondition .= " OR canaliz_rete.$serviceType2 = 'x'";
		}
		$serviceCondition .= ")";

		return "($serviceCondition AND $emailConditions)";
	}

	private function getAvailableServiceColumns($columnPrefix)
	{
		static $columnCache = array();

		if (!isset($columnCache[$columnPrefix])) {
			$sql = "SHOW COLUMNS FROM canaliz_canaliz LIKE '{$columnPrefix}%'";
			$result = $this->db->query($sql)->result();

			$columns = array();
			foreach ($result as $row) {
				$columns[] = $row->Field;
			}

			$columnCache[$columnPrefix] = $columns;
		}

		return $columnCache[$columnPrefix];
	}

	private function praticesSearch($searchParams=Array()) {
		if (empty($searchParams)) {
			return Array();
		}

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
			Array("fieldType" => "string", "fieldName" => "ataraxia_main.codice_stazione", "function" => "UPPER", "textsearch" => true),
			Array("fieldType" => "date", "fieldName" => "ataraxia_main.ts_in_dt", "function" => null, "textsearch" => false),
			Array("fieldType" => "date", "fieldName" => "ataraxia_main.start_processing", "function" => null, "textsearch" => false),
			Array("fieldType" => "date", "fieldName" => "ataraxia_main.end_processing", "function" => null, "textsearch" => false),
			Array("fieldType" => "string", "fieldName" => "ataraxia_main.targa_veicolo", "function" => "UPPER", "textsearch" => true),
			Array("fieldType" => "string", "fieldName" => "ataraxia_main.observations", "function" => "TRIM", "textsearch" => true),
			Array("fieldType" => "string", "fieldName" => "ataraxia_main.atar_file", "function" => null, "textsearch" => true),
			Array("fieldType" => "int", "fieldName" => "ataraxia_main.status", "function" => null, "textsearch" => false),
			Array("fieldType" => "string", "fieldName" => "ataraxia_main.defect_code", "function" => "TRIM", "textsearch" => true),
			Array("fieldType" => "string", "fieldName" => "flottatab.ACTIVITY", "function" => null, "textsearch" => false),
			Array("fieldType" => "string", "fieldName" => "flottatab.MAKE_DESCRIPTION", "function" => null, "textsearch" => false),
			Array("fieldType" => "string", "fieldName" => "flottatab.MODEL_INTL_DESCRIPTION", "function" => null, "textsearch" => false),
			Array("fieldType" => "string", "fieldName" => "canaliz_total.REGIONE", "function" => null, "textsearch" => false),
			Array("fieldType" => "string", "fieldName" => "ataraxia_main.autofficina_name", "function" => "TRIM", "textsearch" => true),
			Array("fieldType" => "string", "fieldName" => "ataraxia_main.email_inviata_autofficina", "function" => "TRIM", "textsearch" => true)
		);

		// get total for paging
		$selectFieldsTotal = $this->buildSelectFields($arrFields,false);
		$this->db->select($selectFieldsTotal);
		$this->db->from("ataraxia_main");
		$this->db->join("flottatab", "ataraxia_main.targa_veicolo = flottatab.REGISTRATION_NUMBER", "left");
		$this->db->join("canaliz_total", "ataraxia_main.codice_stazione = canaliz_total.MAIN_COD_GWY_");

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
		return $totalCount;
	}

}
