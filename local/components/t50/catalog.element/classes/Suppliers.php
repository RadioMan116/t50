<?php

namespace CatalogElementComponent;

use rat\agregator\Product;
use Agregator\Product\JSON\ProductMarket;
use T50GlobVars;
use Agregator\Manager\Manager;
use T50Date;

class Suppliers extends Detail
{
	function prepareMarketData(ProductMarket $marketData){
		return $this->prepare($marketData);
	}

	protected function prepare($marketData){
		$suppliers = T50GlobVars::get("CACHE_SUPPLIERS", $this->arParams["CITY"]);
		$arResult = array();
		foreach($suppliers as $id => $supplier){
			if( $supplier["PROPERTY_CITY_VALUE"] != $this->arParams["CITY"] )
				continue;

			$marketData->setSupplier($id);
			$avail = $marketData->getAvail();
			$updDate = T50Date::convertDate($supplier["DATE_ACTIVE_FROM"], "d.m.Y H:i:s", "d.m.Y H:i");

			$arResult[] = array(
				"SUPPLIER_ID" => $id,
				"TITLE" => $supplier["NAME"],
				"SUPPLIER_URL" => $this->getSupplierInfo($id),
				"AUTO_AVAIL" => $marketData->getAvailAuto(),
				"AVAIL" => $avail,
				"HIDE" => ($avail == AVAIL_OUT_OF_STOCK),
				"IS_MANUAL_AVAIL" => $marketData->isAvailInManualMode(),
				"DATE_SUPPLY" => $marketData->getDateSupply(),
				"AUTO_PURCHASE" => $marketData->getPurchaseAuto(),
				"PURCHASE" => $marketData->getPurchase(),
				"IS_MANUAL_PURCHASE" => $marketData->isPurchaseInManualMode(),
				"SALE" => $marketData->getSale(),
				"PRICE_UPDATE_DATE" => $updDate,
				"COMMENT" => $this->comments[$id] ?? [],
			);
		}

		uasort($arResult, function ($a, $b){
			return ( $a["AVAIL"] > $b["AVAIL"] ? 1 : -1 );
		});

		return $arResult;
	}

	private function getSupplierInfo($id){
		static $canEdit;
		$canEdit = $canEdit ??  Manager::canUpdateSupplierInfo();
		return "/suppliers/{$id}/" .  ( $canEdit ? "edit/" : "" );
	}
}