<?php

use ParagonIE\ConstantTime\Base64UrlSafe;

defined('BASEPATH') OR exit('No direct script access allowed');

require("MainController.php");
require("../src/phpexcel/Classes/PHPExcel.php");

class ExportController extends MainController {

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

    private $csvDelimiter = ";"; // class default csv delimeter

	private $week = Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
	private $weekShort = Array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");
	
	// ################################### EXPORT EXCEL ###################################

	public function exportPraticesExcel() {
    	if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$v = json_decode($this->input->post("dataexport"));
		if (empty($v) || !is_array($v) || count($v)<1) {
			$this->sendError("exportErrorGeneral");
			return;
		}
		$arrDataToExport = $v;
		$pathFileExport= $this->getUploadTempPath();
		$pathReturn = str_replace($this->getBasePath(),"",$pathFileExport);
		$exportFileExt = ".xlsx";
    	$exportFileName = "export-".$this->getRandomString(3,true,true,false);
    	$exportFileName .= time().$this->getRandomString(3,true,true,false);
    	$exportFileName .= $exportFileExt;
		$pathFileExport .= $exportFileName;
		$pathReturn .= $exportFileName;

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->fromArray($arrDataToExport, null, 'A1');
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->setOffice2003Compatibility(true);
		$objWriter->save($pathFileExport);

		$arrData = Array(
			"download" => $pathReturn
		);

		$this->sendSuccess('exportCompleted',$arrData);
		return;
	}

	private function exportPraticesExcelAll($arrDataToExport) {

		$pathFileExport= $this->getUploadTempPath();
		$pathReturn = str_replace($this->getBasePath(),"",$pathFileExport);
		$exportFileExt = ".xlsx";
		$exportFileName = "export-".$this->getRandomString(3,true,true,false);
		$exportFileName .= time().$this->getRandomString(3,true,true,false);
		$exportFileName .= $exportFileExt;
		$pathFileExport .= $exportFileName;
		$pathReturn .= $exportFileName;

		$blnFirst = true;
		foreach ($arrDataToExport as &$row) {
			if (!$blnFirst) {
				foreach ($row as &$col) {
					if (($longDate=DateTime::createFromFormat('Y-m-d H:i:s', $col)) !== FALSE) {
						$strLongDate = date_format($longDate, 'd/m/Y H:i');
						$weekDay = $this->getDayName(date_format($longDate, 'w'));
						$col = "$weekDay $strLongDate";
					}
				}
			}
			else {
				$blnFirst = false;
			}
		}

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->fromArray($arrDataToExport, null, 'A1');
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->setOffice2003Compatibility(true);
		$objWriter->save($pathFileExport);

		$arrData = Array(
			"download" => $pathReturn
		);

		$this->sendSuccess('exportCompleted',$arrData);
		return;
	}


	// ################################### EXPORT CSV ###################################

	public function exportPraticesCsv() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$v = json_decode($this->input->post("dataexport"));
		if (empty($v) || !is_array($v) || count($v)<1) {
			$this->sendError("exportErrorGeneral");
			return;
		}
		$arrDataToExport = $v;
		$pathFileExport= $this->getUploadTempPath();
		$pathReturn = str_replace($this->getBasePath(),"",$pathFileExport);
		$exportFileExt = ".csv";
		$exportFileName = "export-".$this->getRandomString(3,true,true,false);
		$exportFileName .= time().$this->getRandomString(3,true,true,false);
		$exportFileName .= $exportFileExt;
		$pathFileExport .= $exportFileName;
		$pathReturn .= $exportFileName;

		$f = fopen($pathFileExport, "w");
		foreach ($arrDataToExport as $line) {
			fputcsv($f, $line, $this->csvDelimiter);
		}

		$arrData = Array(
			"download" => $pathReturn
		);

		$this->sendSuccess('exportCompleted',$arrData);
		return;

	}

	public function exportPraticesCsvAll($arrDataToExport) {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$pathFileExport= $this->getUploadTempPath();
		$pathReturn = str_replace($this->getBasePath(),"",$pathFileExport);
		$exportFileExt = ".csv";
		$exportFileName = "export-".$this->getRandomString(3,true,true,false);
		$exportFileName .= time().$this->getRandomString(3,true,true,false);
		$exportFileName .= $exportFileExt;
		$pathFileExport .= $exportFileName;
		$pathReturn .= $exportFileName;

		$blnFirst = true;
		foreach ($arrDataToExport as &$row) {
			if (!$blnFirst) {
				foreach ($row as &$col) {
					if (($longDate=DateTime::createFromFormat('Y-m-d H:i:s', $col)) !== FALSE) {
						$strLongDate = date_format($longDate, 'd/m/Y H:i');
						$weekDay = $this->getDayName(date_format($longDate, 'w'));
						$col = "$weekDay $strLongDate";
					}
				}
			}
			else {
				$blnFirst = false;
			}
		}

		$f = fopen($pathFileExport, "w");
		foreach ($arrDataToExport as $line) {
			fputcsv($f, $line, $this->csvDelimiter);
		}

		$arrData = Array(
			"download" => $pathReturn
		);

		$this->sendSuccess('exportCompleted',$arrData);
		return;

	}



	// ######################################## EXPORT ALL PAGES ########################################


	// ################################### DAILY ###################################

	/*
	 * LIST TODAY PRATICES
	 */
	public function getDailyList() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}

		$v = $this->input->post("format");
		$format = (!empty($v)) ? $v : "";
		if ($format==="") {
			$this->sendOutput(Array(),0,false);
			return;
		}

		// COLS TO PRINT
		$arrColsToPrint = json_decode($this->input->post("cols"));
		$selectField = "";
		$blnFirst = true;
		$v = "";
		$arrExportData = Array();
		$rowTitles = Array();
		if (count($arrColsToPrint)>0) {
			foreach($arrColsToPrint as $col) {
				$selectField .= $v.$col->name;
				if ($blnFirst) {
					$blnFirst = false;
					$v = ",";
				}
				array_push($rowTitles, $col->label);
			}
		}
		array_push($arrExportData,$rowTitles);

		// serach params
		$post = $this->input->post("searchtext");
		$searchext = (!empty($post)) ? $post : '';
		$datestart = $this->startDayDate['date_hour'];
		$dateend = $this->endDayDate['date_hour'];
		// select all records, 1 page
		$all = intval($this->input->post("all"));
		// search cfg
		$searchParams = Array(
			"searchtext" => $searchext,
			"datestart" => $datestart,
			"dateend" => $dateend,
			"all" => true,
			"fields" => $selectField
		);

		$arrResultSearch = $this->praticesSearch($searchParams);
		if ($arrResultSearch['totalCount']>0) {
			foreach($arrResultSearch['records'] as $row) {
				$r = Array();
				foreach ($arrColsToPrint as $col) {
					$colName = $col->name;
					$val = $row->$colName;
					$val = (!empty($val)) ? $val : "";
					array_push($r,$val);
				}
				array_push($arrExportData,$r);
			}
			switch($format) {
				case 'excel':
					$this->exportPraticesExcelAll($arrExportData);
					return;
				case 'csv':
					$this->exportPraticesCsvAll($arrExportData);
					return;
				default:
					$this->sendOutput(Array(),0,false);
					return;
			}
		}
		else {
			$this->sendOutput(Array(),0,false);
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
		$v = $this->input->post("format");
		$format = (!empty($v)) ? $v : "";
		if ($format==="") {
			$this->sendOutput(Array(),0,false);
			return;
		}

		// COLS TO PRINT
		$arrColsToPrint = json_decode($this->input->post("cols"));
		$selectField = "";
		$blnFirst = true;
		$v = "";
		$arrExportData = Array();
		$rowTitles = Array();
		if (count($arrColsToPrint)>0) {
			foreach($arrColsToPrint as $col) {
				$selectField .= $v.$col->name;
				if ($blnFirst) {
					$blnFirst = false;
					$v = ",";
				}
				array_push($rowTitles, $col->label);
			}
		}
		array_push($arrExportData,$rowTitles);

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
			"searchtext" => $searchext,
			"datestart" => $datestart,
			"dateend" => $dateend,
			"all" => true,
			"fields" => $selectField
		);
		// list
		$arrResultSearch = $this->praticesSearch($searchParams);
		if ($arrResultSearch['totalCount']>0) {
			foreach($arrResultSearch['records'] as $row) {
				$r = Array();
				foreach ($arrColsToPrint as $col) {
					$colName = $col->name;
					$val = $row->$colName;
					$val = (!empty($val)) ? $val : "";
					array_push($r,$val);
				}
				array_push($arrExportData,$r);
			}
			switch($format) {
				case 'excel':
					$this->exportPraticesExcelAll($arrExportData);
					return;
				case 'csv':
					$this->exportPraticesCsvAll($arrExportData);
					return;
				default:
					$this->sendOutput(Array(),0,false);
					return;
			}
		}
		else {
			$this->sendOutput(Array(),0,false);
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
		$v = $this->input->post("format");
		$format = (!empty($v)) ? $v : "";
		if ($format==="") {
			$this->sendOutput(Array(),0,false);
			return;
		}

		// COLS TO PRINT
		$arrColsToPrint = json_decode($this->input->post("cols"));
		$selectField = "";
		$blnFirst = true;
		$v = "";
		$arrExportData = Array();
		$rowTitles = Array();
		if (count($arrColsToPrint)>0) {
			foreach($arrColsToPrint as $col) {
				$selectField .= $v.$col->name;
				if ($blnFirst) {
					$blnFirst = false;
					$v = ",";
				}
				array_push($rowTitles, $col->label);
			}
		}
		array_push($arrExportData,$rowTitles);

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
			"searchtext" => $searchext,
			"datestart" => $datestart,
			"dateend" => $dateend,
			"status" => Array(400,401,402,403),
			"all" => true,
			"fields" => $selectField
		);
		// list
		$arrResultSearch = $this->praticesSearch($searchParams);
		if ($arrResultSearch['totalCount']>0) {
			foreach($arrResultSearch['records'] as $row) {
				$r = Array();
				foreach ($arrColsToPrint as $col) {
					$colName = $col->name;
					$val = $row->$colName;
					$val = (!empty($val)) ? $val : "";
					array_push($r,$val);
				}
				array_push($arrExportData,$r);
			}
			switch($format) {
				case 'excel':
					$this->exportPraticesExcelAll($arrExportData);
					return;
				case 'csv':
					$this->exportPraticesCsvAll($arrExportData);
					return;
				default:
					$this->sendOutput(Array(),0,false);
					return;
			}
		}
		else {
			$this->sendOutput(Array(),0,false);
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
		$v = $this->input->post("format");
		$format = (!empty($v)) ? $v : "";
		if ($format==="") {
			$this->sendOutput(Array(),0,false);
			return;
		}

		// COLS TO PRINT
		$arrColsToPrint = json_decode($this->input->post("cols"));
		$selectField = "";
		$blnFirst = true;
		$v = "";
		$arrExportData = Array();
		$rowTitles = Array();
		if (count($arrColsToPrint)>0) {
			foreach($arrColsToPrint as $col) {
				$selectField .= $v.$col->name;
				if ($blnFirst) {
					$blnFirst = false;
					$v = ",";
				}
				array_push($rowTitles, $col->label);
			}
		}
		array_push($arrExportData,$rowTitles);
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
			"searchtext" => $searchext,
			"datestart" => $datestart,
			"dateend" => $dateend,
			"status" => Array(490,540,450,570,600,405,560,480),
			"all" => true,
			"fields" => $selectField
		);
		// list
		$arrResultSearch = $this->praticesSearch($searchParams);
		if ($arrResultSearch['totalCount']>0) {
			foreach($arrResultSearch['records'] as $row) {
				$r = Array();
				foreach ($arrColsToPrint as $col) {
					$colName = $col->name;
					$val = $row->$colName;
					$val = (!empty($val)) ? $val : "";
					array_push($r,$val);
				}
				array_push($arrExportData,$r);
			}
			switch($format) {
				case 'excel':
					$this->exportPraticesExcelAll($arrExportData);
					return;
				case 'csv':
					$this->exportPraticesCsvAll($arrExportData);
					return;
				default:
					$this->sendOutput(Array(),0,false);
					return;
			}
		}
		else {
			$this->sendOutput(Array(),0,false);
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
		$v = $this->input->post("format");
		$format = (!empty($v)) ? $v : "";
		if ($format==="") {
			$this->sendOutput(Array(),0,false);
			return;
		}

		// COLS TO PRINT
		$arrColsToPrint = json_decode($this->input->post("cols"));
		$selectField = "";
		$blnFirst = true;
		$v = "";
		$arrExportData = Array();
		$rowTitles = Array();
		if (count($arrColsToPrint)>0) {
			foreach($arrColsToPrint as $col) {
				$selectField .= $v.$col->name;
				if ($blnFirst) {
					$blnFirst = false;
					$v = ",";
				}
				array_push($rowTitles, $col->label);
			}
		}
		array_push($arrExportData,$rowTitles);
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
			"searchtext" => $searchext,
			"datestart" => $datestart,
			"dateend" => $dateend,
			"status" => Array(410,440,460,430,420),
			"all" => true,
			"fields" => $selectField
		);
		// list
		$arrResultSearch = $this->praticesSearch($searchParams);
		if ($arrResultSearch['totalCount']>0) {
			foreach($arrResultSearch['records'] as $row) {
				$r = Array();
				foreach ($arrColsToPrint as $col) {
					$colName = $col->name;
					$val = $row->$colName;
					$val = (!empty($val)) ? $val : "";
					array_push($r,$val);
				}
				array_push($arrExportData,$r);
			}
			switch($format) {
				case 'excel':
					$this->exportPraticesExcelAll($arrExportData);
					return;
				case 'csv':
					$this->exportPraticesCsvAll($arrExportData);
					return;
				default:
					$this->sendOutput(Array(),0,false);
					return;
			}
		}
		else {
			$this->sendOutput(Array(),0,false);
		}
		return;
	}

	// ################################### PICKED / NOT PICKED ###################################

	/*
     * LIST PICKED
     */
	public function getPicked() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$v = $this->input->post("format");
		$format = (!empty($v)) ? $v : "";
		if ($format==="") {
			$this->sendOutput(Array(),0,false);
			return;
		}

		// COLS TO PRINT
		$arrColsToPrint = json_decode($this->input->post("cols"));
		$selectField = "";
		$blnFirst = true;
		$v = "";
		$arrExportData = Array();
		$rowTitles = Array();
		if (count($arrColsToPrint)>0) {
			foreach($arrColsToPrint as $col) {
				$selectField .= $v.$col->name;
				if ($blnFirst) {
					$blnFirst = false;
					$v = ",";
				}
				array_push($rowTitles, $col->label);
			}
		}
		array_push($arrExportData,$rowTitles);
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
			"searchtext" => $searchext,
			"datestart" => $datestart,
			"dateend" => $dateend,
			"all" => $all,
			"status" => Array(402)
		);
		// list
		$arrResultSearch = $this->praticesSearch($searchParams);
		if ($arrResultSearch['totalCount']>0) {
			foreach($arrResultSearch['records'] as $row) {
				$r = Array();
				foreach ($arrColsToPrint as $col) {
					$colName = $col->name;
					$val = $row->$colName;
					$val = (!empty($val)) ? $val : "";
					array_push($r,$val);
				}
				array_push($arrExportData,$r);
			}
			switch($format) {
				case 'excel':
					$this->exportPraticesExcelAll($arrExportData);
					return;
				case 'csv':
					$this->exportPraticesCsvAll($arrExportData);
					return;
				default:
					$this->sendOutput(Array(),0,false);
					return;
			}
		}
		else {
			$this->sendOutput(Array(),0,false);
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
		$v = $this->input->post("format");
		$format = (!empty($v)) ? $v : "";
		if ($format==="") {
			$this->sendOutput(Array(),0,false);
			return;
		}

		// COLS TO PRINT
		$arrColsToPrint = json_decode($this->input->post("cols"));
		$selectField = "";
		$blnFirst = true;
		$v = "";
		$arrExportData = Array();
		$rowTitles = Array();
		if (count($arrColsToPrint)>0) {
			foreach($arrColsToPrint as $col) {
				$selectField .= $v.$col->name;
				if ($blnFirst) {
					$blnFirst = false;
					$v = ",";
				}
				array_push($rowTitles, $col->label);
			}
		}
		array_push($arrExportData,$rowTitles);
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
			"searchtext" => $searchext,
			"datestart" => $datestart,
			"dateend" => $dateend,
			"all" => $all,
			"status" => Array(401)
		);
		// list
		$arrResultSearch = $this->praticesSearch($searchParams);
		if ($arrResultSearch['totalCount']>0) {
			foreach($arrResultSearch['records'] as $row) {
				$r = Array();
				foreach ($arrColsToPrint as $col) {
					$colName = $col->name;
					$val = $row->$colName;
					$val = (!empty($val)) ? $val : "";
					array_push($r,$val);
				}
				array_push($arrExportData,$r);
			}
			switch($format) {
				case 'excel':
					$this->exportPraticesExcelAll($arrExportData);
					return;
				case 'csv':
					$this->exportPraticesCsvAll($arrExportData);
					return;
				default:
					$this->sendOutput(Array(),0,false);
					return;
			}
		}
		else {
			$this->sendOutput(Array(),0,false);
		}
		return;
	}

	/*
     * LIST NOT PICKED
     */
	public function getWaiting() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$v = $this->input->post("format");
		$format = (!empty($v)) ? $v : "";
		if ($format==="") {
			$this->sendOutput(Array(),0,false);
			return;
		}

		// COLS TO PRINT
		$arrColsToPrint = json_decode($this->input->post("cols"));
		$selectField = "";
		$blnFirst = true;
		$v = "";
		$arrExportData = Array();
		$rowTitles = Array();
		if (count($arrColsToPrint)>0) {
			foreach($arrColsToPrint as $col) {
				$selectField .= $v.$col->name;
				if ($blnFirst) {
					$blnFirst = false;
					$v = ",";
				}
				array_push($rowTitles, $col->label);
			}
		}
		array_push($arrExportData,$rowTitles);
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
			foreach($arrResultSearch['records'] as $row) {
				$r = Array();
				foreach ($arrColsToPrint as $col) {
					$colName = $col->name;
					$val = $row->$colName;
					$val = (!empty($val)) ? $val : "";
					array_push($r,$val);
				}
				array_push($arrExportData,$r);
			}
			switch($format) {
				case 'excel':
					$this->exportPraticesExcelAll($arrExportData);
					return;
				case 'csv':
					$this->exportPraticesCsvAll($arrExportData);
					return;
				default:
					$this->sendOutput(Array(),0,false);
					return;
			}
		}
		else {
			$this->sendOutput(Array(),0,false);
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

		$sqlSelectFields = (empty($searchParams['fields'])) ? "*" : $searchParams['fields'];

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
			Array("fieldType" => "int", "fieldName" => "id", "function" => null, "textsearch" => false),
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

		$this->db->select($sqlSelectFields);
		$this->db->from("ataraxia_main");
		$this->db->join("flottatab", "ataraxia_main.targa_veicolo = flottatab.REGISTRATION_NUMBER");

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

		$records = $this->db->get()->result();

		return Array(
			"records" => $records,
			"totalCount" => count($records)
		);

	}

	/*
     * CONVERT DATE TO LONG DATE
     */
	public function getDayName($dayNumber) {
		$dayNumber = (!empty($dayNumber)) ? intval($dayNumber) : -1;
		if ($dayNumber<0 || $dayNumber>6) {
			return "";
		}
		return $this->weekShort[$dayNumber];
	}

}
