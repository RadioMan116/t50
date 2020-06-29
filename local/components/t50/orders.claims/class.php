<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


use Agregator\Components\BaseComponent;
use Agregator\Components\Traits\ComponentDI;
use Agregator\Order\Order;
use Agregator\Order\Client;
use Bitrix\Main\Text\HtmlFilter;
use Agregator\Components\Traits\ComponentData;
use Agregator\Manager\Manager;

class OrdersClaimsComponent extends BaseComponent
{
	use ComponentDI;
	use ComponentData;

	private function checkParams(){
		$this->arParams["CITY"] = "MSK";
		if( $_GET["send_form"] != "Y" )
			return;

		$valid = $this->prepare([
				"order, shop, supplier, manager, unid" => "null, intval",
				"open" => "null, boolean",
				"date_request, date_result" => "null, htmlspecialchars",
				"client" => "null, htmlspecialchars, trim",
			], $_GET)->validate([
				"order, shop, supplier, manager, unid" => "positive",
				"open" => "bool",
				"date_request, date_result" => "date_format: d.m.Y",
				// "client" => "min:3",
			]);

		if( $valid ){
			$this->Filter->setInput($this->input);
		}
	}

	function executeComponent(){
		$this->checkParams();

		$this->Filter->build();
		$this->Loader->setFilter($this->Filter);
		$this->arResult["INITIAL_DATA"] = $this->InitialData->getData();
		$this->arResult["ITEMS"] = $this->Loader->load();
		$this->arResult["NAV_OBJECT"] = $this->Loader->getNav();

		$this->IncludeComponentTemplate();
	}
}