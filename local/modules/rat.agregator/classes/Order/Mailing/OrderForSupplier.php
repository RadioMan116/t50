<?php

namespace Agregator\Order\Mailing;

use Agregator\Common\Mail\Mail;
use Agregator\Common\Mail\MailStructure;
use rat\agregator\Order;
use rat\agregator\Basket;
use rat\agregator\Delivery;
use rat\agregator\Client as ClientOrm;
use T50Html;

class OrderForSupplier extends Mailing
{
	function send(int $orderId, MailStructure $mailData){
		if( $orderId <= 0 )
			return false;

		$success = Mail::send($mailData);
		if( $success ){
			$this->saveHistory($orderId, $mailData);
		}

		return $success;
	}

	function getPreview(int $orderId){
		if( $orderId <= 0 )
			return false;

		$data = $this->compileData($orderId);
		if( empty($data["SHOP_EMAIL"]) )
			return false;

		return Mail::prepareByTemplate("ORDER_DETAIL_FOR_SUPPLIER", $data);
	}

	protected function compileData(int $orderId){
		$order = $this->getOrderInfo($orderId);
		$basket = $this->getBasketInfo($orderId);

		$paymentTypes = Basket::getEnum("UF_PAYMENT_TYPE");

		$basketItems = [];
		$payCard = false;
		foreach($basket as $item){
			$item["PRICE_SALE"] = T50Html::fnum($item["UF_PRICE_SALE"]);
			$basketItems[] = str_replace(
				["NAME", "QUANTITY", "PRICE"],
				[
					"NAME" => $item["UF_NAME"],
					"QUANTITY" => $item["UF_QUANTITY"],
					"PRICE" => T50Html::fnum($item["UF_PRICE_SALE"]),
				],
				"NAME - PRICE  - QUANTITY шт"
			);

			$payType = $paymentTypes[$item["UF_PAYMENT_TYPE"]];
			if( substr_count($payType, "card") )
				$payCard = true;
		}

		$address = ClientOrm::buildFullAddress($order, "ORDER_CL_");

		$phone = $order["ORDER_CL_UF_PHONE"];
		if( empty($phone) )
			$phone = $order["ORDER_CL_UF_PHONE2"];

		$deliverySumText = [];
		$deliverySum = Delivery::calcSumClientShop($basket, "BASKET_DL_");
		if( $deliverySum["client"] > 0 )
			$deliverySumText[] = "доставка с клиента - " . T50Html::fnum($deliverySum["client"]);
		if( $deliverySum["shop"] > 0 )
			$deliverySumText[] = "доставка с нас - " . T50Html::fnum($deliverySum["shop"]);

		$dateDelivery = Delivery::detectDateTime($basket, "BASKET_DL_");

		if( $payCard )
			$additionalInfo = "оплата - картой";

		return array(
			"NUMBER" => $order["ID"], //номер заказа
			"ADDRESS" => $address,
			"PHONE" => $phone,
			"PRICES" => implode("\n", $deliverySumText),
			"DATE_DELIVERY" => $dateDelivery["date"],
			"TIME_DELIVERY" => $dateDelivery["time"],
			"SHOP_NAME" => $order["SHOP_HOST"], //название магазина
			"SHOP_EMAIL" => $order["SHOP_EMAIL"], //почта магазина (From:)
			"ORDER_LIST" => implode("\n", $basketItems), //список товаров
			"ADD_INFO" => $additionalInfo, //дополнительная информация
			"EMAIL" => "",
		);
	}
}
