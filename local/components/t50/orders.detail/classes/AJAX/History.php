<?php

namespace OrdersDetailComponent\AJAX;

use rat\agregator\Basket as BasketOrm;
use Agregator\Components\BaseAjaxComponent;
use rat\agregator\OrderHistory;
use Agregator\Order\Basket as OrderBasket;
use rat\agregator\Product;
use T50GlobVars;
use T50Date;
use Bitrix\Main\UI\PageNavigation;
use ORM\BaseDataManager;

class History extends BaseAjaxComponent
{
	static $tableNames = [
		"t50_baskets" => "корзина",
		"t50_orders" => "заказ",
		"t50_clients" => "клиент",
		"t50_installations" => "установка",
		"t50_accounts" => "номер счета",
		"t50_delivery" => "доставка",
	];

	function load(){
		if( !$this->prepare(["order_id" => "intval"])->validate(["order_id" => "positive"]) )
			die();

		return $this->_load($this->input->order_id);
	}

	private function _load($orderId){
		$arResult = array();

		$nav = new PageNavigation("history");
		$nav->allowAllRecords(true)->setPageSizes([5, 10, 20])->setPageSize(10)->initFromUri();
		$res = OrderHistory::clas()::getList([
			"filter" => ["UF_ORDER_ID" => $orderId],
			"order" => ["ID" => "DESC"],
			"count_total" => true,
			'offset' => $nav->getOffset(),
			'limit' => $nav->getLimit(),
		]);
		$nav->setRecordCount($res->getCount());

		$items = [];
		while($result = $res->Fetch()) {
			$common = ( $result["UF_ENTITY"] == "COMMON" );
			$items[] = [
				"FIELD" => $this->getField($result),
				"WHERE" => self::$tableNames[$result["UF_ENTITY"]] ?? $result["UF_ENTITY"],
				"BEFORE" => $this->getValue($result, "UF_BEFORE"),
				"AFTER" => $this->getValue($result, "UF_AFTER"),
				"MANAGER" => $this->getManager($result["UF_MANAGER_ID"]),
				"DATE" => T50Date::bxdate($result["UF_DATE_TIME"], "d.m.Y"),
				"TIME" => T50Date::bxdate($result["UF_DATE_TIME"], "H:i"),
				"COMMENT" => ( $common ? $result["UF_COMMENT"] : null),
				"COMMON" => $common,
			];
		}

		return ["ITEMS" => $items, "NAV_OBJECT" => $nav, "ORDER_ID" => $orderId];
	}

	private function getManager($managerId){
		static $managers;
		$managers = $managers ?? T50GlobVars::get("MANAGERS");
		if( $managerId > 0 )
			return $managers[$managerId]["NAME"];

		return "[auto]";
	}

	private function getField($item){
		static $enumProps;
		$enumProps = $enumProps ?? T50GlobVars::get("HLPROPS");

		if( !empty($item["UF_CODE_FLAG"]) ){
			$enumProp = $enumProps[$item["UF_ENTITY"]][$item["UF_CODE"]];
			if( isset($enumProp[$item["UF_CODE_FLAG"]]) )
				return $enumProp[$item["UF_CODE_FLAG"]]["val"];
		}

		return $this->getFieldName($item["UF_ENTITY"], $item["UF_CODE"]);
	}

	private function getValue($item, $code){
		static $enumProps;
		static $boolProps;
		$enumProps = $enumProps ?? T50GlobVars::get("HLPROPS");
		$boolProps = $boolProps ?? T50GlobVars::get("HLFLAGS");
		$value = $item[$code];

		if( $boolProps[$item["UF_CODE"]] )
			return ( $value ? "да" : "нет" );

		$enumProp = $enumProps[$item["UF_ENTITY"]][$item["UF_CODE"]];

		if( isset($enumProp) ){
			foreach($enumProp as $enumPropItem){
			    if( $enumPropItem["id"] == $value )
			    	return $enumPropItem["val"];
			}
		}

		if( ($value == 0 || $value == 1) && !empty($item["UF_CODE_FLAG"]) )
			return ( $value ? "да" : "нет" );

		return $this->detailPrepareValue($value, $item);
	}

	private function detailPrepareValue($value, $item){
		static $classes = [];
		if( !isset($classes[$item["UF_ENTITY"]]) )
			$classes[$item["UF_ENTITY"]] = BaseDataManager::getClassByTable($item["UF_ENTITY"]);

		if( !$classes[$item["UF_ENTITY"]] )
			return $value;

		return $classes[$item["UF_ENTITY"]]::prepareValueForHistory($item["UF_CODE"], $value);
	}

	private function getFieldName($table, $code){
		static $names;
		$names = $names ?? T50GlobVars::get("FIELD_NAMES");
		return $names[$table][$code];
	}
}