<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Agregator\Components\BaseComponent;
use Agregator\Components\Traits\ComponentDI;
use Agregator\Components\Traits\ComponentData;
use Agregator\Manager\Manager;

class HiddenSuppliersCompoment extends BaseComponent
{
	use ComponentDI, ComponentData;

	function executeComponent(){
		$this->arResult = $this->Loader->loadDefault();
		$this->arResult["HAS_ACCESS"] = Manager::canChangeProductCard();
		if( !empty($_POST) ){
			$this->onSubmit();
		} else {
			$input = new StdClass;
			$input->region = key($this->arResult["REGIONS"]);
			$this->arResult += $this->Loader->filter($input);
		}

		$this->IncludeComponentTemplate();
	}

	private function onSubmit(){
		$this->prepare([
			"shop, brand, category" => "null, intval",
			"region" => "intval",
			"hidden_by_price*" => "null, intval",
			"hidden_by_avail*" => "null, intval",
			"comment" => "null, htmlspecialchars",
			"action" => "htmlspecialchars",
		]);
		$valid = $this->validate([
			"shop" => function ($value){
				return isset($this->arResult["SHOPS"][$value]);
			},
			"brand" => function ($value){
				return isset($this->arResult["BRANDS"][$value]);
			},
			"category" => function ($value){
				return isset($this->arResult["CATEGORIES"][$value]);
			},
			"region" => function ($value){
				return isset($this->arResult["REGIONS"][$value]);
			},
			"hidden_by_price*" => "positive",
			"hidden_by_avail*" => "positive",
			"comment" => "min:5",
			"action" => "in:filter,update",
		]);
		if( !$valid )
			die("error");

		switch($this->input->action){
			case "filter":
				$this->arResult += $this->Loader->filter($this->input);
			break;
			case "update":
				$this->arResult += $this->Loader->filter($this->input);
				if( $this->arResult["HAS_ACCESS"] ){
					if( $this->Loader->update($this->input, $this->arResult["DATA"]) )
						$this->arResult = array_merge($this->arResult, $this->Loader->filter($this->input));
				}
			break;
		}
	}
}
