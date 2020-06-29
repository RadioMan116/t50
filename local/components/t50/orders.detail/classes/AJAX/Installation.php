<?php

namespace OrdersDetailComponent\AJAX;

use Agregator\Components\BaseAjaxComponent;
use rat\agregator\Installation as InstallationOrm;
use Agregator\Order\Installation as OrderInstallation;
use Agregator\Order\History;
use T50GlobVars;
use T50Date;
use Agregator\Components\InputManager;
use Agregator\Integration\RemCity\Order as RemCityOrder;
use rat\agregator\OrderProperty;

class Installation extends BaseAjaxComponent
{
	function load(){
		$valid = $this->prepare(["order_id" => "intval"])->validate(["order_id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$result = $this->loadByOrderId($this->input->order_id);
		$this->resultJson(true, "", $result);
	}

	function loadByOrderId(int $orderId){
		$historyData = (new History())->getComments($orderId);
		$remcityOrderNum = (int) OrderProperty::getInt($orderId, "REMCITY_ORDER_NUM");
		$items = InstallationOrm::clas()::getList(["filter" => ["UF_ORDER_ID" => $orderId]])->fetchAll();
		foreach($items as $k => $item)
		    $items[$k] = $this->prepareForJson($item, $historyData, $remcityOrderNum);

		$sum = $this->calcSum($items);
		$arResult = compact("items", "remcityOrderNum");
		$arResult += $sum;

		return $arResult;
	}

	private function calcSum($items){
		$client = $shop = 0;
		foreach($items as $item){
			$shop += $item["purchase"];
			if( $item["costs_us"] == false ){
				$client += $item["sale"];
			}

			if( $item["visit_master_us"] ){
				$shop += $item["visit_master_sum"];
			} else {
				$client += $item["visit_master_sum"];
			}
		}
		return ["price_client" => $client, "price_shop" => $shop];
	}

	private function prepareForJson($data, $historyData, $remcityOrderNum = 0){
		$flags = InstallationOrm::getFlags($data["UF_FLAGS"]);
		$typeIdCode = InstallationOrm::getEnum("UF_TYPE");
		$providersIdCode = InstallationOrm::getEnum("UF_PROVIDER");
		$provider = $providersIdCode[$data["UF_PROVIDER"]];

		$canChange = !( $provider == "remcity" && $remcityOrderNum > 0 );


		return [
		    "id" => $data["ID"],
		    "basket_id" => $data["UF_BASKET_ID"],
		    "order_id" => $data["UF_ORDER_ID"],
		    "product_name" => $data["UF_PRODUCT_NAME"],
		    "provider" => $provider,
		    "service_id" => $data["UF_SERVICE_ID"],
		    "type" => $typeIdCode[$data["UF_TYPE"]],
		    "date" => T50Date::bxdate($data["UF_DATE"]),
		    "sale" => $data["UF_PRICE_SALE"],
		    "purchase" => $data["UF_PRICE_PURCHASE"],
		    "costs_us" => $flags["US_COSTS"],
		    "visit_master_us" => $flags["US_VISIT_MASTER"],
		    "visit_master_km" => $data["UF_MKAD_KM"],
		    "visit_master_km_price" => $data["UF_MKAD_PRICE"],
		    "visit_master_price" => $data["UF_MASTER"],
		    "visit_master_sum" => $data["UF_MKAD_KM"] * $data["UF_MKAD_PRICE"] + $data["UF_MASTER"],
		    "commission" => $data["UF_COMMISSION"],
		    "comment" => $data["UF_COMMENT"],
		    "can_change" => $canChange,
		];
	}

	function update(){
		$valid = $this->prepare([
			"multiple_update_mode" => "null, boolean",
			"basket_id, order_id, id" => "intval",
			"value, code" => "htmlspecialchars",
			"provider" => "null, htmlspecialchars",
		])->validate([
			"multiple_update_mode" => "bool",
			"order_id" => "positive",
			"basket_id, id" => "required",
			"code" => "in:service,date,costs_us,visit_master_price,visit_master_km,visit_master_us,visit_master_km_price,comment, month_vp,month_zp,suppl_commission,sale,purchase,type",
		]);
		if( !$valid )
			$this->validateErrors();

		if( $this->input->basket_id <= 0 && $this->input->id <= 0 )
			$this->resultJson(false, "basket_id <= 0 and id <= 0");

		$installation = new OrderInstallation;
		$installation->init($this->input->order_id, $this->input->basket_id, $this->input->id);
		if( $this->input->multiple_update_mode === true )
			$installation->setMutilpleUpdateMode();

		$prepare = new InputManager();
		$value = $this->input->value;
		switch ($this->input->code) {
			case "service":
				if( isset($this->input->provider) ){
					$success = $installation->setService($this->input->provider, intval($value));
				} else {
					$success = false;
				}
			break;
			case "date":
				$success = $installation->setDate($value);
			break;
			case "costs_us":
				$success = $installation->setFlag("US_COSTS", $prepare->singlePrepare($value, "boolean"));
			break;
			case "visit_master_us":
				$value = $prepare->singlePrepare($value, "boolean");
				$success = $installation->setFlag("US_VISIT_MASTER", $value);
			break;
			case "visit_master_price":
				$success = $installation->setMasterPrice((int) $value);
			break;
			case "visit_master_km":
				$success = $installation->setMKADDistantion((int) $value);
			break;
			case "visit_master_km_price":
				$success = $installation->setMKADDPrice((int) $value);
			break;
			case "comment":
				$success = $installation->setComment($value);
			break;
			case "month_vp":
				$this->resultJson($installation->setMonthVP($value));
			break;
			case "month_zp":
				$this->resultJson($installation->setMonthZP($value));
			break;
			case "suppl_commission":
				$this->resultJson($installation->setSupplierCommission($value));
			break;
			case "sale":
				$this->resultJson($installation->setSale((int) $value));
			break;
			case "purchase":
				$this->resultJson($installation->setPurchase((int) $value));
			break;
			case "type":
				$this->resultJson($installation->setType($value));
			break;
		}

		$result = $this->loadByOrderId($this->input->order_id);
		$this->resultJson($success, "", $result);
	}

	function create(){
		$valid = $this->prepare([
				"order_id, service_id" => "intval",
				"provider" => "htmlspecialchars",
				"product_name" => "null, htmlspecialchars",
			])->validate([
				"order_id, service_id" => "positive",
				"provider" => "required",
				"product_name" => "required",
			]);
		if( !$valid )
			$this->validateErrors();

		$installation = new OrderInstallation();
		$success = $installation->customCreate(
			$this->input->product_name,
			$this->input->provider,
			$this->input->service_id,
			$this->input->order_id
		);
		$result = $this->loadByOrderId($this->input->order_id);
		$this->resultJson($success, "", $result);
	}

	function delete(){
		$valid = $this->prepare(["order_id, id" => "intval"])->validate(["order_id, id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$installation = new OrderInstallation();
		$installation->init($this->input->order_id, 0, $this->input->id);
		$success = $installation->customDelete();
		$result = $this->loadByOrderId($this->input->order_id);
		$this->resultJson($success, "", $result);
	}

	function sentRemcity(){
		$valid = $this->prepare(["order_id" => "intval"])->validate(["order_id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$remCityOrder = new RemCityOrder($this->input->order_id);
		$success = $remCityOrder->sync();
		$data = $this->loadByOrderId($this->input->order_id);
		$this->resultJson($success, "", $data);
	}
}