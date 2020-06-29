<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


use Agregator\Components\BaseComponent;
use Agregator\Components\Traits\ComponentDI;
use Agregator\Order\Order;
use Agregator\Order\Client;
use Bitrix\Main\Text\HtmlFilter;
use Agregator\Components\Traits\ComponentData;
use Agregator\Manager\Manager;

class OrdersDefaultComponent extends BaseComponent
{
	use ComponentDI;
	use ComponentData;

	private function executeColumnsEditor(){
		if( $_POST["columns_editor"] != "send" )
			return;

		$this->TableView->update();
		localredirect($GLOBALS["APPLICATION"]->getCurPageParam());
		exit();
	}

	private function executeFilter(){
		$availableShops = array_column($this->arResult["INITIAL_DATA"]["SHOP"]["items"], "ID");

		if( $_GET["send_form"] != "Y" ){
			$_REQUEST = $_GET = array(
				"test" => "N",
				"date_create_from" => date("d.m.Y", strtotime("-2 week")),
				"date_create_to" => date("d.m.Y"),
			);
		}

		$checkShopCallable = function ($shopId) use($availableShops){
			return in_array($shopId, $availableShops);
		};
		$valid = $this->prepare([
				"shop*, manager*" => "intval",
				"status*, source*" => "intval",
				"supplier*, provider*" => "intval",
				"pay_type*" => "intval",
				"region" => "null, intval",
				"complaint" => "null, htmlspecialchars",
				"date_create_from, date_create_to" => "null, htmlspecialchars",
				"date_delivery_from, date_delivery_to" => "null, htmlspecialchars",
				"date_account_from, date_account_to" => "null, htmlspecialchars",
				"shipment_tk, official"  => "null, boolean",
				"commission_recived, agency_contract"  => "null, boolean",
				"test"  => "null, boolean",
				"all_orders_with_unid" => "null, intval",
			], $_GET)->validate([
				"shop*" => $checkShopCallable,
				"manager*" => "positive",
				"status*, source*" => "positive",
				"supplier*, provider*" => "positive",
				"pay_type*" => "positive",
				"region" => "positive",
				"complaint" => "in:open,close",
				"date_create_from, date_create_to" => "date_format: d.m.Y",
				"date_delivery_from, date_delivery_to" => "date_format: d.m.Y",
				"date_account_from, date_account_to" => "date_format: d.m.Y",
				"shipment_tk, official"  => "bool",
				"commission_recived, agency_contract"  => "bool",
				"test"  => "bool",
				"all_orders_with_unid" => "positive",
			]);

		if( $valid ){
			$this->Filter->setInput($this->input)->build();
			$this->dataForJs("scroll_to_table", true);
		} else {
			$this->dataForJs("filter_error", true);
		}
	}

	function executeComponent(){
		$this->arResult["INITIAL_DATA"] = $this->InitialData->getData();
		$this->executeColumnsEditor();
		$this->executeFilter();
		$this->arResult["ITEMS"] = $this->Loader->setFilter($this->Filter)->load();
		$this->arResult["NAV_OBJECT"] = $this->Loader->getNav();
		$this->arResult["SUM"] = $this->Summary->calculate($this->arResult);
		$this->arResult["TABLE_VIEW"] = $this->TableView->getData();

		$this->IncludeComponentTemplate();
	}

	private function dataForJs($code, $value){
		if( !isset($this->arResult["DATA_FOR_JS"]) )
			$this->arResult["DATA_FOR_JS"] = [];

		$this->arResult["DATA_FOR_JS"][$code] = $value;
	}

}