<?php

namespace CatalogDefaultComponent;

use rat\agregator\Product;
use rat\agregator\ProductPrice;
use T50GlobVars;
use T50ArrayHelper;
use Bitrix\Main\Entity;
use ORM\ORMInfo;

class InitialData
{
	private $filter;

	function setFilter(Filter $filter){
		$this->filter = $filter;
	}

	function getData(){
		static $data;
		if( isset($data) )
			return $data;

		$data = array(
			"CITY" => [
				"TITLE" => "Город",
				"NAME" => "city",
				"TYPE" => "SELECT",
				"DATA" => ["MSK" => "Москва", "SPB" => "Санкт-Петербург"],
			],
			"BRAND" =>  [
				"TITLE" => "Бренд",
				"NAME" => "brand",
				"TYPE" => "SELECT",
				"SELECT_DEFAULT" => "-",
				"DATA" => $this->getDataBrands(),
			],
			"CATEGORY" =>  [
				"TITLE" => "Категория",
				"NAME" => "category",
				"TYPE" => "MSELECT",
				"SELECT_DEFAULT" => "-",
				"DATA" => $this->getDataCategories(),
			],
			"SHOP" =>  [
				"TITLE" => "Магазин",
				"NAME" => "shop",
				"TYPE" => "SELECT",
				"SELECT_DEFAULT" => "-",
				"DATA" => $this->getDataShops(),
			],
			"TYPE" =>  [
				"TITLE" => "Тип",
				"NAME" => "build_in",
				"TYPE" => "SELECT",
				"SELECT_DEFAULT" => "-",
				"DATA" => $this->getDataBuildIn(),
			],
			"AVAIL" =>  [
				"TITLE" => "Статус наличия",
				"NAME" => "avail",
				"TYPE" => "MSELECT",
				"SELECT_DEFAULT" => "-",
				"DATA" => $this->getDataAvail(),
			],
			"SALE" =>  [
				"TITLE" => "Цена продажи, руб.",
				"NAME" => "sale",
				"TYPE" => "RANGE",
				"VALUE" => [$_REQUEST["sale_from"], $_REQUEST["sale_to"]],
				// "DATA" => $this->getDataRange("SALE"),
			],
			"PURCHASE" =>  [
				"TITLE" => "Цена закупки, руб.",
				"NAME" => "purchase",
				"TYPE" => "RANGE",
				"VALUE" => [$_REQUEST["purchase_from"], $_REQUEST["purchase_to"]],
				// "DATA" => $this->getDataRange("PURCHASE"),
			],
			"COMMISSION" =>  [
				"TITLE" => "Комиссия, руб.",
				"NAME" => "commission",
				"TYPE" => "RANGE",
				"VALUE" => [$_REQUEST["commission_from"], $_REQUEST["commission_to"]],
				// "DATA" => $this->getDataRange("COMMISSION"),
			],
			"FORMULA" =>  [
				"TITLE" => "Формула цены",
				"NAME" => "formula",
				"TYPE" => "SELECT",
				"SELECT_DEFAULT" => "Все формулы",
				"DATA" => T50GlobVars::get("FORMULAS"),
			],
			"DELIVERY" =>  [
				"TITLE" => "Доставка",
				"NAME" => "delivery",
				"TYPE" => "SELECT",
				"SELECT_DEFAULT" => "-",
				"DATA" => $this->getDataFreePay(),
			],
			"INSTAL" =>  [
				"TITLE" => "Установка",
				"NAME" => "instal",
				"TYPE" => "SELECT",
				"SELECT_DEFAULT" => "-",
				"DATA" => $this->getDataFreePay(),
			],
			"NEW" =>  [
				"TITLE" => "Новинка",
				"NAME" => "new",
				"TYPE" => "SELECT",
				"SELECT_DEFAULT" => "-",
				"DATA" => $this->getSimpleBoolean(),
			],
			"PRICE_MODE" =>  [
				"TITLE" => "Режим цены",
				"NAME" => "price_mode",
				"TYPE" => "SELECT",
				"SELECT_DEFAULT" => "-",
				"DATA" => $this->getDataMode(),
			],
			"AVAIL_MODE" =>  [
				"TITLE" => "Режим наличия",
				"NAME" => "avail_mode",
				"TYPE" => "SELECT",
				"SELECT_DEFAULT" => "-",
				"DATA" => $this->getDataMode(),
			],
		);

		return $data;
	}

	function getNames(){
		$data = $this->getData();
		$arResult = array();
		foreach(["BRAND", "CATEGORY", "AVAIL", "FORMULA"] as $code)
			$arResult[$code] = $data[$code]["DATA"];

		return $arResult;
	}

	private function getDataBrands(){
		$allBrands = T50GlobVars::get("CACHE_BRAND_NAMES");
		if( isset($this->arParams["SHOP"]) )
			return T50ArrayHelper::filterByKeys($allBrands, $this->arParams["SHOP"]["PROPERTY_BRANDS_VALUE"]);

		return $allBrands;
	}

	private function getDataCategories(){
		if( empty($_REQUEST["category"]) && isset($this->arParams["CATEGORY"]) )
			$_REQUEST["category"] = $this->arParams["CATEGORY"]["ID"];

		if( isset($this->arParams["SHOP"]) )
			return $this->arParams["SHOP"]["PROPERTY_CATEGORIES_VALUE"];
		return T50ArrayHelper::pluck(T50GlobVars::get("CACHE_CATEGORIES"), "NAME");
	}

	private function getDataShops(){
		return T50ArrayHelper::pluck(T50GlobVars::get("CACHE_SHOPS"), "NAME");
	}

	function getDataFreePay(){
		return [
			"Y" => "Бесплатно",
			"N" => "Платно",
		];
	}

	function getDataBuildIn(){
		return [
			"Y" => "Встройка",
			"N" => "Отдельностоящие",
		];
	}

	private function getDataMode(){
		return [
			"Y" => "Ручной",
			"N" => "Авто",
		];
	}

	private function getSimpleBoolean(){
		return [
			"Y" => "Да",
			"N" => "Нет",
		];
	}

	private function getDataAvail(){
		$avails = array(
			AVAIL_IN_STOCK => "В наличии",
			AVAIL_BY_REQUEST => "Под заказ",
			AVAIL_OUT_OF_STOCK => "Нет в наличии",
			AVAIL_DISCONTINUED => "Снят с производства",
		);
		return $avails;
	}

	private function getDataRange($code){
		$ranges = $this->getPricesRanges();
		return [$ranges["{$code}_FROM"], $ranges["{$code}_TO"]];
	}

	function getPricesRanges(){
		static $ranges;
		if( isset($ranges) )
			return $ranges;

		$filter = array();
		$columnsForFilterRanges = array("PR.UF_CITY", "UF_SHOPS", "UF_BRAND", "UF_CATEGORIES");
		foreach($columnsForFilterRanges as $column){
			if( isset($this->filter->filter[$column]) ){
				$filter[$column] = $this->filter->filter[$column];
			}
		}

		if( $this->filter->shopId > 0 )
			$filter["UF_SHOP"] = $this->filter->shopId;

		$select = [
			"SALE_FROM", "SALE_TO", "COMMISSION_FROM", "COMMISSION_TO",
			"PURCHASE_FROM", "PURCHASE_TO"
		];

		$filter = $this->filter->getFilter();
		foreach($filter as $code => $item){
		    if( $code{0} == ">" || $code{0} == "<" )
		    	unset($filter[$code]);
		}

		// ORMInfo::sqlTracker("start");
		$ranges = Product::clas()::getRow([
			"select" => $select,
			"runtime" => ProductPrice::getRuntime(),
			"filter" => $filter,
		]);
		// ORMInfo::sqlTracker("show");

		return $ranges;
	}
}