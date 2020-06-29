<?php

namespace CatalogElementComponent;

use rat\agregator\Product;
use rat\agregator\ProductPrice;
use rat\agregator\ProductComment;
use rat\agregator\Formula;
use Bitrix\Main\Entity;
use Agregator\Product\JSON\ProductMarket;
use ORM\ORMInfo;
use T50GlobVars;
use Agregator\Components\Traits\ComponentDI;

class Loader
{
	use ComponentDI;

	private $cityId;
	private $arResult = array();

	function __construct($arParams){
		$this->cityId = T50GlobVars::get("HLPROPS")[ProductPrice::getTablename()]["UF_CITY"][$arParams["CITY"]];
	}

	function load(){
		$product = $this->loadProduct();
		if( !isset($product) )
			return false;

		$comments = ProductComment::getByProductId($product["ID"], $this->arParams["CITY"]);
		$this->Shops->setComments($comments["shops"] ?? []);
		$this->Suppliers->setComments($comments["suppliers"] ?? []);

		$this->arResult["MARKET_DATA"] = $this->loadMarketData($product);
		$this->arResult["SHOPS_DATA"] = $this->loadShopsData($product);
		$this->arResult["PRODUCT"] = $this->prepareProduct($product);
		$this->arResult["OTHER_COMMENTS"] = $comments["common"];

		return true;
	}

	private function loadProduct(){
		$filter = [
			"=UF_CODE" => $this->arParams["ELEMENT_CODE"],
			"UF_SHOPS" => $this->arParams["SHOP"]["ID"],
			"UF_CATEGORIES" => $this->arParams["CATEGORY"]["ID"],
		];
		return Product::clas()::getRow(compact("filter"));
	}

	private function loadShopsData($product){
		$select = ["*"];
		$filter = ["UF_PRODUCT_ID" => $product["ID"], "UF_CITY" => $this->cityId];
		$shopsData = ProductPrice::clas()::getList(compact("filter", "select"))->fetchAll();
		return $this->Shops->prepareShopsData($shopsData, $product);
	}

	private function loadMarketData($product){
		$marketData = new ProductMarket($product["UF_DATA_MARKET"]);
		return $this->Suppliers->prepareMarketData($marketData);
	}

	private function prepareProduct(array $product){
		unset($product["UF_DATA_MARKET"]);
		$product["FLAGS"] = Product::prepareFlags($product["UF_FLAGS"]);
		return $product;
	}

	function getData(){
		return $this->arResult;
	}
}