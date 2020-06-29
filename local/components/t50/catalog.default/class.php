<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Agregator\Components\BaseComponent;
use Agregator\Components\Traits\ComponentDI;
use Agregator\Manager\Manager;

class CatalogDefaultComponent extends BaseComponent
{
	use ComponentDI;

	private function checkParams(){
		$this->arParams["CITY"] = "MSK";
		if( $_REQUEST["send_form"] != "Y" )
			return;

		$this->prepare([
    		"city" => "htmlspecialchars",
    		"avail*" => "intval",
    		"brand, formula" => "null, intval",
    		"category*" => "null, intval",
    		"sale_from, sale_to, purchase_from, purchase_to, commission_from, commission_to" => "null, StrToInt",
    		"delivery, instal, new, build_in" => "null, boolean",
    		"price_mode, avail_mode" => "null, boolean",
    		"model, only_model" => "htmlspecialchars",
		]);

		$validated = $this->validate([
    		"avail*" => "in:1,2,3,4",
    		"city" => "in:MSK,SPB",
    		"brand, formula" => "positive",
    		"category*" => "positive",
    		"sale_from, sale_to, purchase_from, purchase_to, commission_from, commission_to" => "numeric",
		]);

		if( $validated ){
			$this->Filter->setInput($this->input);
			$this->arParams["CITY"] = $this->input->city;
		}
	}

	function executeComponent(){
		$this->checkParams();

		$this->Filter->build();
		$this->InitialData->setFilter($this->Filter);
		$this->Loader->setFilter($this->Filter);

		$this->arResult["INITIAL_DATA"] = $this->InitialData->getData();
		$this->arResult["ITEMS"] = $this->Loader->load();
		$this->arResult["NAV_OBJECT"] = $this->Loader->getNav();
		$this->arResult["IS_EDITABLE"] = Manager::canChangeProductCard();

		$this->IncludeComponentTemplate();
	}
}
