<?php

namespace OrdersDocsComponent;

use PhpOffice\PhpWord\Element\TextRun;
use Agregator\Order\Docs\WordTable;
use rat\agregator\Order;
use rat\agregator\Basket;
use rat\agregator\Client;
use Bitrix\Main\Entity\ReferenceField;
use T50Html;
use T50Date;

class Treaty extends Base
{
	private $isLegal;

	function getTemplateName(){
		return ( $this->isLegal ? "treaty_legal_persons.docx" : "treaty_physical_persons.docx" );
	}

	function setIsLegal(){
		$this->isLegal = true;
		return $this;
	}

	function loadData(){
		$order = $this->getOrder();
		$basket = $this->getBasket();
		$arResult = array(
			"NUMBER" => $order["ID"],
			"DATE" => $order["DATE"],
			"FIO" => $order["FIO"],
			"PERSON_INFO" => $order["PERSON_INFO"],
			"TOTAL_TEXT" => $basket["TOTAL_TEXT"],
		);
		$table = (new WordTable())
			->setColumnsWidthPercent([62, 8, 15, 15])
			->setData($basket["ITEMS"])
			->build();

		$arResult["BASKET_TABLE"] = $table;
		return $arResult;
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
		$data["FIO"] = $data["ORDER_CL_UF_FIO"];
		if( empty($data["FIO"]) )
			$data["FIO"] = $data["ORDER_CL_UF_FIO2"];

		$data["PERSON_INFO"] =  str_replace(["\r\n", "\r", "\n"], "<w:br/>", $data["ORDER_CL_UF_REQUISITES"]);
		return $data;
	}

	private function getBasket(){
		$res = Basket::clas()::getList(["filter" => ["UF_ORDER_ID" => $this->arParams["ORDER_ID"]]]);
		$total = 0;
		$items = array([
			0 => "Наименование товара",
			1 => "Кол-во",
			2 => "Цена, руб с НДС",
			3 => "Стоимость, руб с НДС"
		]);
		while( $result = $res->Fetch() ){
			$sum = $result["UF_PRICE_SALE"] * $result["UF_QUANTITY"];
			$total += $sum;
		    $items[] = array(
		    	0 => $result["UF_NAME"],
				1 => $result["UF_QUANTITY"],
				2 => T50Html::fnum($result["UF_PRICE_SALE"]),
				3 => T50Html::fnum($sum),
		    );
		}
		$items[] = array(
	    	0 => "",
			1 => "",
			2 => "Итого, руб. с НДС",
			3 => T50Html::fnum($total),
	    );

		$totalText = $this->numToText($total);

		return ["ITEMS" => $items, "TOTAL" => $total, "TOTAL_TEXT" => $totalText];
	}

	function generate(){
		$generator = $this->getGenerator();
		$data = $this->loadData();
		$generator->setData($data);
		$title = ( $this->isLegal ? "Договор для юридических лиц.docx" : "Договор для физических лиц.docx" );
		$generator->download($title);
	}
}