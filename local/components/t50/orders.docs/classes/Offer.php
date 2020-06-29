<?php

namespace OrdersDocsComponent;

use rat\agregator\Order;
use rat\agregator\Basket;
use rat\agregator\Client;
use Bitrix\Main\Entity\ReferenceField;
use T50Html;
use T50Date;
use T50GlobVars;
use Agregator\IB\Elements;

class Offer extends Base
{
	function getTemplateName(){
		return "offer.xls";
	}

	private function getOrder(){
		$data = Order::clas()::getRow(array(
			"select" => ["*", "CL.*"],
			"filter" => ["ID" => $this->arParams["ORDER_ID"]],
			"runtime" => [
				new ReferenceField("CL", Client::clas(), ['=this.UF_CLIENT' => 'ref.ID']),
			]
		));

		$data["DATE"] = T50Date::bxDate($data["UF_DATE_CREATE"]);

		if( $data["UF_MANAGER_ID"] <= 0 )
			$this->addError("Заказ без менеджера");
		$data["MANAGER"] = T50GlobVars::get("MANAGERS")[$data["UF_MANAGER_ID"]]["NAME"];

		$data["FIO"] = ( $data["ORDER_CL_UF_FIO"] ? $data["ORDER_CL_UF_FIO"] : $data["ORDER_CL_UF_FIO2"] );
		if( empty($data["FIO"]) )
			$this->addError("Не указано имя");

		$data["ADDRESS"] = Client::buildFullAddress($data, "ORDER_CL_");
		if( empty($data["ADDRESS"]) )
			$this->addError("Не указан адрес");

		$data["SHOP_INFO"] = $this->getShopInfo((int) $data["UF_SHOP"]);

		if( !empty($this->errors) )
			return;

		return $data;
	}

	private function getShopInfo(int $shopId){
		if( $shopId <= 0 )
			return "";

		$shop = (new Elements("shops"))
			->select("NAME")
			->props("HTTP_HOST", "PHONE", "EMAIL")
			->getOneFetchById($shopId);

		if( empty($shop) )
			return "";

		$data = [
			parse_url($shop["PROPERTY_HTTP_HOST_VALUE"], PHP_URL_HOST),
			$shop["PROPERTY_PHONE_VALUE"],
			$shop["PROPERTY_EMAIL_VALUE"]
		];
		$data = array_filter(array_map("trim", $data));
		return implode(", ", $data);
	}

	private function getBasket(){
		$res = Basket::clas()::getList(array("filter" => ["UF_ORDER_ID" => $this->arParams["ORDER_ID"]]));
		$items = array();
		$total = 0;
		while( $result = $res->Fetch() ){
			$sum = $result["UF_PRICE_SALE"] * $result["UF_QUANTITY"];
			$total += $sum;
			$items[] = array(
		    	$cnt,                      // №
		    	$result["UF_NAME"],        // Наименование товара
		    	"шт.",                     // Ед. изм.
		    	$result["UF_QUANTITY"],    // Кол-во
		    	$result["UF_PRICE_SALE"],  // Цена
		    	$sum,                      // Сумма
			);
		}

		if( empty($items) ){
			$this->addError("Нет данных по корзине");
			return ;
		}

		$totalText = $this->numToText($total) . " 00 копеек";

		return [
			"ITEMS" => $items,
			"TOTAL" => $total,
			"TOTAL_TEXT" => $totalText,
		];
	}

	function generate(){
		$basket = $this->getBasket();
		if( empty($basket) )
			return;

		$order = $this->getOrder();
		if( empty($order) )
			return;

		$generator = $this->getGenerator();

		$generator->setValues(array(
			"B7" => "Заказ № " . $order["ID"],
			"C9" => $order["DATE"],
			"C11" => $order["SHOP_INFO"],
			"C14" => $order["FIO"],
			"G33" => $basket["TOTAL"],
			"B35" => $basket["TOTAL_TEXT"],
			"D41" => $order["MANAGER"],
		));
		$generator->setImage("D35", "ntk_trade.gif", ["X" => -20]);
		$generator->fillTable(22, 32, ["B", "C", "D", "E", "F", "G"], $basket["ITEMS"]);
		$generator->download("Коммерческое предложение.xls");
	}
}