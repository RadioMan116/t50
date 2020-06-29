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
		$this->arResult["HAS_ACCESS"] = Manager::canUpdateSupplierInfo();
		if( !$this->checkParams() ){
			@define("ERROR_404", "Y");
			CHTTP::SetStatus("404 Not Found");
			return;
		}

		$this->IncludeComponentTemplate($this->input->action);
	}

	function checkParams(){
		$this->prepare([
			"supplier_id" => "intval",
			"action" => "null, htmlspecialchars",
			"text" => "null, htmlspecialchars",
		]);
		$valid = $this->validate([
			"supplier_id" => "positive",
			"action" => function ($action){
				if( !in_array($action, ["edit", "update"]) )
					return false;

				if( !$this->arResult["HAS_ACCESS"] )
					return false;

				return true;
			},
		]);
		if( !$valid )
			return false;

		if( !isset($this->input->action) )
			$this->input->action = "show";

		if( $this->input->action == "update" ){
			if( $this->Supplier->update($this->input) ){
				LocalRedirect("/suppliers/{$this->input->supplier_id}/edit/");
				die();
			} else {
				$this->arResult["ERROR"] = true;
			}
		}

		$this->arResult += $this->Supplier->load($this->input->supplier_id);
		if( empty($this->arResult["NAME"]) )
			return false;

		return $valid;
    }
}
