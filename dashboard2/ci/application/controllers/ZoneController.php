<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require("MainController.php");

class ZoneController extends MainController {

    public function __construct(){
        parent::__construct(true);
    }

	// ################################### REGIONS ###################################
	// GET REGION
	public function getRegions() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrData = $this->getRegionsUser();
		$this->sendOutput($arrData);
		return;
//
//
//		$network = $this->input->post("network");
//		$blnNetwork = (!empty($network) && intval($network)===1) ? true : false;
//		$direct = $this->input->post("direct");
//		$blnDirect = (!empty($direct) && intval($direct)===1) ? true : false;
//		if (!$blnNetwork && !$blnDirect) {
//			$this->sendZero();
//			return;
//		}
//
//		$arrData = Array();
//		$arrValues = Array();
//		$arrDirect = Array();
//		$arrNetwork = Array();
//
//		// direct
//		if ($blnDirect) {
//			$this->db->select('REGIONE as region');
//			$this->db->distinct();
//			$this->db->from('canaliz_rete');
//			$this->db->where('NETWORK','diretti');
//			$this->db->order_by('REGIONE','asc');
//			$arrDirect = $this->db->get()->result();
//		}
//
//		// network
//		if ($blnNetwork) {
//			$this->db->select('REGIONE as region');
//			$this->db->distinct();
//			$this->db->from('canaliz_total');
//			$this->db->order_by('REGIONE', 'asc');
//			$arrNetwork = $this->db->get()->result();
//		}
//
//		foreach($arrNetwork as $el) {
//			if (!empty($el->region)) {
//				$value = $el->region;
//				array_push($arrValues,$value);
//				$region = strtoupper($el->region);
//				$r = Array(
//					"id" => $value,
//					"name" => $region,
//					"checked" => false
//				);
//				array_push($arrData,$r);
//			}
//		}
//		foreach($arrDirect as $el) {
//			if (!empty($el->region)) {
//				$value = $el->region;
//				if (array_search(strtolower($value), array_map('strtolower', $arrValues))===FALSE) {
//					$region = strtoupper($el->region);
//					$r = Array(
//						"id" => $value,
//						"name" => $region,
//						"checked" => false
//					);
//					array_push($arrData, $r);
//				}
//			}
//		}
//
//		sort($arrData);
//		$this->sendOutput($arrData);
//		return;
	}

	// ################################### PROVINCES ###################################
	// GET PROVINCES
	public function getProvinces() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}
		$arrRegions = Array();
		$arrRegionsPost = json_decode($this->input->post("regions"));
		if (empty($arrRegionsPost)) {
			$arrRegions = $this->getRegionsUser();
			return;
		}
		else {
			foreach ($arrRegionsPost as $el) {
				$d = Array(
					"id" => $el->id
				);
				array_push($arrRegions,$d);
			}
		}
		$arrProvinces = $this->getProvincesByRegion($arrRegions,false);
		$this->sendOutput($arrProvinces);
		return;



		$network = $this->input->post("network");
		$blnNetwork = (!empty($network) && intval($network)===1) ? true : false;
		$direct = $this->input->post("direct");
		$blnDirect = (!empty($direct) && intval($direct)===1) ? true : false;
		if (!$blnNetwork && !$blnDirect) {
			$this->sendZero();
			return;
		}

		$arrRegions = json_decode($this->input->post("regions"));
		if (empty($arrRegions)) {
			$this->sendZero();
			return;
		}
		$arrData = Array();
		$arrValues = Array();
		$arrDirect = Array();
		$arrNetwork = Array();

		// direct
		if ($blnDirect) {
			$sqlSelect = "SELECT DISTINCT PROVINCIA as province FROM canaliz_rete";
			$or = "";
			$blnFirst = true;
			$conditions = "";
			foreach ($arrRegions as $el) {
				$region = str_replace("'", "''", $el->id);
				$conditions .= $or . " REGIONE='$region'";
				if ($blnFirst) {
					$blnFirst = false;
					$or = ' OR ';
				}
			}
			if ($conditions == "") {
				$conditions = "(1=1)";
			}
			$order = "ORDER BY PROVINCIA";
			$conditions = "(NETWORK = 'diretti') AND ($conditions)";
			$sql = "$sqlSelect where $conditions $order";
			$arrDirect = $this->db->query($sql)->result();
		}

		// network
		if ($blnNetwork) {
			$sqlSelect = "SELECT DISTINCT `PROV.` as province FROM canaliz_total";
			$or = "";
			$blnFirst = true;
			$conditions = "";
			foreach ($arrRegions as $el) {
				$region = str_replace("'", "''", $el->id);
				$conditions .= $or . " REGIONE='$region'";
				if ($blnFirst) {
					$blnFirst = false;
					$or = ' OR ';
				}
			}
			if ($conditions == "") {
				$conditions = "(1=1)";
			}
			$order = "ORDER BY `PROV.`";
			$sql = "$sqlSelect where $conditions $order";
			$arrNetwork = $this->db->query($sql)->result();
		}

		foreach($arrNetwork as $el) {
			if (!empty($el->province)) {
				$value = $el->province;
				array_push($arrValues,$value);
				$province = strtoupper($el->province);
				$r = Array(
					"id" => $value,
					"name" => $province,
					"checked" => false
				);
				array_push($arrData,$r);
			}
		}
		foreach($arrDirect as $el) {
			if (!empty($el->province)) {
				$value = $el->province;
				if (array_search(strtolower($value), array_map('strtolower', $arrValues)) === FALSE) {
					$province = strtoupper($el->province);
					$r = Array(
						"id" => $value,
						"name" => $province,
						"checked" => false
					);
					array_push($arrData, $r);
				}
			}
		}
		sort($arrData);
		$this->sendOutput($arrData);
		return;
	}




}
