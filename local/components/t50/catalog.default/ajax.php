<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Agregator\Components\BaseAjaxComponent;
use Agregator\Components\Traits\ComponentDI;

class CatalogDefaultComponent extends BaseAjaxComponent
{
	use ComponentDI;

	function actionLoadProducts(){
		$this->prepare([
			"type" => "htmlspecialchars",
    		"brand, category, shop" => "null, intval",
    		"model" => "null, htmlspecialchars",
		]);

		$validated = $this->validate([
    		"brand, category, formula" => "positive",
		]);

		if( $validated ){
			$this->input->city = "MSK";
			$this->Filter->setInput($this->input);
		}



		$this->Filter->build();
		$this->InitialData->setFilter($this->Filter);
		$this->Loader->setFilter($this->Filter);

		$arResult["INITIAL_DATA"] = $this->InitialData->getData();
		$arResult["ITEMS"] = $this->Loader->load();
		$arResult["NAV_OBJECT"] = $this->Loader->getNav();

		$arResult["TYPE"] =  $this->input->type;

		$this->includeTemplate("products", $arResult);
	}
}
