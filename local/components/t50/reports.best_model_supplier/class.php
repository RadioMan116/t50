<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


use Agregator\Components\BaseComponent;
use Agregator\Components\Traits\ComponentDI;
use Agregator\Components\Traits\ComponentData;
use Agregator\Manager\Manager;
use T50GlobVars;

class ReportsBestModelSupplierComponent extends BaseComponent
{
	use ComponentDI;
	use ComponentData;

	public $reportTitle = "Лучшие поставщики по моделям";

	private function checkParams(){
		if( $_REQUEST["send_form"] != "Y" ){
			$_REQUEST["only_avail"] = $_GET["only_avail"] = "Y";
			return false;
		}

		$this->prepare([
    		"shop" => "intval",
    		"brands*" => "intval",
    		"suppliers*" => "intval",
    		"only_avail, spb, cond_and_sale" => "null, boolean",
    		"out_to_excel, out_to_csv" => "null, boolean",
		]);

		return $this->validate([
			"shop" => "positive",
    		"brands*" => "positive",
    		"suppliers*" => "positive",
    		"only_avail, spb, cond_and_sale" => "bool",
    		"out_to_excel, out_to_csv" => "bool",
		]);
	}

	function executeComponent(){
		$validParams = $this->checkParams();
		$this->initialData();

		if( !$validParams ){
			$this->IncludeComponentTemplate();
			return $this;
		}

		$this->prepareFileOutLinks();


		$result = $this->Loader
			->setData($this->arResult)
			->setInput($this->input)
			->load();

		$this->arResult["ITEMS"] = $result->getData();
		$this->arResult["USED_SUPPLIERS"] = $result->getUsedSuppliers();
		$this->arResult["SHOW_COND_AND_SALE"] = (bool) $this->input->cond_and_sale;

		if( $this->input->out_to_excel )
			$this->Excel->setArResult($this->arResult)->export();

		if( $this->input->out_to_csv )
			$this->TotalCsv->setArResult($this->arResult)->export();

		$this->IncludeComponentTemplate();
		return $this;
	}

	private function initialData(){
		$this->arResult["BRANDS"] = T50GlobVars::get("CACHE_BRAND_NAMES");
		$this->arResult["SHOPS"] = array_column(T50GlobVars::get("CACHE_SHOPS"), "NAME", "ID");
		$this->arResult["SUPPLIERS"] = array_column(T50GlobVars::get("CACHE_SUPPLIERS"), "NAME", "ID");
		$this->arResult["FORMULAS"] = T50GlobVars::get("FORMULAS");
		$this->arResult["CATEGORIES"] = array_column(T50GlobVars::get("CACHE_CATEGORIES"), "NAME", "ID");
	}

	private function prepareFileOutLinks(){
		global $APPLICATION;
		$this->arResult["LINK_OUT_TO_EXCEL"] = $APPLICATION->GetCurPageParam("out_to_excel=Y");
		$this->arResult["LINK_OUT_TO_CSV"] = $APPLICATION->GetCurPageParam("out_to_csv=Y");
	}
}