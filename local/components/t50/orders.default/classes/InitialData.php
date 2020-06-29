<?php

namespace OrdersDefaultComponent;

use rat\agregator\Order;
use rat\agregator\Basket;
use rat\agregator\Installation;
use T50GlobVars;
use T50ArrayHelper;
use Bitrix\Main\Entity;
use ORM\ORMInfo;
use Agregator\Manager\Manager;

class InitialData
{

	function getData(){
		static $data;
		if( isset($data) )
			return $data;

		$data = array(
			"FILTER_TYPE" =>  Manager::getCookie("ORDERS_FILTER_TYPE", "all"),
			"SHOP" =>  $this->getDataShops(),
			"STATUS" => $this->getStatuses(),
			"SOURCE" => $this->getEnum(Order::class, "UF_SOURCE"),
			"REGION" => $this->getEnum(Order::class, "UF_CITY"),
			"MANAGER" => $this->getManagers(),
			"PURCHASE_MANAGER" => $this->getPurchaseManagers(),
			"SUPPLIER" => $this->getSuppliers(),
			"PROVIDER" => array_values(T50GlobVars::get("CACHE_SUPPLIERS", "MSK")),
			"SERVICE_PROVIDER" => Installation::getEnum("UF_PROVIDER", true, true),
			"COMPLAINT" => $this->getСomplaint(),
			"PAY_TYPES" => Basket::getEnum("UF_PAYMENT_TYPE", true, true),
		);

		return $data;
	}

	private function getDataShops(){
		$availableShops = Manager::getAvailableShops();
		$rawGroups = T50GlobVars::get("LPROPS_SHOPS")["GROUP"];
		$groups = array_combine(array_keys($rawGroups["CODE_VALUES"]), array_keys($rawGroups["VALUES"]));
		$groupsIdCode = array_flip($rawGroups["CODE_VALUES"]);
		$items = [];

		foreach(T50GlobVars::get("CACHE_SHOPS") as $id => $item){
			if( !isset($availableShops[$id]) )
				continue;
		    $items[] = [
		    	"ID" => $item["ID"],
		    	"NAME" => $item["NAME"],
		    	"GROUP" => $groupsIdCode[$item["PROPERTY_GROUP_ENUM_ID"]],
		    ];
		}
		return compact("groups", "items");
	}

	private function getStatuses(){
		$groupStatusesMap = array(
			"dispatched" => array(
				"title" => "Отгружен",
				"statuses" => ["appointed_delivery", "agreed_delivery", "completed"],
			),
			"not_shipped" => array(
				"title" => "Не отгружен",
				"statuses" => ["partially_shipped", "wait_delivery", "paid", "paid__wait_delivery"],
			),
		);
		$groups = T50ArrayHelper::pluck($groupStatusesMap, "title");

		$items = [];
		foreach(Order::getEnum("UF_STATUS", false) as $statusCode => $item){
			$group = null;
			foreach($groupStatusesMap as $groupCode => $groupInfo){
				if( in_array($statusCode, $groupInfo["statuses"]) )
					$group = $groupCode;
			}
			$items[] = [
		    	"ID" => $item["id"],
		    	"NAME" => $item["val"],
		    	"GROUP" => $group,
		    	"CODE" => $statusCode,
		    ];
		}

		return compact("items", "groups");
	}

	private function getEnum($ormClass, $code){
		$data = $ormClass::getEnum($code, false);
		return array_column($data, "val", "id");
	}

	private function getManagers(){
		$groups = T50ArrayHelper::pluck(T50GlobVars::get("HLPROPS")["USER"]["UF_GROUP"], "val");
		$items = [];
		foreach(T50GlobVars::get("MANAGERS", "sales_managers") as $item){
			$items[] = [
		    	"ID" => $item["ID"],
		    	"NAME" => $item["NAME"],
		    	"GROUP" => $item["GROUP"],
		    ];
		    $groups[$item["id"]] = $item["val"];
		}

		return compact("items", "groups");
	}

	private function getPurchaseManagers(){
		$managers = T50GlobVars::get("MANAGERS", "purchase_managers");
		return array_values($managers);
	}

	private function getSuppliers(){
		$suppliers = T50GlobVars::get("CACHE_SUPPLIERS", "MSK");
		return T50ArrayHelper::pluck($suppliers, "NAME");
	}

	private function getСomplaint(){
		return array(
			"open" => "Открытая",
			"close" => "Закрытая",
		);
	}
}