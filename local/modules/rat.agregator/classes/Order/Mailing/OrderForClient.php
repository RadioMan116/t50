<?php

namespace Agregator\Order\Mailing;

use Agregator\Common\Mail\Mail;
use Agregator\Common\Mail\MailStructure;
use rat\agregator\Order;
use rat\agregator\Basket;
use rat\agregator\Delivery;
use Agregator\Order\Client as OrderClient;
use T50Html;

class OrderForClient extends Mailing
{
	function send(int $orderId, MailStructure $mailData){
		if( $orderId <= 0 )
			return false;

		$success = Mail::send($mailData);
		if( $success ){
			OrderClient::setIsEmailSent($orderId);
		}

		return $success;
	}

	function getPreview(int $orderId){
		if( $orderId <= 0 )
			return false;

		$data = $this->compileData($orderId);
		if( empty($data["EMAIL"]) || empty($data["SHOP_EMAIL"]) )
			return false;

		return Mail::prepareByTemplate("ORDER_DETAIL_FOR_CLIENT", $data);
	}

	protected function compileData(int $orderId){
		$order = $this->getOrderInfo($orderId);
		$basket = $this->getBasketInfo($orderId);

		$basketItems = [];
		$basketSum = $deliverySum = 0;
		foreach($basket as $item){
			$item["PRICE_SALE"] = T50Html::fnum($item["UF_PRICE_SALE"]);
			$basketItems[] = str_replace(
				["NAME", "QUANTITY", "PRICE"],
				[
					"NAME" => $item["UF_NAME"],
					"QUANTITY" => $item["UF_QUANTITY"],
					"PRICE" => T50Html::fnum($item["UF_PRICE_SALE"]),
				],
				"NAME - QUANTITY шт. * PRICE р."
			);

			$basketSum += $item["UF_PRICE_SALE"] * $item["UF_QUANTITY"];
		}

		$ORDER_DATE = "";
		$dateDelivery = Delivery::detectDateTime($basket, "BASKET_DL_");
		if( $dateDelivery["date"] ){
			$ORDER_DATE .= $dateDelivery["date"];
			if( $dateDelivery["time"] )
				$ORDER_DATE .= "\nВремя доставки: " . $dateDelivery["time"];
		}

		$ORDER_SUM = T50Html::fnum($basketSum) . " р.";
		$deliverySum = Delivery::calcSumClientShop($basket, "BASKET_DL_");
		if( $deliverySum["client"] > 0  )
			$ORDER_SUM .= " + доставка " . T50Html::fnum($deliverySum["client"]) . " р.";


		return array(
			"NUMBER" => $order["ID"], //номер заказа
			"MANAGER" => $order["MANAGER"], //ФИО менеджера
			"CLIENT" => $order["ORDER_CL_UF_FIO"], //ФИО клиента
			"EMAIL" => $order["ORDER_CL_UF_EMAIL"], //email клиента
			"SHOP_NAME" => $order["SHOP_HOST"], //название магазина
			"SHOP_EMAIL" => $order["SHOP_EMAIL"], //почта магазина (From:)
			"ORDER_DATE" => $ORDER_DATE, //дата доставки
			"ORDER_LIST" => implode("\n", $basketItems), //список товаров
			"ORDER_SUM" => $ORDER_SUM, //сумма (с наим. валюты)
			// "ADD_INFO" => XXX, //дополнительная информация
		);
	}
}