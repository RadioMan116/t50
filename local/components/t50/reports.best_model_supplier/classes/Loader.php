<?php

namespace ReportsBestModelSupplierComponent;

use rat\agregator\Product;
use rat\agregator\ProductPrice;
use ORM\ORMInfo;

class Loader
{
	use TraitData;

	function load(){
		// ORMInfo::sqlTracker("start");
		$res = Product::clas()::getList([
			"select" => array(
				"ID", "UF_DATA_MARKET", "UF_MODEL_PRINT", "UF_TITLE", "UF_CATEGORIES", "UF_BRAND",
				"PR.UF_SHOP", "PR.UF_FORMULA", "SALE", "PR.UF_PRICE_PURCHASE", "COMMISSION"
			),
			"runtime" => ProductPrice::getRuntime(),
			"filter" => $this->prepareFilter(),
		]);
		// ORMInfo::sqlTracker("show");

		$compiler = new Compiler;
		$compiler->setInput($this->input)->setData($this->initilData);
		$arResult = array();
		while( $result = $res->Fetch() ){
			$compiler->addRow($result);
		}

		return $compiler;
	}

	private function prepareFilter(){
		$cityInfo = ProductPrice::getEnum("UF_CITY", false);
		$filter = [
			"UF_SHOPS" => $this->input->shop,
			"PR.UF_CITY" => ( $this->input->spb ? $cityInfo["SPB"]["id"] : $cityInfo["MSK"]["id"] ),
		];

		if( !empty($this->input->brands) )
			$filter["UF_BRAND"] = $this->input->brands;

		return $filter;
	}
}