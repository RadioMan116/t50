<?php
namespace Agregator\Product\Calculator;

use rat\agregator\Formula;
use T50GlobCache;

class FormulaLoader extends FormulaData
{
	function getById($id){
		$this->id = (int) $id;
		list($main, $childs) = $this->loadData($this->id, true);
		if( empty($main) )
			return null;

		if( !empty($childs) )
			return $this->parseMultiple($main, $childs);

		return $this->parseSingle($main);
	}

	static function getFormulas($detail = false){
		$select = ["ID", "UF_TITLE"];
		if( $detail )
			$select = ["*"];

		$res = Formula::clas()::getList(["select" => $select, "filter" => ["UF_PARENT_ID" => 0]]);
		$arResult = [];
		while( $result = $res->Fetch() )
			$arResult[$result["ID"]] = ( $detail ? $result : $result["UF_TITLE"] );

		return $arResult;
	}

	private function parseSingle($item){
		if( $item["UF_PARENT_ID"] > 0 )
			return ;
		$result = new \StdClass;
		$result->mode = $item["UF_MODE"];
		$result->suppliersSale = $item["UF_SUPPLIERS_RRC"];
		$result->useSupplSale = (bool) $item["UF_USE_SUPPLIERS_RRC"];
		$result->checkRrc = (bool) $item["UF_CHECK_RRC"];
		$result->minCom = (int) $item["UF_MIN_COMMISSION"];
		$result->maxCom = (int) $item["UF_MAX_COMMISSION"];
		$result->perc = (int) $item["UF_PERCENT"];
		return $result;
	}

	private function parseMultiple($parent, $childs){
		if( $parent["UF_PARENT_ID"] > 0 )
			return;

		$arResult = array();
		foreach($childs as $child){
			$result = new \StdClass;
			$result->purchaseRange = [$child["UF_MIN_PURCHASE"], $child["UF_MAX_PURCHASE"]];
			$result->data = new \StdClass;
			$result->data->mode = $parent["UF_MODE"];
			$result->data->suppliersSale = $parent["UF_SUPPLIERS_RRC"];
			$result->data->useSupplSale = (bool) $parent["UF_USE_SUPPLIERS_RRC"];
			$result->data->checkRrc = (bool) $parent["UF_CHECK_RRC"];
			$result->data->minCom = (int) $child["UF_MIN_COMMISSION"];
			$result->data->maxCom = (int) $child["UF_MAX_COMMISSION"];
			$result->data->perc = (int) $child["UF_PERCENT"];
			$arResult[] = $result;
		}

		return $arResult;
	}
}