<?php

namespace OrdersDetailComponent\AJAX;

use Agregator\Components\BaseAjaxComponent;
use rat\agregator\Delivery as DeliveryOrm;
use Agregator\Order\Delivery as OrderDelivery;
use T50GlobVars;
use T50Date;

class Delivery extends BaseAjaxComponent
{
	function load(){
		$valid = $this->prepare(["order_id" => "intval"])->validate(["order_id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$result = $this->loadByOrderId($this->input->order_id);
		$this->resultJson(true, "", $result);
	}

	function loadByOrderId(int $orderId){
		$data = DeliveryOrm::clas()::getList(["filter" => ["UF_ORDER_ID" => $orderId]])
			->fetchAll();

		$items = array_map([$this, "prepareForJson"], $data);
		$sum = DeliveryOrm::calcSumClientShop($data);
		return ["items" => $items, "price_client" => $sum["client"], "price_shop" => $sum["shop"]];
	}

	private function prepareForJson($data){
		$flags = DeliveryOrm::getFlags($data["UF_FLAGS"]);
		return [
		    "id" => $data["ID"],
		    "basket_id" => $data["UF_BASKET_ID"],
		    "condition" => $data["UF_CONDITIONS"],
		    "date" => T50Date::bxdate($data["UF_DATE"]),
		    "time" => $data["UF_TIME"],
		    "time_range" => $this->getTimeRange(),
		    "pickup_address" => $data["UF_PICKUP_ADDRESS"],
		    "cost" => $data["UF_COSTS"],
		    "cost_us" => $flags["US_COSTS"],
		    "mkad_price" => $data["UF_MKAD_PRICE"],
		    "mkad_km" => $data["UF_MKAD_KM"],
		    "mkad_sum" => ($data["UF_MKAD_PRICE"] * $data["UF_MKAD_KM"]),
		    "mkad_us" => $flags["US_MKAD"],
		    "vip" => $data["UF_VIP"],
		    "vip_us" => $flags["US_VIP"],
		    "lift" => $data["UF_LIFT"],
		    "lift_us" => $flags["US_LIFT"],
		];
	}

	private function getTimeRange(){
		return ["10-12", "12-15", "15-18", "18-21"];
	}

	function update(){
		$valid = $this->prepare([
			"multiple_update_mode" => "null, boolean",
			"basket_id, order_id" => "intval",
			"value, code" => "htmlspecialchars",
		])->validate([
			"multiple_update_mode" => "bool",
			"order_id" => "positive",
			"basket_id" => "positive",
			"code" => "in:condition,date,time,pickup_address,cost,cost_us,mkad_km,mkad_price,mkad_us,vip,vip_us,lift,lift_us",
		]);
		if( !$valid )
			$this->validateErrors();

		$delivery = new OrderDelivery;
		$delivery->init($this->input->order_id, $this->input->basket_id);
		if( $this->input->multiple_update_mode === true )
			$delivery->setMutilpleUpdateMode();

		$value = $this->input->value;
		switch ($this->input->code) {
			case "condition":
				$success = $delivery->setCondition(intval($value));
			break;
			case "date":
				$success = $delivery->setDate($value);
			break;
			case "time":
				$success = $delivery->setTime($value);
			break;
			case "pickup_address":
				$success = $delivery->setPickupAddress($value);
			break;
			case "cost":
				$success = $delivery->setPrice(intval($value));
			break;
			case "cost_us":
				$success = $delivery->setFlag("US_COSTS", boolval($value));
			break;
			case "mkad_km":
				$success = $delivery->setMKADDistantion(intval($value));
			break;
			case "mkad_price":
				$success = $delivery->setMKADPrice(intval($value));
			break;
			case "mkad_us":
				$success = $delivery->setFlag("US_MKAD", boolval($value));
			break;
			case "vip":
				$success = $delivery->setVipPrice(intval($value));
			break;
			case "vip_us":
				$success = $delivery->setFlag("US_VIP", boolval($value));
			break;
			case "lift":
				$success = $delivery->setLiftPrice(intval($value));
			break;
			case "lift_us":
				$success = $delivery->setFlag("US_LIFT", boolval($value));
			break;
		}

		$result = $this->loadByOrderId($this->input->order_id);
		$this->resultJson($success, "", $result);
	}

	static function loadConditions(){
		$data = DeliveryOrm::getEnum("UF_CONDITIONS", false);
		$arResult = [];
		foreach($data as $code => $item){
		    $item = ["val" => $item["id"], "title" => $item["val"], "pickup" => false, "tk" => false];

		    if( $code == "pickup" )
		    	$item["pickup"] = true;

		    if( $code == "delivery_tk" )
		    	$item["tk"] = true;

		    $arResult[] = $item;
		}

		return $arResult;
	}
}