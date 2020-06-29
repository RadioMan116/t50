<?php

namespace OrdersDocsComponent;

use rat\agregator\Order;
use rat\agregator\Basket;
use rat\agregator\Client;
use Bitrix\Main\Entity\ReferenceField;
use T50Html;
use T50Date;
use T50GlobVars;

class ClientMessage extends Base
{
	const SHOP_RAT = "SHOP_RAT";
	const SHOP_HD = "SHOP_HD";

	private $shopType;

	function getTemplateName(){
		return "client_message.xls";
	}

	private function getOrder(){
		$data = Order::clas()::getRow(array(
			"select" => ["*", "CL.*"],
			"filter" => ["ID" => $this->arParams["ORDER_ID"]],
			"runtime" => [
				new ReferenceField("CL", Client::clas(), ['=this.UF_CLIENT' => 'ref.ID']),
			]
		));
		$this->shopType = self::SHOP_RAT;

		$data["DATE"] = T50Date::bxDate($data["UF_DATE_CREATE"]);

		if( $data["UF_MANAGER_ID"] <= 0 )
			$this->addError("Заказ без менеджера");
		$data["MANAGER"] = T50GlobVars::get("MANAGERS")[$data["UF_MANAGER_ID"]]["NAME"];

		$fio = ( $data["ORDER_CL_UF_FIO"] ? $data["ORDER_CL_UF_FIO"] : $data["ORDER_CL_UF_FIO2"] );
		if( empty($fio) )
			$this->addError("Не указано имя получателя");

		$email = $data["ORDER_CL_UF_EMAIL"];
		$phone = ( $data["ORDER_CL_UF_PHONE"] ? $data["ORDER_CL_UF_PHONE"] : $data["ORDER_CL_UF_PHONE2"] );
		$requisites = $email ? "email: {$email}" : "тел.: {$phone}";
		if( empty($requisites) )
			$this->addError("Нужно указать телефон или email");

		$data["CLIENT_INFO"] = "{$fio} ({$requisites})";

		$data["ADDRESS"] = Client::buildFullAddress($data, "ORDER_CL_");
		if( empty($data["ADDRESS"]) )
			$this->addError("Не указан адрес доставки");

		if( !empty($this->errors) )
			return;

		return $data;
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

		switch ($this->shopType) {

			case self::SHOP_HD:
				$generator->setValues(array(
					"B1" => "Общество с ограниченной ответственностью «Хаусдорф»",
					"B4" => "Введена приказом ООО «Хаусдорф» от 09.01.2018г №7",
					"B7" => "Заказ № " . $order["ID"],
					"C9" => $order["DATE"],
					"C11" => "ООО «Хаусдорф» , г. Москва, Рублевское шоссе, д.52А",
					"C14" => $order["CLIENT_INFO"],
					"C17" => $order["ADDRESS"],
					"B38" => $basket["TOTAL_TEXT"],
					"G36" => $basket["TOTAL"],
					"E49" => $order["MANAGER"],
				));
				$generator->fillTable(24, 35, ["B", "C", "D", "E", "F", "G"], $basket["ITEMS"]);
				$generator->download("Договор на онлайн оплату.xls");
			break;

			case self::SHOP_RAT:
				$generator->setValues(array(
					"B1" => "Общество с ограниченной ответственностью «НТК Трейд»",
					"B4" => "",
					"B7" => "Заказ № " . $order["ID"],
					"C9" => $order["DATE"],
					"C11" => "ООО «НТК Трейд», г. Москва, пр. Маршала Жукова, 59.",
					"C14" => $order["CLIENT_INFO"],
					"C17" => $order["ADDRESS"],
					"B38" => $basket["TOTAL_TEXT"],
					"G36" => $basket["TOTAL"],
					"E49" => $order["MANAGER"],
				));
				$generator->fillTable(24, 35, ["B", "C", "D", "E", "F", "G"], $basket["ITEMS"]);
				$generator->download("Сообщение покупателя.xls");
			break;

			default:
				throw new \RuntimeException("not detected shop type");
		}
	}
}