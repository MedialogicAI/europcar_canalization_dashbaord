<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require("MainController.php");

class AvailabilityController extends MainController {

    public function __construct(){
        parent::__construct(true);
    }
    
    // default availability
	private $defaultNetworkAvailability = 2;
	private $defaultDirectAvailability = 2;

	// ################################### CHART DATA ###################################
	// GET CHART DATA
	public function getData() {
		if (!$this->isLogged()) {
			$this->sendNotAuthorized();
			return;
		}

		$maxCharLabel = 40; // truncate long name
		$recordIdAuto = 0;
		$arrData = Array();
		// set filters
		$blnFilterDirect = (!is_null($this->input->post("direct")) && intval($this->input->post("direct"))===1) ? true : false;
		$blnFilterNetwork = (!is_null($this->input->post("network")) && intval($this->input->post("network"))===1) ? true : false;
		$defect = strtolower($this->input->post("defect"));
		$blnFilterDefect = (!empty($defect)) ? true : false;
		$arrProvinces = json_decode($this->input->post("provinces"));
		$blnFilterProvinces = (is_array($arrProvinces) && count($arrProvinces)>0) ? true : false;
		$arrRegions = json_decode($this->input->post("regions"));
		$blnFilterRegions = (!empty($arrRegions) && is_array($arrRegions) && count($arrRegions)>0 && !$blnFilterProvinces) ? true : false;
		// no data
		if (!$blnFilterDirect && !$blnFilterNetwork) {
			$this->sendZero();
			return;
		}

		// ################################## START AVAILABILITY SEARCH ##################################
		$sqlWhereDefect = "(1=1)";
		if ($blnFilterDefect)	 {
			switch($defect) {
				case "carrozzeria":
					$fldDefect = "CARROZZERIA";
					break;
				case "cristalli":
					$fldDefect = "CRISTALLI";
					break;
				case "gomme":
					$fldDefect = "GOMME";
					break;
				case "meccanica":
					$fldDefect = "MECCANICA";
					break;
				default:
					$blnFilterDefect = false;
					break;
			}
			$sqlWhereDefect = ($blnFilterDefect) ? "(canaliz_rete.$fldDefect = 'x')" : "(1=1)";
		}

		// ----- show all regions (no filters)
		if (!$blnFilterRegions && !$blnFilterProvinces) {
			$arrUserRegions = $this->getRegionsUser();
			$conditionsRegions = "(1=1)";
			if (!$this->isLoggedManager() || $blnFilterRegions) {
				$conditionsRegions = $this->buildSqlConditions($arrUserRegions,"id","canaliz_rete.REGIONE", false);
			}

			// ----- filter network
			if ($blnFilterNetwork) {
				$arrIndexRegionsNetwork = Array();
				$arrAssignedRegion = Array();
				$i = 0;
				foreach ($arrUserRegions as $el) {
					$arrIndexRegionsNetwork[$i] = $el['id'];
					$r = Array(
						"assigned" => 0,
						"availability" => $this->getCapacityRegionNetwork($el['id'],$sqlWhereDefect),
						"region" => $el['id']
					);
					array_push($arrAssignedRegion,$r);
					$i++;
				}
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
				(canaliz_rete.NETWORK <> 'diretti') AND 
				(canaliz_rete.CODICE_NETWORK IS NOT NULL AND canaliz_rete.CODICE_NETWORK <> '') AND
				(canaliz_rete.CODICE_FORNITORE IS NOT NULL AND canaliz_rete.CODICE_FORNITORE <> '') AND
				(canaliz_rete.REGIONE IS NOT NULL AND canaliz_rete.REGIONE <> '') AND
				(canaliz_rete.PROVINCIA IS NOT NULL AND canaliz_rete.PROVINCIA <> '') AND
				canaliz_rete.EMAIL IS NOT NULL AND movement.REPAIRER_ID IS NOT NULL AND 
				movement.CHECKOUT_STATION = canaliz_canaliz.COD_GWY ";

				$sqlWhereNetwork = "(
				(canaliz_rete.CARROZZERIA = 'x' AND ((canaliz_canaliz.CARROZZERIE1 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.CARROZZERIE2 = canaliz_rete.EMAIL) OR (canaliz_canaliz.CARROZZERIE3 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.CARROZZERIE4 = canaliz_rete.EMAIL)))
				OR
				((canaliz_rete.MECCANICA = 'x' OR canaliz_rete.TAGLIANDO = 'x') AND ((canaliz_canaliz.OFFICINE1 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.OFFICINE2 = canaliz_rete.EMAIL) OR (canaliz_canaliz.OFFICINE3 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.OFFICINE4 = canaliz_rete.EMAIL)))
				OR
				((canaliz_rete.GOMME = 'x' OR canaliz_rete.WINTER_PROGRAMME_ = 'x') AND ((canaliz_canaliz.GOMME1 = canaliz_rete.EMAIL) OR (canaliz_canaliz.GOMME2 = canaliz_rete.EMAIL)))
				OR
				(canaliz_rete.CRISTALLI = 'x' AND ((canaliz_canaliz.CRISTALLI = canaliz_rete.EMAIL)))
				
				) ";
				$sqlOrder = "GROUP BY movement.id ORDER BY canaliz_rete.REGIONE";
				$sqlWhere = "$sqlWhereSelect AND $sqlWhereNetwork AND $conditionsRegions AND $sqlWhereDefect";
				$sql = "$sqlSelect $sqlWhere $sqlOrder";
				$arrMovements = $this->db->query($sql)->result();
				$lastElement = '';
				$arrPresentAdded = Array();
				foreach($arrMovements as $movement) {
					$indexRegion = array_search(strtolower($movement->regione), array_map('strtolower', $arrIndexRegionsNetwork));
					$arrAssignedRegion[$indexRegion]['assigned']++;
				}

				foreach ($arrAssignedRegion as $el) {
					$assigned = $el["assigned"];
					$availability = $el["availability"] = ($el["availability"]>0) ? $el["availability"] : $this->defaultNetworkAvailability;
					$recordName = $recordNameLong = trim($el["region"]);
					$recordName = (strlen($recordName)>$maxCharLabel) ? substr($recordName,0,$maxCharLabel)."..." : $recordName;
					$el["is_direct"] = false;
					$el["is_network"] = true;
					$r = Array(
						"id" => ++$recordIdAuto,
						"availability" => $availability,
						"assigned" => $assigned,
						"name" => "$recordIdAuto - ".strtoupper($recordName),
						"namelong" => $recordNameLong,
						"record" => $el,
						"is_station" => false
					);

					array_push($arrData,$r);
				}
			}

			// ----- end filter network
			
			// ----- filter direct
			if ($blnFilterDirect) {
				$arrIndexRegionsDirect = Array();
				$arrAssignedRegion = Array();
				$i = 0;
				foreach ($arrUserRegions as $el) {
					$arrIndexRegionsDirect[$i] = $el['id'];
					$r = Array(
						"assigned" => 0,
						"availability" => $this->getCapacityRegionDirect($el['id'],$sqlWhereDefect),
						"region" => $el['id']
					);
					array_push($arrAssignedRegion,$r);
					$i++;
				}
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
				(canaliz_rete.NETWORK = 'diretti') AND
				(canaliz_rete.CODICE_NETWORK IS NULL OR canaliz_rete.CODICE_NETWORK = '') AND
				(canaliz_rete.CODICE_FORNITORE IS NOT NULL AND canaliz_rete.CODICE_FORNITORE <> '') AND
				canaliz_rete.EMAIL IS NOT NULL AND movement.REPAIRER_ID IS NOT NULL AND 
				(canaliz_rete.REGIONE IS NOT NULL AND canaliz_rete.REGIONE <> '') AND
				(canaliz_rete.PROVINCIA IS NOT NULL AND canaliz_rete.PROVINCIA <> '') AND 
				movement.CHECKOUT_STATION = canaliz_canaliz.COD_GWY ";

				$sqlWhereDirect = "(
				(canaliz_rete.CARROZZERIA = 'x' AND ((canaliz_canaliz.CARROZZERIE1 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.CARROZZERIE2 = canaliz_rete.EMAIL) OR (canaliz_canaliz.CARROZZERIE3 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.CARROZZERIE4 = canaliz_rete.EMAIL)))
				OR
				((canaliz_rete.MECCANICA = 'x' OR canaliz_rete.TAGLIANDO = 'x') AND ((canaliz_canaliz.OFFICINE1 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.OFFICINE2 = canaliz_rete.EMAIL) OR (canaliz_canaliz.OFFICINE3 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.OFFICINE4 = canaliz_rete.EMAIL)))
				OR
				((canaliz_rete.GOMME = 'x' OR canaliz_rete.WINTER_PROGRAMME_ = 'x') AND ((canaliz_canaliz.GOMME1 = canaliz_rete.EMAIL) OR (canaliz_canaliz.GOMME2 = canaliz_rete.EMAIL)))
				OR
				(canaliz_rete.CRISTALLI = 'x' AND ((canaliz_canaliz.CRISTALLI = canaliz_rete.EMAIL)))
				
				)";
				$sqlOrder = "GROUP BY movement.id ORDER BY canaliz_rete.REGIONE";
				$sqlWhere = "$sqlWhereSelect AND $sqlWhereDirect AND $conditionsRegions AND $sqlWhereDefect";
				$sql = "$sqlSelect $sqlWhere $sqlOrder";
				$arrMovements = $this->db->query($sql)->result();
				$lastElement = '';
				foreach($arrMovements as $movement) {
					$indexRegion = array_search(strtolower($movement->regione), array_map('strtolower', $arrIndexRegionsDirect));
					$arrAssignedRegion[$indexRegion]['assigned']++;
				}
				foreach ($arrAssignedRegion as $el) {
					$name = $el["region"];
					$assigned = $el["assigned"];
					$availability = $el["availability"] = ($el["availability"]>0) ? $el["availability"] : $this->defaultDirectAvailability;
					$recordName = $recordNameLong = trim($el["region"]);
					$recordName = (strlen($recordName)>$maxCharLabel) ? substr($recordName,0,$maxCharLabel)."..." : $recordName;
					$el["is_direct"] = true;
					$el["is_network"] = false;
					if ($blnFilterNetwork) {
						$recordName = "* $recordName";
					}
					$r = Array(
						"id" => ++$recordIdAuto,
						"availability" => $availability,
						"assigned" => $assigned,
						"name" => "$recordIdAuto - ".strtoupper($recordName),
						"namelong" => $recordNameLong,
						"record" => $el,
						"is_station" => false
					);
					array_push($arrData,$r);
				}
			}
			// ----- end filter direct
		} // end show all regions (no filters)


		// show provinces
		if ($blnFilterRegions) {
			$arrUserProvinces = $this->getProvincesByRegion($arrRegions);
			$conditionsProvinces = $this->buildSqlConditions($arrUserProvinces,"id","canaliz_rete.PROVINCIA", false);

			// ----- filter network
			if ($blnFilterNetwork) {
				$arrIndexProvincesNetwork = Array();
				$arrAssignedProvince = Array();
				$i = 0;
				foreach ($arrUserProvinces as $el) {
					$arrIndexProvincesNetwork[$i] = $el['id'];
					$r = Array(
						"assigned" => 0,
						"availability" => $this->getCapacityProvinceNetwork($el['id'],$sqlWhereDefect),
						"province" => $el['id']
					);
					array_push($arrAssignedProvince,$r);
					$i++;
				}
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
				(canaliz_rete.NETWORK <> 'diretti') AND 
				(canaliz_rete.CODICE_NETWORK IS NOT NULL AND canaliz_rete.CODICE_NETWORK <> '') AND
				(canaliz_rete.REGIONE IS NOT NULL AND canaliz_rete.REGIONE <> '') AND
				(canaliz_rete.PROVINCIA IS NOT NULL AND canaliz_rete.PROVINCIA <> '') AND
				 
				canaliz_rete.EMAIL IS NOT NULL AND movement.REPAIRER_ID IS NOT NULL AND 
				movement.CHECKOUT_STATION = canaliz_canaliz.COD_GWY ";
				$sqlWhereNetwork = "(

				(canaliz_rete.CARROZZERIA = 'x' AND ((canaliz_canaliz.CARROZZERIE1 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.CARROZZERIE2 = canaliz_rete.EMAIL) OR (canaliz_canaliz.CARROZZERIE3 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.CARROZZERIE4 = canaliz_rete.EMAIL)))
				OR
				((canaliz_rete.MECCANICA = 'x' OR canaliz_rete.TAGLIANDO = 'x') AND ((canaliz_canaliz.OFFICINE1 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.OFFICINE2 = canaliz_rete.EMAIL) OR (canaliz_canaliz.OFFICINE3 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.OFFICINE4 = canaliz_rete.EMAIL)))
				OR
				((canaliz_rete.GOMME = 'x' OR canaliz_rete.WINTER_PROGRAMME_ = 'x') AND ((canaliz_canaliz.GOMME1 = canaliz_rete.EMAIL) OR (canaliz_canaliz.GOMME2 = canaliz_rete.EMAIL)))
				OR
				(canaliz_rete.CRISTALLI = 'x' AND ((canaliz_canaliz.CRISTALLI = canaliz_rete.EMAIL)))
				
				) ";
				$sqlOrder = "GROUP BY movement.id ORDER BY canaliz_rete.PROVINCIA";
				$sqlWhere = "$sqlWhereSelect AND $sqlWhereNetwork AND $conditionsProvinces AND $sqlWhereDefect";
				$sql = "$sqlSelect $sqlWhere $sqlOrder";

				$arrMovements = $this->db->query($sql)->result();
				$lastElement = '';
				foreach($arrMovements as $movement) {
					$indexProvince = array_search(strtolower($movement->provincia), array_map('strtolower', $arrIndexProvincesNetwork));
					$arrAssignedProvince[$indexProvince]['assigned']++;
				}

				foreach ($arrAssignedProvince as $el) {
					$assigned = $el["assigned"];
					$availability = $el["availability"] = ($el["availability"]>0) ? $el["availability"] : $this->defaultNetworkAvailability;
					$recordName = $recordNameLong = trim($el["province"]);
					$recordName = (strlen($recordName)>$maxCharLabel) ? substr($recordName,0,$maxCharLabel)."..." : $recordName;
					$el["is_direct"] = false;
					$el["is_network"] = true;
					$r = Array(
						"id" => ++$recordIdAuto,
						"availability" => $availability,
						"assigned" => $assigned,
						"name" => "$recordIdAuto - ".strtoupper($recordName),
						"namelong" => $recordNameLong,
						"record" => $el,
						"is_station" => false
					);
					array_push($arrData,$r);
				}
			}
			// ----- end filter network

			// ----- filter direct
			if ($blnFilterDirect) {
				$arrIndexProvincesDirect = Array();
				$arrAssignedProvince = Array();
				$i = 0;
				foreach ($arrUserProvinces as $el) {
					$arrIndexProvincesDirect[$i] = $el['id'];
					$r = Array(
						"assigned" => 0,
						"availability" => $this->getCapacityProvinceDirect($el['id'],$sqlWhereDefect),
						"province" => $el['id']
					);
					array_push($arrAssignedProvince,$r);
					$i++;
				}

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
				(canaliz_rete.NETWORK = 'diretti') AND 
				(canaliz_rete.CODICE_FORNITORE IS NOT NULL AND canaliz_rete.CODICE_FORNITORE <> '') AND
				(canaliz_rete.REGIONE IS NOT NULL AND canaliz_rete.REGIONE <> '') AND
				(canaliz_rete.PROVINCIA IS NOT NULL AND canaliz_rete.PROVINCIA <> '') AND
				 
				canaliz_rete.EMAIL IS NOT NULL AND movement.REPAIRER_ID IS NOT NULL AND 
				movement.CHECKOUT_STATION = canaliz_canaliz.COD_GWY ";
				$sqlWhereDirect = "(

				(canaliz_rete.CARROZZERIA = 'x' AND ((canaliz_canaliz.CARROZZERIE1 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.CARROZZERIE2 = canaliz_rete.EMAIL) OR (canaliz_canaliz.CARROZZERIE3 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.CARROZZERIE4 = canaliz_rete.EMAIL)))
				OR
				((canaliz_rete.MECCANICA = 'x' OR canaliz_rete.TAGLIANDO = 'x') AND ((canaliz_canaliz.OFFICINE1 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.OFFICINE2 = canaliz_rete.EMAIL) OR (canaliz_canaliz.OFFICINE3 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.OFFICINE4 = canaliz_rete.EMAIL)))
				OR
				((canaliz_rete.GOMME = 'x' OR canaliz_rete.WINTER_PROGRAMME_ = 'x') AND ((canaliz_canaliz.GOMME1 = canaliz_rete.EMAIL) OR (canaliz_canaliz.GOMME2 = canaliz_rete.EMAIL)))
				OR
				(canaliz_rete.CRISTALLI = 'x' AND ((canaliz_canaliz.CRISTALLI = canaliz_rete.EMAIL)))
				
				) ";
				$sqlOrder = "GROUP BY movement.id ORDER BY canaliz_rete.REGIONE";
				$sqlWhere = "$sqlWhereSelect AND $sqlWhereDirect AND $conditionsProvinces AND $sqlWhereDefect";
				$sql = "$sqlSelect $sqlWhere $sqlOrder";

				$arrMovements = $this->db->query($sql)->result();
				$lastElement = '';
				foreach($arrMovements as $movement) {
					$indexProvince = array_search(strtolower($movement->provincia), array_map('strtolower', $arrIndexProvincesDirect));
					$arrAssignedProvince[$indexProvince]['assigned']++;
				}

				foreach ($arrAssignedProvince as $el) {
					$assigned = $el["assigned"];
					$availability = $el["availability"] = ($el["availability"]>0) ? $el["availability"] : $this->defaultDirectAvailability;
					$recordName = $recordNameLong = trim($el["province"]);
					$recordName = (strlen($recordName)>$maxCharLabel) ? substr($recordName,0,$maxCharLabel)."..." : $recordName;
					if ($blnFilterNetwork) {
						$recordName = "* $recordName";
					}
					$el["is_direct"] = true;
					$el["is_network"] = false;
					$r = Array(
						"id" => ++$recordIdAuto,
						"availability" => $availability,
						"assigned" => $assigned,
						"name" => "$recordIdAuto - ".strtoupper($recordName),
						"namelong" => $recordNameLong,
						"record" => $el,
						"is_station" => false
					);
					array_push($arrData,$r);
				}
			}
			// ----- filter direct
		}
		// end show provinces

		// show stations
		if ($blnFilterProvinces) {
			$conditionsProvinces = $this->buildSqlConditions($arrProvinces,"id","canaliz_rete.PROVINCIA", true);
			// ----- filter network
			if ($blnFilterNetwork) {
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
				(canaliz_rete.NETWORK <> 'diretti') AND 
				(canaliz_rete.CODICE_NETWORK IS NOT NULL AND canaliz_rete.CODICE_NETWORK <> '') AND
				(canaliz_rete.REGIONE IS NOT NULL AND canaliz_rete.REGIONE <> '') AND
				(canaliz_rete.PROVINCIA IS NOT NULL AND canaliz_rete.PROVINCIA <> '') AND
				 
				canaliz_rete.EMAIL IS NOT NULL AND movement.REPAIRER_ID IS NOT NULL AND 
				movement.CHECKOUT_STATION = canaliz_canaliz.COD_GWY ";
				$sqlWhereNetwork = "(
				(canaliz_rete.CARROZZERIA = 'x' AND ((canaliz_canaliz.CARROZZERIE1 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.CARROZZERIE2 = canaliz_rete.EMAIL) OR (canaliz_canaliz.CARROZZERIE3 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.CARROZZERIE4 = canaliz_rete.EMAIL)))
				OR
				((canaliz_rete.MECCANICA = 'x' OR canaliz_rete.TAGLIANDO = 'x') AND ((canaliz_canaliz.OFFICINE1 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.OFFICINE2 = canaliz_rete.EMAIL) OR (canaliz_canaliz.OFFICINE3 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.OFFICINE4 = canaliz_rete.EMAIL)))
				OR
				((canaliz_rete.GOMME = 'x' OR canaliz_rete.WINTER_PROGRAMME_ = 'x') AND ((canaliz_canaliz.GOMME1 = canaliz_rete.EMAIL) OR (canaliz_canaliz.GOMME2 = canaliz_rete.EMAIL)))
				OR
				(canaliz_rete.CRISTALLI = 'x' AND ((canaliz_canaliz.CRISTALLI = canaliz_rete.EMAIL)))
				
				) ";
				$sqlOrder = "GROUP BY movement.id ORDER BY canaliz_rete.RAGIONE_SOCIALE";
				$sqlWhere = "$sqlWhereSelect AND $sqlWhereNetwork AND $conditionsProvinces AND $sqlWhereDefect";
				$sql = "$sqlSelect $sqlWhere $sqlOrder";
				$arrMovements = $this->db->query($sql)->result();
				$lastElement = '';
				$arrIndexStationNetwork = Array();
				$arrAssignedStations = Array();
				$i = 0;

				$conditionsNotPresent = $this->buildSqlConditions($arrProvinces,"id","PROVINCIA", true);
				$sqlNotPresent = "SELECT DISTINCT RAGIONE_SOCIALE as ragione_sociale,
								CODICE_FORNITORE as repairer_id,
								CARROZZERIA as carrozzeria,
								CRISTALLI as cristalli,
								GOMME as gomme,
								INTERVENTO_IN_GARANZIA as garanzia,
								MECCANICA as meccanica,
								TAGLIANDO as tagliando,
								WINTER_PROGRAMME_ as winter
								FROM canaliz_rete ";
				$sqlNotPresent .= "WHERE $conditionsNotPresent ";
				$sqlNotPresent .= "AND NETWORK <> 'diretti' ";
				$sqlNotPresent .= "AND CODICE_NETWORK IS NOT NULL AND CODICE_NETWORK <> '' ";
				$sqlNotPresent .= "AND REGIONE IS NOT NULL AND REGIONE <> '' ";
				$sqlNotPresent .= "AND PROVINCIA IS NOT NULL AND PROVINCIA <> '' ";
				$sqlNotPresent .= "ORDER BY RAGIONE_SOCIALE";
				$arrAllRecords = $this->db->query($sqlNotPresent)->result();
				$arrPresentAdded = Array();

				foreach($arrMovements as $movement) {
					if ($lastElement!==$movement->ragione_sociale) {
						$lastElement = $movement->ragione_sociale;
						$arrIndexStationNetwork[$i] = strtolower($movement->ragione_sociale);
						$i++;
						$r = Array(
							"assigned" => 1,
							"availability" => $this->defaultNetworkAvailability,
							"station" => $movement->ragione_sociale,
							"repairer_id" => $movement->repairer_id,
							"is_carrozzeria" => (strtolower($movement->carrozzeria) === "x") ? true : false,
							"is_cristalli" => (strtolower($movement->cristalli) === "x") ? true : false,
							"is_gomme" => (strtolower($movement->gomme) === "x") ? true : false,
							"is_garanzia" => (strtolower($movement->garanzia) === "x") ? true : false,
							"is_meccanica" => (strtolower($movement->meccanica) === "x") ? true : false,
							"is_tagliando" => (strtolower($movement->tagliando) === "x") ? true : false,
							"is_winter" => (strtolower($movement->winter) === "x") ? true : false
						);
						array_push($arrAssignedStations,$r);
						array_push($arrPresentAdded,$movement->ragione_sociale);
					}
					else {
						$indexStation = array_search(strtolower($movement->ragione_sociale), array_map('strtolower', $arrIndexStationNetwork));
						$arrAssignedStations[$indexStation]['assigned']++;
					}
				}

				foreach ($arrAllRecords as $record) {
					if (in_array($record->ragione_sociale,$arrPresentAdded)===FALSE) {
						$r = Array(
							"assigned" => 0,
							"availability" => $this->defaultNetworkAvailability,
							"station" => $record->ragione_sociale,
							"repairer_id" => $record->repairer_id,
							"is_carrozzeria" => (strtolower($record->carrozzeria) === "x") ? true : false,
							"is_cristalli" => (strtolower($record->cristalli) === "x") ? true : false,
							"is_gomme" => (strtolower($record->gomme) === "x") ? true : false,
							"is_garanzia" => (strtolower($record->garanzia) === "x") ? true : false,
							"is_meccanica" => (strtolower($record->meccanica) === "x") ? true : false,
							"is_tagliando" => (strtolower($record->tagliando) === "x") ? true : false
						);
						array_push($arrAssignedStations,$r);
					}
				}

				foreach ($arrAssignedStations as $el) {
					$assigned = $el["assigned"];
					$availability = $el["availability"] = ($el["availability"]>0) ? $el["availability"] : $this->defaultNetworkAvailability;
					$recordName = $recordNameLong = trim($el["station"]);
					$recordName = (strlen($recordName)>$maxCharLabel) ? substr($recordName,0,$maxCharLabel)."..." : $recordName;
					$el["is_direct"] = false;
					$el["is_network"] = true;
					$r = Array(
						"id" => ++$recordIdAuto,
						"availability" => $availability,
						"assigned" => $assigned,
						"name" => "$recordIdAuto - ".strtoupper($recordName),
						"namelong" => $recordNameLong,
						"record" => $el,
						"is_station" => true
					);
					array_push($arrData,$r);
				}
			}
			// ----- end filter network

			// ----- filter direct
				if ($blnFilterDirect) {
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
				(canaliz_rete.NETWORK = 'diretti') AND 
				(canaliz_rete.CODICE_FORNITORE IS NOT NULL AND canaliz_rete.CODICE_FORNITORE <> '') AND
				(canaliz_rete.REGIONE IS NOT NULL AND canaliz_rete.REGIONE <> '') AND
				(canaliz_rete.PROVINCIA IS NOT NULL AND canaliz_rete.PROVINCIA <> '') AND
				 
				canaliz_rete.EMAIL IS NOT NULL AND movement.REPAIRER_ID IS NOT NULL AND 
				movement.CHECKOUT_STATION = canaliz_canaliz.COD_GWY ";
				$sqlWhereNetwork = "(
				
				(canaliz_rete.CARROZZERIA = 'x' AND ((canaliz_canaliz.CARROZZERIE1 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.CARROZZERIE2 = canaliz_rete.EMAIL) OR (canaliz_canaliz.CARROZZERIE3 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.CARROZZERIE4 = canaliz_rete.EMAIL)))
				OR
				((canaliz_rete.MECCANICA = 'x' OR canaliz_rete.TAGLIANDO = 'x') AND ((canaliz_canaliz.OFFICINE1 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.OFFICINE2 = canaliz_rete.EMAIL) OR (canaliz_canaliz.OFFICINE3 = canaliz_rete.EMAIL)
						OR (canaliz_canaliz.OFFICINE4 = canaliz_rete.EMAIL)))
				OR
				((canaliz_rete.GOMME = 'x' OR canaliz_rete.WINTER_PROGRAMME_ = 'x') AND ((canaliz_canaliz.GOMME1 = canaliz_rete.EMAIL) OR (canaliz_canaliz.GOMME2 = canaliz_rete.EMAIL)))
				OR
				(canaliz_rete.CRISTALLI = 'x' AND ((canaliz_canaliz.CRISTALLI = canaliz_rete.EMAIL)))
				
				) ";
				$sqlOrder = "GROUP BY movement.id ORDER BY canaliz_rete.RAGIONE_SOCIALE";
				$sqlWhere = "$sqlWhereSelect AND $sqlWhereNetwork AND $conditionsProvinces AND $sqlWhereDefect";
				$sql = "$sqlSelect $sqlWhere $sqlOrder";
				$arrMovements = $this->db->query($sql)->result();
				$lastElement = '';
				$arrIndexStationNetwork = Array();
				$arrAssignedStations = Array();
				$i = 0;
				$conditionsNotPresent = $this->buildSqlConditions($arrProvinces,"id","PROVINCIA", true);
				$sqlNotPresent = "SELECT DISTINCT RAGIONE_SOCIALE as ragione_sociale,
								CODICE_FORNITORE as repairer_id,
								CARROZZERIA as carrozzeria,
								CRISTALLI as cristalli,
								GOMME as gomme,
								INTERVENTO_IN_GARANZIA as garanzia,
								MECCANICA as meccanica,
								TAGLIANDO as tagliando,
								WINTER_PROGRAMME_ as winter
								FROM canaliz_rete ";
				$sqlNotPresent .= "WHERE $conditionsNotPresent ";
				$sqlNotPresent .= "AND NETWORK = 'diretti' ";
				$sqlNotPresent .= "AND CODICE_FORNITORE IS NOT NULL AND CODICE_FORNITORE <> '' ";
				$sqlNotPresent .= "AND (CODICE_NETWORK IS NULL OR CODICE_NETWORK = '') ";
				$sqlNotPresent .= "AND REGIONE IS NOT NULL AND REGIONE <> '' ";
				$sqlNotPresent .= "AND PROVINCIA IS NOT NULL AND PROVINCIA <> '' ";
				$sqlNotPresent .= "ORDER BY RAGIONE_SOCIALE";
				$arrAllRecords = $this->db->query($sqlNotPresent)->result();
				$arrPresentAdded = Array();

				foreach($arrMovements as $movement) {
					if ($lastElement!==$movement->ragione_sociale) {
						$lastElement = $movement->ragione_sociale;
						$arrIndexStationNetwork[$i] = strtolower($movement->ragione_sociale);
						$i++;
						$r = Array(
							"assigned" => 1,
							"availability" => $this->getDirectAvailability($movement->repairer_id),
							"station" => $movement->ragione_sociale,
							"repairer_id" => $movement->repairer_id,
							"is_carrozzeria" => (strtolower($movement->carrozzeria) === "x") ? true : false,
							"is_cristalli" => (strtolower($movement->cristalli) === "x") ? true : false,
							"is_gomme" => (strtolower($movement->gomme) === "x") ? true : false,
							"is_garanzia" => (strtolower($movement->garanzia) === "x") ? true : false,
							"is_meccanica" => (strtolower($movement->meccanica) === "x") ? true : false,
							"is_tagliando" => (strtolower($movement->tagliando) === "x") ? true : false,
							"is_winter" => (strtolower($movement->winter) === "x") ? true : false
						);
						array_push($arrAssignedStations,$r);
						array_push($arrPresentAdded,$movement->ragione_sociale);
					}
					else {
						$indexStation = array_search(strtolower($movement->ragione_sociale), array_map('strtolower', $arrIndexStationNetwork));
						$arrAssignedStations[$indexStation]['assigned']++;
					}
				}

				foreach ($arrAllRecords as $record) {
					if (in_array($record->ragione_sociale,$arrPresentAdded)===FALSE) {
						$r = Array(
							"assigned" => 0,
							"availability" => $this->getDirectAvailability($record->repairer_id),
							"station" => $record->ragione_sociale,
							"repairer_id" => $record->repairer_id,
							"is_carrozzeria" => (strtolower($record->carrozzeria) === "x") ? true : false,
							"is_cristalli" => (strtolower($record->cristalli) === "x") ? true : false,
							"is_gomme" => (strtolower($record->gomme) === "x") ? true : false,
							"is_garanzia" => (strtolower($record->garanzia) === "x") ? true : false,
							"is_meccanica" => (strtolower($record->meccanica) === "x") ? true : false,
							"is_tagliando" => (strtolower($record->tagliando) === "x") ? true : false
						);
						array_push($arrAssignedStations,$r);
					}
				}

				foreach ($arrAssignedStations as $el) {
					$assigned = $el["assigned"];
					$availability = $el["availability"] = ($el["availability"]>0) ? $el["availability"] : $this->defaultNetworkAvailability;
					$recordName = $recordNameLong = trim($el["station"]);
					$recordName = (strlen($recordName)>$maxCharLabel) ? substr($recordName,0,$maxCharLabel)."..." : $recordName;
					if ($blnFilterNetwork) {
						$recordName = "* $recordName";
					}
					$el["is_direct"] = false;
					$el["is_network"] = true;
					$r = Array(
						"id" => ++$recordIdAuto,
						"availability" => $availability,
						"assigned" => $assigned,
						"name" => $recordIdAuto." - ".strtoupper($recordName),
						"namelong" => $recordNameLong,
						"record" => $el,
						"is_station" => true
					);
					array_push($arrData,$r);
				}
			}
			// ----- filter direct
		}
		// end show stations



		// -------------------------------------
		// --- send result data for chart: $arrData
		$this->sendOutput($arrData);
		return;
	}


	private function getDirectAvailability($repairerId) {
		$this->db->select("carico");
		$this->db->from("capacity_diretti");
		$this->db->where("lnkmatch",$repairerId);
		$arrData = $this->db->get()->result();
		return (count($arrData)>0) ? intval($arrData[0]->carico) : $this->defaultDirectAvailability;
		
	}

	private function getCapacityRegionNetwork($region,$condDefect) {
		$this->db->select("CODICE_FORNITORE");
		$this->db->distinct();
		$this->db->from("canaliz_rete");
		$this->db->where("REGIONE",$region);
		$this->db->where("REGIONE <>",'');
		$this->db->where("REGIONE IS NOT NULL");
		$this->db->where("PROVINCIA <>",'');
		$this->db->where("PROVINCIA IS NOT NULL");
		$this->db->where("NETWORK <>", 'diretti');
		//$this->db->where("CODICE_FORNITORE IS NOT NULL");
		//$this->db->where("CODICE_FORNITORE <>",'');
		$this->db->where("(CODICE_NETWORK IS NOT NULL AND CODICE_NETWORK <> '')");
		$this->db->where($condDefect);
		$how = $this->db->get()->num_rows();
		$how *= $this->defaultNetworkAvailability;
		return $how;
	}

	private function getCapacityRegionDirect($region,$condDefect) {
		$this->db->select("CODICE_FORNITORE as codice");
		$this->db->distinct();
		$this->db->from("canaliz_rete");
		$this->db->where("REGIONE",$region);
		$this->db->where("REGIONE <>",'');
		$this->db->where("REGIONE IS NOT NULL");
		$this->db->where("PROVINCIA <>",'');
		$this->db->where("PROVINCIA IS NOT NULL");
		$this->db->where("NETWORK", 'diretti');
		$this->db->where("CODICE_FORNITORE IS NOT NULL");
		$this->db->where("CODICE_FORNITORE <>",'');
		$this->db->where($condDefect);
		$arrData = $this->db->get()->result();
		$totCapacity = 0;
		foreach($arrData as $el) {
			$totCapacity += $this->getDirectAvailability($el->codice);
		}
		return $totCapacity;
	}

	private function getCapacityProvinceNetwork($province,$condDefect) {
		$this->db->select("CODICE_FORNITORE");
		$this->db->distinct();
		$this->db->from("canaliz_rete");
		$this->db->where("PROVINCIA",$province);
		$this->db->where("REGIONE <>",'');
		$this->db->where("REGIONE IS NOT NULL");
		$this->db->where("PROVINCIA <>",'');
		$this->db->where("PROVINCIA IS NOT NULL");
		$this->db->where("NETWORK <>", 'diretti');
		//$this->db->where("CODICE_FORNITORE IS NOT NULL");
		//$this->db->where("CODICE_FORNITORE <>",'');
		$this->db->where("CODICE_NETWORK IS NOT NULL");
		$this->db->where("CODICE_NETWORK <>",'');
		$this->db->where($condDefect);
		$how = $this->db->get()->num_rows();
		$how = $how * $this->defaultNetworkAvailability;
		return $how;
	}

	private function getCapacityProvinceDirect($province,$condDefect) {
		$this->db->select("CODICE_FORNITORE as codice");
		$this->db->distinct();
		$this->db->from("canaliz_rete");
		$this->db->where("PROVINCIA",$province);
		$this->db->where("REGIONE <>",'');
		$this->db->where("REGIONE IS NOT NULL");
		$this->db->where("PROVINCIA <>",'');
		$this->db->where("PROVINCIA IS NOT NULL");
		$this->db->where("NETWORK", 'diretti');
		$this->db->where("CODICE_FORNITORE IS NOT NULL");
		$this->db->where("CODICE_FORNITORE <>",'');
		$this->db->where($condDefect);
		$arrData = $this->db->get()->result();
		$totCapacity = 0;
		foreach($arrData as $el) {
			$totCapacity += $this->getDirectAvailability($el->codice);
		}
		return $totCapacity;
	}


}
