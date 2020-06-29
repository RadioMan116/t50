<?php

namespace CatalogElementComponent;

use T50GlobVars;
use T50ArrayHelper;
use rat\agregator\Formula;
use rat\agregator\ProductComment;
use Agregator\Product\Brand;

class Info
{
	function getFormulas(array $ids){
		if( empty($ids) )
			return [];

		$filter = ["ID" => $ids];
		$select = ["ID", "UF_TITLE", "UF_MODE"];
		$formulas = Formula::getListIndexed(compact("filter", "select"));
		return array_map([Formula::class, "prepare"], $formulas);
	}

	function getProductInfo(array $product){
		$brandNames = T50GlobVars::get("CACHE_BRAND_NAMES");
		$shopNames = T50ArrayHelper::pluck(T50GlobVars::get("CACHE_SHOPS"), "NAME");
		$shopNames = array_map(function ($id) use($shopNames){
			return $shopNames[$id];
		}, $product["UF_SHOPS"]);

		return [
			"UNID" => $product["ID"],
			"BRAND" => $brandNames[$product["UF_BRAND"]],
			"FORMULAS" => [0 => "-"] + T50GlobVars::get("FORMULAS"),
			"BRAND_FORMULA" => $this->getBrandFormula((int) $product["UF_BRAND"]),
			"SHOPS" => implode(" / ", $shopNames),
			"MODEL" => $product["UF_MODEL_PRINT"],
			"STATISTICS" => json_decode($product["UF_STATISTICS"], true) ?? [],
		];
	}

	private function getBrandFormula(int $brandId){
		$map = Brand::getMapBrandFormula();
		return $map[$brandId];
	}

}