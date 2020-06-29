<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Agregator\Components\BaseComponent;
use Agregator\Components\Traits\ComponentDI;
use rat\agregator\Order;

class OrdersDocsComponent extends BaseComponent
{
	use ComponentDI;

	function executeComponent(){
		if( !Order::isExists($this->arParams["ORDER_ID"]) )
			return $this->notFound();

		$methodName = T50Text::camelCase($this->arParams["DOC_TEMPLATE"]);
		if( !method_exists($this, $methodName) )
			return $this->notFound();

		$this->arParams["TEMPLATE_DIR"] = __DIR__ . "/docs_templates";

		$this->$methodName();
		$this->IncludeComponentTemplate();
	}

	private function notFound(){
		@define("ERROR_404", "Y");
		CHTTP::SetStatus("404 Not Found");
	}

	private function treatyPhysicalPersons(){
		$this->Treaty->generate();
		$this->arResult["ERRORS"] = $this->Treaty->getErrors();
	}

	private function treatyLegalPersons(){
		$this->Treaty->setIsLegal()->generate();
		$this->arResult["ERRORS"] = $this->Treaty->getErrors();
	}

	private function invoice(){
		$this->prepare(["prepayment" => "boolean"]);
		$this->Invoice->setInput($this->input)->generate();
		$this->arResult["ERRORS"] = $this->Invoice->getErrors();
	}

	private function clientMessage(){
		$this->ClientMessage->generate();
		$this->arResult["ERRORS"] = $this->ClientMessage->getErrors();
	}

	private function offer(){
		$this->Offer->generate();
		$this->arResult["ERRORS"] = $this->Offer->getErrors();
	}
}
