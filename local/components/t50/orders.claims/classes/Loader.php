<?php

namespace OrdersClaimsComponent;

use rat\agregator\Complaint;
use rat\agregator\Order;
use rat\agregator\Basket;
use rat\agregator\Account;
use rat\agregator\Delivery;
use rat\agregator\Client;
use rat\agregator\OrderComment;
use Bitrix\Main\Entity;
use ORM\ORMInfo;
use T50GlobVars;
use T50Date;
use Bitrix\Main\UI\PageNavigation;

class Loader
{
	private $filter;
	private $nav;

	function setFilter(Filter $filter){
		$this->filter = $filter;
	}

	function load(){
		$select = [
			"ID", "UF_ORDER_ID", "ORD.UF_SHOP", "BK.UF_SUPPLIER", "UF_MANAGER_ID",
			"CL.*", "UF_DATE_REQUEST", "DL.UF_DATE", "UF_DATE_REQUEST",
			"UF_REASON", "UF_REQUIREMENT", "UF_ERROR_TYPE", "UF_DATE_FINISH",
			"UF_RESULT", "AK.UF_ACCOUNT"
		];
		// ORMInfo::sqlTracker("start");
		$this->nav = new PageNavigation("claims");
		$this->nav->allowAllRecords(true)->setPageSizes([5, 10, 20])->setPageSize(10)->initFromUri();
		$res = Complaint::clas()::getList([
			"select" => $select,
			"runtime" => [
				new Entity\ReferenceField("ORD", Order::clas(), ['=this.UF_ORDER_ID' => 'ref.ID']),
				new Entity\ReferenceField("CL", Client::clas(), ['=this.ORD.UF_CLIENT' => 'ref.ID']),
				new Entity\ReferenceField("BK", Basket::clas(), ['=this.UF_ORDER_ID' => 'ref.UF_ORDER_ID']),
				new Entity\ReferenceField("DL", Delivery::clas(), ['=this.BK.ID' => 'ref.UF_BASKET_ID']),
				new Entity\ReferenceField("AK", Account::clas(), ['=this.BK.ID' => 'ref.UF_BASKET_ID']),
			],
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
		$managers = T50GlobVars::get("MANAGERS");
		$shops = T50GlobVars::get("CACHE_SHOPS");
		$suppliers = T50GlobVars::get("CACHE_SUPPLIERS");
		$reasons = Complaint::getEnum("UF_REASON", true, true);
		$resultTypes = Complaint::getEnum("UF_RESULT", true, true);
		$requirements = Complaint::getEnum("UF_REQUIREMENT", true, true);
		$errorTypes = Complaint::getEnum("UF_ERROR_TYPE", true, true);

		$ordersId = array();
		$arResult = array();
		while($result = $res->Fetch()) {
			$arResult[] = array(
				"ORDER_ID" => $result["UF_ORDER_ID"],
				"SHOP" => $shops[$result["COMPLAINT_ORD_UF_SHOP"]]["NAME"],
				"SUPPLIER" => $suppliers[$result["COMPLAINT_BK_UF_SUPPLIER"]]["NAME"],
				"ACCOUNT" => $result["COMPLAINT_AK_UF_ACCOUNT"],
				"MANAGER" => $managers[$result["UF_MANAGER_ID"]]["NAME"],
				"CLIENT" => $this->prepareClientData($result),
				"DATE_DELIVERY" => T50Date::bxdate($result["COMPLAINT_DL_UF_DATE"]),
				"DATE_REQUEST" => T50Date::bxdate($result["UF_DATE_REQUEST"]),
				"REASON" => $reasons[$result["UF_REASON"]],
				"REQUIREMENT" => $requirements[$result["UF_REQUIREMENT"]],
				"ERROR" => $errorTypes[$result["UF_ERROR_TYPE"]],
				"UF_DATE_FINISH" => T50Date::bxdate($result["UF_DATE_FINISH"]),
				"RESULT" => $resultTypes[$result["UF_RESULT"]],
			);
			$ordersId[] = $result["UF_ORDER_ID"];
		}

		$histories = $this->getHistory(array_unique($ordersId));
		$arResult = array_map(function ($item) use($histories){
			$item["HISTORY"] = $histories[$item["ORDER_ID"]];
			return $item;
		}, $arResult);

		return $arResult;
	}

	private function prepareClientData($rawResult){
		$address = Client::buildFullAddress($rawResult, "COMPLAINT_CL_");
		$data = array(
			$rawResult["COMPLAINT_CL_UF_FIO"],
			$rawResult["COMPLAINT_CL_UF_PHONE"],
			$rawResult["COMPLAINT_CL_UF_EMAIL"],
			$address,
		);
		$data = array_filter($data);
		return implode("<br>", $data);
	}

	private function getHistory(array $ordersId = []){
		$res = OrderComment::clas()::getList([
			"order" => ["UF_DATE_CREATE" => "ASC"],
			"select" => ["ID", "UF_DATE_CREATE", "UF_COMMENT", "UF_ORDER_ID"],
			"filter" => ["UF_CLAIM_COMMENT" => 1, "UF_ORDER_ID" => $ordersId]
		]);
		$arResult = array();
		while($result = $res->Fetch()) {
			$orderId = $result["UF_ORDER_ID"];
			if( !isset($arResult[$orderId]) )
				$arResult[$orderId] = array();

			$arResult[$orderId][] = $result;
		}
		$arResult = array_map(function ($items){
			$result = [];
			foreach($items as $item){
			    $result[] = T50Date::bxdate($item["UF_DATE_CREATE"]) . " " . $item["UF_COMMENT"];
			}
			return implode("<br><br>", $result);
		}, $arResult);
		return $arResult;
	}

	function getNav(){
		return $this->nav;
	}

}