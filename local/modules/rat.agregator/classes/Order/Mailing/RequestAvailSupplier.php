<?php

namespace Agregator\Order\Mailing;

use Agregator\Common\Mail\Mail;
use Agregator\Common\Mail\MailStructure;
use rat\agregator\Order;
use rat\agregator\Basket;
use rat\agregator\Delivery;
use T50GlobVars;
use T50Html;

class RequestAvailSupplier extends Mailing
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

		return Mail::prepareByTemplate("REQUEST_AVAIL_FROM_SUPPLIER", $data);
	}

	protected function compileData(int $orderId){
		$order = $this->getOrderInfo($orderId);
		$basket = $this->getBasketInfo($orderId);

		$basketItems = [];
		foreach($basket as $item){
			$basketItems[] = str_replace(
				["NAME", "QUANTITY"],
				[
					"NAME" => $item["UF_NAME"],
					"QUANTITY" => $item["UF_QUANTITY"],
				],
				"NAME\nколичество - QUANTITY шт."
			);
		}

		$manager = T50GlobVars::get("MANAGERS")[$GLOBALS["USER"]->getId()];

		return array(
			"NUMBER" => $order["ID"], //номер заказа
			"MANAGER" => $manager["NAME"], //ФИО менеджера
			"ORDER_LIST" => implode("\n\n", $basketItems), //список товаров
			"SHOP_NAME" => $order["SHOP_HOST"], //название магазина
			"SHOP_EMAIL" => $order["SHOP_EMAIL"], //почта магазина (From:)
			"EMAIL" => "",
		);
	}
}