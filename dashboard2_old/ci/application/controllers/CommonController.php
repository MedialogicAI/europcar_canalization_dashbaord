<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require("MainController.php");

class CommonController extends MainController {

    public function __construct(){
        parent::__construct(true);
    }

	// ################################### USER STATIONS LIST ###################################
	// GET REGION
	public function getStationsUserList() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = $this->getStationsUser();
		$start = intval($this->input->post("start"));
		$limit = intval($this->input->post("limit"));
		if ($start<0 || $limit<1) {
			$start = $limit = null;
		}

		$arrTemp = $this->getStationsUser();
		$this->sendOutput($arrTemp);
		return;
		$arrStations = $arrTemp['records'];
		$totalCount = $arrTemp['totalCount'];

		$this->sendOutput($arrStations,$totalCount);
		return;
	}

	// ################################### DEFECT CODE ###################################
	// GET REGION
	public function getDefectCode() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();

//		$this->db->select('defect_code');
//		$this->db->distinct();
//		$this->db->from('ataraxia_main');
//		$this->db->order_by('defect_code', 'asc');
//		$arrDefect = $this->db->get()->result();
//
//		foreach($arrDefect as $el) {
//			if (!empty($el->defect_code) && $el->defect_code !=="") {
//				$r = Array(
//					"id" => $el->defect_code,
//					"name" => strtoupper($el->defect_code)
//				);
//				array_push($arrData, $r);
//			}
//		}

		$r = Array(
			"id" => "carrozzeria",
			"name" => strtoupper("CARROZZERIA")
		);
		array_push($arrData, $r);
		$r = Array(
			"id" => "cristalli",
			"name" => strtoupper("CRISTALLI")
		);
		array_push($arrData, $r);
		$r = Array(
			"id" => "gomme",
			"name" => strtoupper("GOMME")
		);
		array_push($arrData, $r);
		$r = Array(
			"id" => "meccanica",
			"name" => strtoupper("MECCANICA")
		);
		array_push($arrData, $r);

		$this->sendOutput($arrData);
		return;
	}


	// ################################### SUPPLIERS ###################################
	// GET SUPPLIERS LIST
	public function getSuppliers() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = Array();

		$v = intval($this->input->post("start"));
		$start = (!empty($v)) ? $v : 1;
		$v = intval($this->input->post("limit"));
		$limit = (!empty($v)) ? $v : 20;

		// set filters
		$blnFilterNetwork = (!is_null($this->input->post("network")) && intval($this->input->post("network"))===1) ? true : false;
		$blnFilterDirect = (!is_null($this->input->post("direct")) && intval($this->input->post("direct"))===1) ? true : false;
		$blnFilterAll = (!$blnFilterNetwork && !$blnFilterDirect) || ($blnFilterNetwork && $blnFilterDirect);

		$this->db->select('id');
		$this->db->from('canaliz_rete');
		$this->db->where("CODICE_FORNITORE <>",NULL);
		$this->db->where("CODICE_FORNITORE <>",'');
		if (!$blnFilterAll) {
			// network
			if ($blnFilterNetwork) {
				$this->db->where("NETWORK <>",NULL);
				$this->db->where("NETWORK <>",'');
				$this->db->where("NETWORK <>","diretti");
			}
			else { // direct
				$this->db->where("NETWORK","diretti");
			}
		}
		$this->db->group_by("CODICE_FORNITORE");
		$this->db->order_by('RAGIONE_SOCIALE','asc');
		$totalCount = $this->db->count_all_results();

		$this->db->select('*');
		$this->db->from('canaliz_rete');
		$this->db->where("CODICE_FORNITORE <>",NULL);
		$this->db->where("CODICE_FORNITORE <>",'');
		if (!$blnFilterAll) {
			// network
			if ($blnFilterNetwork) {
				$this->db->where("NETWORK <>",NULL);
				$this->db->where("NETWORK <>",'');
				$this->db->where("NETWORK <>","diretti");
			}
			else { // direct
				$this->db->where("NETWORK","diretti");
			}
		}
		$this->db->group_by("CODICE_FORNITORE");
		$this->db->order_by('RAGIONE_SOCIALE','asc');
		$this->db->limit($limit,$start);
		$arrData = $this->db->get()->result();

		$this->sendOutput($arrData,$totalCount);
		return;
	}



}
