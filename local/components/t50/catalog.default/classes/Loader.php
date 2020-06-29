<?php

namespace CatalogDefaultComponent;

use rat\agregator\Product;
use rat\agregator\ProductPrice;
use Bitrix\Main\Entity;
use ORM\ORMInfo;
use T50GlobVars;
use Bitrix\Main\UI\PageNavigation;
use rat\agregator\ProductComment;

class Loader
{
	private $filter;
	private $nav;

	function setFilter(Filter $filter){
		$this->filter = $filter;
	}

	function load(){
		$select = [
			"ID", "PR.ID", "UF_BRAND", "UF_CATEGORIES", "UF_MODEL_PRINT", "AVAIL",
			"PR.UF_RRC", "SALE", "PR.UF_PRICE_PURCHASE", "UF_CODE",
			"COMMISSION", "PR.UF_FORMULA", "PR.UF_MANUAL_AVAIL", "PR.UF_MANUAL_PRICE",
			"PR.UF_SHOP", "PR.UF_AVAIL", "PR.UF_MANUAL_AVAIL",
			"PR.UF_PRICE_SALE", "PR.UF_MANUAL_PRICE",
			"UF_FLAG_BUILD_IN", "UF_FLAG_NEW",
			"PR.UF_FLAG_FREE_INSTALL", "PR.UF_FLAG_FREE_DELIVER"
		];

		// ORMInfo::sqlTracker("start");
		$this->nav = new PageNavigation("products");
		$this->nav->allowAllRecords(true)->setPageSizes([5, 10, 20])->setPageSize(10)->initFromUri();
		$res = Product::clas()::getList([
			"select" => $select,
			"runtime" => ProductPrice::getRuntime(),
			"filter" => $this->filter->getFilter(),
			"count_total" => true,
			'offset' => $this->nav->getOffset(),
			'limit' => $this->nav->getLimit(),
		]);
		$this->nav->setRecordCount($res->getCount());
		// ORMInfo::sqlTracker("show");

		$data = $this->prepareData($res);

		return $data;
	}

	private function prepareData($res){
		$flagsIdCode = array();
		foreach(T50GlobVars::get("HLPROPS")[Product::getTableName()]["UF_FLAGS"] as $code => $item)
		    $flagsIdCode[$item["id"]] = $code;

		$categories = T50GlobVars::get("CACHE_CATEGORIES");
		$brands = T50GlobVars::get("CACHE_BRAND_NAMES");
		$formulaNames = T50GlobVars::get("FORMULAS");

		$arResult = array();
		$productsId = array();
		while($result = $res->Fetch()) {
			$formulaData = array(
				"ID" => $result["PRODUCT_PR_UF_FORMULA"],
				"TITLE" => $formulaNames[$result["PRODUCT_PR_UF_FORMULA"]],
				"CITY" => $this->arParams["CITY"],
			);

			$productsId[] = $result["ID"];
			$result["CATEGORY"] = $categories[$result["UF_CATEGORIES"][0]]["NAME"];
			$result["BRAND"] = $brands[$result["UF_BRAND"]];
			$result["FORMULA"] = $formulaData;
			$result["FORMULA_LAUNCHED_DATA"] = $formulas[$result["PRODUCT_PR_UF_FORMULA"]];
			$result["FLAGS"] = array_fill_keys($flagsIdCode, false);
			$result["COMMENT"] = ["avail_shop" => [], "sale" => []];
			foreach($result["UF_FLAGS"] as $flagId)
			    $result["FLAGS"][$flagsIdCode[$flagId]] = true;

			$result["URL"] = Product::buildUrl(
				$result["PRODUCT_PR_UF_SHOP"], $result["UF_CATEGORIES"][0], $result["UF_CODE"]
			);
		    $arResult[] = $result;
		}

		$comments = ProductComment::getByProductsId($productsId, $this->arParams["CITY"]);
		foreach($arResult as $k => $item){
		    $shopId = $item["PRODUCT_PR_UF_SHOP"];
		    if( !isset($comments[$item["ID"]]["shops"][$shopId]) )
		    	continue;

		    $arResult[$k]["COMMENT"] = $comments[$item["ID"]]["shops"][$shopId];
		}

		return $arResult;
	}

	private function prepareUrl(array $params){
		$urlParts = ["/catalog"];
		$urlParts[] = $params["shop"];
		$urlParts[] = $params["category"];
		$urlParts[] = $params["element"];
		return implode("/", $urlParts) . ".html";
	}

	function getNav(){
		return $this->nav;
	}

}