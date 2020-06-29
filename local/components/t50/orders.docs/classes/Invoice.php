<?php

namespace OrdersDocsComponent;

use rat\agregator\Account;
use rat\agregator\Order;
use rat\agregator\Basket;
use rat\agregator\Client;
use Bitrix\Main\Entity\ReferenceField;
use Agregator\Common\NumberText;
use T50Html;
use T50Date;

class Invoice extends Base
{
	const SHOP_RAT = "SHOP_RAT";
	const SHOP_HD = "SHOP_HD";

	private $input;
	private $shopType;

	function getTemplateName(){
		switch( $this->shopType ){
			case self::SHOP_RAT:
				return "invoice_RAT.xls";
			case self::SHOP_HD:
				return "invoice_HD.xls";
		}
	}

	function setInput(\StdClass $input){
		$this->input = $input;
		return $this;
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

		$clientInfo = array();
		$clientInfo["FIO"] = $data["ORDER_CL_UF_FIO"];
		if( empty($clientInfo["FIO"]) )
			$clientInfo["FIO"] = $data["ORDER_CL_UF_FIO2"];

		$clientInfo["ADDRESS"] = Client::buildFullAddress($data, "ORDER_CL_");
		$clientInfo["REQUISITES"] = str_replace(["\r\n", "\r", "\n"], ", ", $data["ORDER_CL_UF_REQUISITES"]);

		$clientInfo = array_filter(array_map("trim", $clientInfo));
		$data["CLIENT_INFO"] = implode("; ", $clientInfo);

		return $data;
	}

	private function getBasket(){
		$res = Basket::clas()::getList(array(
			"select" => ["*", "AC.UF_OFFICIAL_OUR"],
			"filter" => ["UF_ORDER_ID" => $this->arParams["ORDER_ID"]],
			"runtime" => [
				new ReferenceField("AC", Account::clas(), ['=this.ID' => 'ref.UF_BASKET_ID']),
			]
		));
		$items = array();
		$total = 0;
		$cnt = 0;
		while( $result = $res->Fetch() ){
			$cnt ++;

			if( !isset($account) ){
				$accounts = array_map(function ($val){
					return trim($val);
				}, $result["BASKET_AC_UF_OFFICIAL_OUR"]);
				$accounts = array_filter($accounts);
				if( !empty($accounts) )
					$account = current($accounts);
			}

			$sum = $result["UF_PRICE_SALE"] * $result["UF_QUANTITY"];
			$total += $sum;

			$items[] = array(
		    	$cnt,                      // №
		    	$result["UF_NAME"],        // Товары (работы, услуги)
		    	$result["UF_QUANTITY"],    // Кол-во
		    	"шт.",                     // Ед.
		    	$result["UF_PRICE_SALE"],  // Цена
		    	$sum,                      // Сумма
			);
		}

		if( empty($items) ){
			$this->addError("Нет данных по корзине");
			return ;
		}

		if( empty($account) ){
			$this->addError("Не указан счет (Счет офиц. наш)");
			return ;
		}

		$totalText = $this->numToText($total) . " 00 копеек";
		$totalPrint = T50Html::fnum($total);
		$count = count($items);
		$countText = $this->numToText($count, NumberText::IDX_GENUS_AVERAGE);

		$details = "Всего наименований {$count} ({$countText}),  на сумму {$totalPrint} руб.";

		return [
			"ITEMS" => $items,
			"TOTAL" => $total,
			"TOTAL_TEXT" => $totalText,
			"DETAILS" => $details,
			"ACCOUNT" => $account
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
					"B13" => "Счет на оплату № " . $basket["ACCOUNT"] . " от " . $order["DATE"],
					"G19" => $order["CLIENT_INFO"],
					"AH28" => $basket["TOTAL"],
					"B31" => $basket["DETAILS"],
					"B32" => $basket["TOTAL_TEXT"],
				));
				$generator->fillTable(22, 27, ["B", "D", "V", "Y", "AB", "AG"], $basket["ITEMS"]);
			break;

			case self::SHOP_RAT:
				$generator->setValues(array(
					"B13" => "Счет на оплату № " . $basket["ACCOUNT"] . " от " . $order["DATE"],
					"G19" => $order["CLIENT_INFO"],
					"B28" => $basket["DETAILS"],
					"B29" => $basket["TOTAL_TEXT"],
				));
				if( $this->input->prepayment ){
					$generator->setImage("N29",  "bakunin_sing.png", ["H" => 140]);
					$generator->setImage("G31",  "ntk_trade.png", ["H" => 150]);
					$generator->setImage("AC30", "kurnosova_sign.png", ["H" => 90, "Y" => 10]);
				}
				$generator->fillTable(22, 23, ["B", "D", "V", "Y", "AB", "AG"], $basket["ITEMS"]);
			break;

			default:
				throw new \RuntimeException("not detected shop type");
		}

		if( $this->input->prepayment ){
			$generator->download("Счет на предоплату {$order["ID"]}.xls");
		} else {
			$generator->download("Счет на отгрузку {$order["ID"]}.xls");
		}
	}
}