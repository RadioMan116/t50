<?php

namespace Agregator\Order\Mailing;

use rat\agregator\Order;
use rat\agregator\Basket;
use rat\agregator\Delivery;
use rat\agregator\Client;
use Bitrix\Main\Entity;
use Agregator\IB\Elements;
use T50GlobVars;
use Agregator\Order\History;
use Agregator\Common\Mail\MailStructure;

abstract class Mailing
{
	protected function getOrderInfo(int $orderId){
		$managers = T50GlobVars::get("MANAGERS");
		$order = Order::clas()::getRow([
			"select" => ["*", "CL.*"],
			"filter" => ["ID" => $orderId],
			"runtime" => [
				new Entity\ReferenceField("CL", Client::clas(), ['=this.UF_CLIENT' => 'ref.ID']),
			],
		]);
		$order["MANAGER"] = $managers[$order["UF_MANAGER_ID"]]["NAME"];

		$elevatorsInfo = Client::getEnum("UF_ELEVATOR", true, true);
		$order["ORDER_CL_UF_ELEVATOR"] = $elevatorsInfo[$order["ORDER_CL_UF_ELEVATOR"]];

		if( $order["UF_SHOP"] > 0 ){
			$shop = (new Elements("shops"))
				->props("HTTP_HOST", "EMAIL", "OFFICIAL_NAME")
				->getOneFetchById($order["UF_SHOP"]);
			$order["SHOP_NAME"] = $shop["PROPERTY_OFFICIAL_NAME_VALUE"];
			$order["SHOP_HOST"] = parse_url($shop["PROPERTY_HTTP_HOST_VALUE"], PHP_URL_HOST);
			$order["SHOP_EMAIL"] = $shop["PROPERTY_EMAIL_VALUE"];
		}
		return $order;
	}

	protected function getBasketInfo(int $orderId){
		$data = Basket::clas()::getList([
			"select" => ["*", "DL.*"],
			"filter" => ["UF_ORDER_ID" => $orderId],
			"runtime" => [
				new Entity\ReferenceField("DL", Delivery::clas(), ['=this.ID' => 'ref.UF_BASKET_ID']),
			],
		])->FetchAll();
		return $data;
	}

	protected function saveHistory(int $orderId, MailStructure $mailData){
		$history = new History;
		$comment = "{$mailData->to} [{$mailData->subject}]";
		return $history->addSimpleComment($comment, $orderId, true, "MAILING")->save();
	}
}