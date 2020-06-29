<?php

namespace OrdersDetailComponent\AJAX;

use Agregator\Components\BaseAjaxComponent;
use rat\agregator\Account as AccountOrm;
use Agregator\Order\Account as OrderAccount;
use T50GlobVars;
use T50Date;

class Account extends BaseAjaxComponent
{
	function load(){
		$valid = $this->prepare([
				"order_id" => "intval",
				"basket_id" => "null, intval",
			])->validate([
				"order_id" => "positive",
				"basket_id" => "positive",
			]);
		if( !$valid )
			$this->validateErrors();

		$result = $this->loadByOrderId($this->input->order_id, $this->input->basket_id);
		$this->resultJson(isset($result), "", $result);
	}

	function loadByOrderId(int $orderId, $basketId = 0){
		$filter = ["UF_ORDER_ID" => $orderId];
		if( $basketId > 0 )
			$filter["UF_BASKET_ID"] = $basketId;

		$res = AccountOrm::clas()::getList(["filter" => $filter]);
		if( $basketId > 0 ){
			$arResult = $res->fetch();
			if( empty($arResult) )
				return null;

			$arResult = $this->prepareForJson($arResult);
		} else {
			$data = $res->fetchAll();
			$items = array_map([$this, "prepareForJson"], $data);
			$arResult = ["items" => $items];
		}

		return $arResult;
	}

	private function prepareForJson($data){
		$flags = AccountOrm::getFlags($data["UF_FLAGS"]);

		$prepareArray = function($values){
			if( !is_array($values) )
				return [];

			$values = array_map(function ($val){
				if( T50Date::isDate($val) ){
					$val = T50Date::bxdate($val);
					if( $val == "01.01.1970" )
						$val = "";
				}

				return $val;
			}, $values);

			return array_values($values);
		};

		return [
		    "id" => $data["ID"],
		    "basket_id" => $data["UF_BASKET_ID"],
		    "account" => $data["UF_ACCOUNT"], // string,
		    "account_product" => $prepareArray($data["UF_ACCOUNT_PRODUCT"]), // string[],
		    "date_arrival" => $prepareArray($data["UF_DATE_ARRIVAL"]), // string[],
		    "account_delivery" => $prepareArray($data["UF_ACCOUNT_DELIVERY"]), // string[],
		    "official_our" => $prepareArray($data["UF_OFFICIAL_OUR"]), // string[],
		    "official_partners" => $prepareArray($data["UF_OFFICIAL_PARTNERS"]), // string[],
		    "account_tn_tk" => $prepareArray($data["UF_ACCOUNT_TN_TK"]), // string[],
		    "in_stock" => $data["UF_IN_STOCK"] == 1, // boolean,
		    "shipment" => $data["UF_SHIPMENT"] == 1, // boolean,
		];
	}

	function update(){
		$valid = $this->prepare([
			"multiple_update_mode" => "null, boolean",
			"basket_id, order_id" => "intval",
			"code" => "htmlspecialchars",
			"value" => "null",
		])->validate([
			"multiple_update_mode" => "bool",
			"order_id" => "positive",
			"basket_id" => "positive",
			"code" => "in:account,account_product,date_arrival,account_delivery,official_our,official_partners,account_tn_tk,in_stock,shipment",
		]);

		if( !$valid )
			$this->validateErrors();

		$account = new OrderAccount;
		$account->init($this->input->order_id, $this->input->basket_id);
		if( $this->input->multiple_update_mode === true )
			$account->setMutilpleUpdateMode();

		$value = $this->input->value;

		if( is_array($value) ){
			$value = array_map("htmlspecialchars", $value);
		} else {
			$value = htmlspecialchars($value);
		}

		switch ($this->input->code) {
		    case "account":
		     	$success = $account->setOrderAccount($value);
		    break;
		    case "account_product":
		     	$success = $account->setOrderProductAccount($value);
		    break;
		    case "date_arrival":
		     	$success = $account->setArrivalDate($value);
		    break;
		    case "account_delivery":
		     	$success = $account->setDeliveryAccount($value);
		    break;
		    case "official_our":
		     	$success = $account->setOurOfficialAccount($value);
		    break;
		    case "official_partners":
		     	$success = $account->setPartnersOfficialAccount($value);
		    break;
		    case "account_tn_tk":
		     	$success = $account->setTransportAccount($value);
		    break;
		    case "in_stock":
		     	$success = $account->setFlagInStock($value);
		    break;
		    case "shipment":
		     	$success = $account->setFlagInShipment($value);
		    break;
		}

		$result = $this->loadByOrderId($this->input->order_id, $this->input->basket_id);
		$this->resultJson($success, "", $result);
	}

	function removeRow(){
		$valid = $this->prepare([
			"basket_id, order_id, index" => "intval",
		])->validate([
			"order_id, basket_id" => "positive",
			"index" => "min:0",
		]);

		if( !$valid )
			$this->validateErrors();

		$account = new OrderAccount;
		$account->init($this->input->order_id, $this->input->basket_id);
		$success = $account->removeRow($this->input->index);
		$result = $this->loadByOrderId($this->input->order_id, $this->input->basket_id);
		$this->resultJson($success, "", $result);
	}
}