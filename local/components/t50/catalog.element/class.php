<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Agregator\Components\BaseComponent;
use Agregator\Components\Traits\ComponentDI;
use Agregator\Manager\Manager;
use T50Html;

class CatalogElementComponent extends BaseComponent
{
	use ComponentDI;

	function executeComponent(){

		if( !$this->Loader->load() ){
			@define("ERROR_404", "Y");
			\CHTTP::SetStatus("404 Not Found");
			return;
		}
		$this->arResult = $this->Loader->getData();
		$this->arResult["INFO"] = $this->Info->getProductInfo($this->arResult["PRODUCT"]);
		$this->arResult["EDITABLE"] = Manager::canChangeProductCard();

		$this->arResult["COMMENTS_JS_DATA"] = T50Html::dataAttrs([
			"product_id" => $this->arResult["PRODUCT"]["ID"],
			"brand_id" => $this->arResult["PRODUCT"]["UF_BRAND"],
			"shop_id" => current($this->arResult["PRODUCT"]["UF_SHOPS"]),
			"can_change" => (Manager::canUpdateProductComments() ? 1 : 0),
		]);

		$this->generateTitle();
		$this->IncludeComponentTemplate();
	}

	private function generateTitle(){
		global $APPLICATION;
		$APPLICATION->SetTitle($this->arResult["PRODUCT"]["UF_TITLE"]);
	}
}
