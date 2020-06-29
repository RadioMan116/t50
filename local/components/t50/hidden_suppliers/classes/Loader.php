<?php

namespace HiddenSuppliersCompoment;

use Bitrix\Main\Entity;
use ORM\ORMInfo;
use T50GlobVars;
use T50ArrayHelper;
use T50Date;
use rat\agregator\HiddenSupplier;
use Agregator\Manager\Manager;


class Loader
{
	function loadDefault(){
		$shops = T50ArrayHelper::pluck(T50GlobVars::get("CACHE_SHOPS"), "NAME");
		$brandNames = T50GlobVars::get("CACHE_BRAND_NAMES");
		$categories = T50ArrayHelper::pluck(T50GlobVars::get("CACHE_CATEGORIES"), "NAME");
		$regions = T50ArrayHelper::pluck(HiddenSupplier::getEnum("UF_REGION", false), "val", "id");

		return [
			"SHOPS" => $shops,
			"BRANDS" => $brandNames,
			"CATEGORIES" => $categories,
			"REGIONS" => $regions,
		];
	}

	function filter(\StdClass $input){
		$suppliers = T50ArrayHelper::pluck(T50GlobVars::get("CACHE_SUPPLIERS", $city), "NAME");
		$city = HiddenSupplier::getEnum("UF_REGION")[$input->region];
		$filter = [
			"UF_REGION" => $input->region,
			"UF_SHOP" => $input->shop ?? 0,
			"UF_BRAND" => $input->brand ?? 0,
			"UF_CATEGORY" => $input->category ?? 0,
		];
		// \ORM\ORMInfo::sqlTracker("start");
		$data = HiddenSupplier::clas()::getRow(compact("filter"));
		// \ORM\ORMInfo::sqlTracker("show");
		if( isset($data) ){
			$data["DATE"] = T50Date::bxdate($data["UF_DATE"]);
			$data["MANAGER"] = T50GlobVars::get("MANAGERS")[$data["UF_MANAGER_ID"]]["NAME"];
		}

		return ["SUPPLIERS" => $suppliers, "DATA" => $data];
	}

	function update(\StdClass $input, $oldData){
		if( !isset($input->comment) )
			return false;

		$id = intval($oldData["ID"]);
		$updData = [
			"UF_REGION" => $input->region,
			"UF_SHOP" => $input->shop ?? 0,
			"UF_BRAND" => $input->brand ?? 0,
			"UF_CATEGORY" => $input->category ?? 0,
			"UF_HIDDEN_BY_PRICE" => $input->hidden_by_price,
			"UF_HIDDEN_BY_AVAIL" => $input->hidden_by_avail,
			"UF_MANAGER_ID" => $GLOBALS["USER"]->getId(),
			"UF_COMMENT" => $input->comment,
			"UF_DATE" => date("d.m.Y H:i:s"),
		];

		if( $id > 0 ){
			$result = HiddenSupplier::clas()::update($id, $updData);
		} else {
			$result = HiddenSupplier::clas()::add($updData);
		}

		return $result->isSuccess();
	}
}