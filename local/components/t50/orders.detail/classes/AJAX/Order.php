<?php

namespace OrdersDetailComponent\AJAX;

use rat\agregator\Basket as BasketOrm;
use rat\agregator\Order as OrderOrm;
use Agregator\Components\BaseAjaxComponent;
use rat\agregator\OrderHistory;
use Agregator\Order\Basket as OrderBasket;
use Agregator\Order\Order as AgregatorOrder;
use Agregator\Order\Installation as OrderInstallation;
use Agregator\Order\Client as OrderClient;
use rat\agregator\Product;
use T50GlobVars;
use T50Date;
use rat\agregator\OrderProperty;
use Agregator\Product\Shop;

class Order extends BaseAjaxComponent
{
	function loadStatic(){
		$data = [
			"managers" => $this->getManagers(),
			"statuses" => OrderOrm::getEnumForJson("UF_STATUS"),
			"sources" => OrderOrm::getEnumForJson("UF_SOURCE"),
			"cities" => OrderOrm::getEnumForJson("UF_CITY"),
			"deliveryConditions" => Delivery::loadConditions(),
			"installationProviders" => OrderInstallation::getProviders(),
			"suppliers" => $this->getSuppliers(),
		];
		$this->resultJson(true, "", $data);
	}

	function load(){
		$valid = $this->prepare(["order_id" => "intval"])->validate(["order_id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$result = $this->loadByOrderId($this->input->order_id);
		if( empty($result) )
			$this->resultJson(false, "not found order by id {$this->input->order_id}");

		$this->resultJson(true, "", $result);
	}

	function loadByOrderId(int $orderId){
		$data = OrderOrm::clas()::getRowById($orderId);
		if( empty($data) )
			$this->resultJson(false, "not found order by id {$orderId}");

		$result = $this->prepareData($data);
		return $result;
	}

	private function prepareData($data){

		$shopName = T50GlobVars::get("CACHE_SHOPS")[$data["UF_SHOP"]]["NAME"];
		$flags = OrderOrm::getFlags($data["UF_FLAGS"]);
		$shareComManagerId = OrderProperty::getInt($data["ID"], "SHARE_COM_MANAGER");
		$dateInvoice = OrderProperty::getStr($data["ID"], "DATE_INVOICE");
		$arResult = array(
		    "is_test" => (int) $data["UF_TEST"],
		    "shop" => $shopName,
		    "order_id" => $data["ID"],
		    "remote_order_id" => $data["UF_REMOTE_ORDER"],
		    "manager" => $data["UF_MANAGER_ID"],
		    "source" => $data["UF_SOURCE"],
		    "status" => $data["UF_STATUS"],
		    "city" => $data["UF_CITY"],
		    "dateCreate" => T50Date::bxdate($data["UF_DATE_CREATE"], "d.m.Y, H:i:s"),
		    "dateDelivery" => T50Date::bxdate($data["UF_DATE_DELIVERY"], "d.m.Y"),
		    "agreed_client" => $flags["agreed_client"] ?? false,
		    "agreed_supplier" => $flags["agreed_supplier"] ?? false,
		    "currentManager" => $GLOBALS["USER"]->getId(),
		    "client_id" => $data["UF_CLIENT"],
		    "share_com_manager" => $shareComManagerId,
		    "date_invoice" => $dateInvoice,
		);
		$phoneCodes = Shop::getPhonesCode((int) $data["UF_SHOP"]);
		foreach($phoneCodes as $cityCode => $phoneCode){
		    $arResult["phone_code_" . strtolower($cityCode)] = $phoneCode;
		}

		return $arResult;
	}

	function update(){
		$valid = $this->prepare([
				"order_id" => "intval",
				"value" => "htmlspecialchars",
				"code" => "htmlspecialchars",
			])->validate([
				"order_id" => "positive",
				"code" => "in: is_test, source, status, agreed_client, agreed_supplier, share_com_manager, city, date_invoice",
			]);

		if( !$valid )
			$this->validateErrors();

		if( $this->input->code != "date_invoice" )
			$this->input->value = intval($this->input->value);

		$order = new AgregatorOrder;
		$order->init($this->input->order_id);

		switch ($this->input->code) {
			case "is_test":
				$success = $order->setIsTest((bool) $this->input->value);
				$this->resultJson($success, "", $this->loadByOrderId($this->input->order_id));
			case "source":
				$success = $order->setSource($this->input->value);
				$this->resultJson($success, "", $this->loadByOrderId($this->input->order_id));
			case "status":
				$success = $order->setStatus($this->input->value);
				$this->resultJson($success, "", $this->loadByOrderId($this->input->order_id));
			case "city":
				$success = $order->setCity($this->input->value);
				$order = $this->loadByOrderId($this->input->order_id);
				$client = (new OrderClient)->load($order["client_id"]);
				$this->resultJson($success, "", compact("order", "client"));
			case "agreed_client":
				$success = $order->setFlag("agreed_client", (bool) $this->input->value);
				$this->resultJson($success, "", $this->loadByOrderId($this->input->order_id));
			case "agreed_supplier":
				$success = $order->setFlag("agreed_supplier", (bool) $this->input->value);
				$this->resultJson($success, "", $this->loadByOrderId($this->input->order_id));
			case "share_com_manager":
				$success = AgregatorOrder::shareCommission($this->input->order_id, $this->input->value);
				$this->resultJson($success);
			case "date_invoice":
				$success = AgregatorOrder::setDateInvoice($this->input->order_id, $this->input->value);
				$this->resultJson($success, "", $this->loadByOrderId($this->input->order_id));
		}
	}

	private function getManagers(){
		$managers = T50GlobVars::get("MANAGERS");
		$managers = array_map(function ($manager){
			return ["id" => $manager["ID"], "name" => $manager["NAME"]];
		}, $managers);
		return array_values($managers);
	}

	private function getSuppliers(){
		$data = T50GlobVars::get("CACHE_SUPPLIERS", "MSK");
		return \T50ArrayHelper::toIndexed($data, ["ID" => "val", "NAME" => "title"]);
	}
}