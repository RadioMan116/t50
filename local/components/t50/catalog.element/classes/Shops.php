<?php

namespace CatalogElementComponent;

use rat\agregator\Product;
use Agregator\Product\JSON\ProductMarket;
use T50GlobVars;
use Agregator\Product\ProductShopsAttachment;


class Shops extends Detail
{
	private $urls = [];

	function prepareShopsData(array $shopsData, array $product){
		$this->urls = ProductShopsAttachment::getRemoteUrls($product);
		return $this->prepare($shopsData);
	}

	protected function prepare($shopsData){
		if( empty($shopsData) )
			return [];

		$shops = T50GlobVars::get("CACHE_SHOPS");
		$arResult = array();
		$formulas = $this->Info->getFormulas(array_column($shopsData, "UF_FORMULA"));

		foreach($shopsData as $item){
			$shop = $shops[$item["UF_SHOP"]];
			if( empty($shop) )
				continue;

			$formulaData = array(
				"ID" => $item["UF_FORMULA"],
				"TITLE" => $formulas[$item["UF_FORMULA"]]["UF_TITLE"],
				"CITY" => $this->arParams["CITY"],
				"MODE" => $formulas[$item["UF_FORMULA"]]["UF_MODE"]["val"],
			);

			$sale = ( $item["UF_MANUAL_PRICE"] ? $item["UF_PRICE_SALE_M"] : $item["UF_PRICE_SALE"] );

			$item["HOST"] = preg_replace("#^https?://#", "", $shop["PROPERTY_HTTP_HOST_VALUE"]);
			$item["SHOP_NAME"] = $shop["NAME"];
			$item["GROUP"] = $shop["PROPERTY_GROUP_VALUE"];
			$item["AVAIL"] = ( $item["UF_MANUAL_AVAIL"] ? $item["UF_AVAIL_M"] : $item["UF_AVAIL"] );
			$item["SALE"] = $sale;
			$item["COMMISSION"] = $sale - $item["UF_PRICE_PURCHASE"];
			$item["FORMULA"] = $formulaData;
			$item["COMMENT"] = $this->comments[$shop["ID"]] ?? [];
			$item["DELIVERY"] = $item["UF_FLAG_FREE_DELIVER"];
		    $item["INSTALL"] = $item["UF_FLAG_FREE_INSTALL"];
		    $item["DISCONT"] = "???";
		    $item["URL"] = $this->urls[$shop["ID"]];

		    $arResult[] = $item;
		}

		return $arResult;
	}

}