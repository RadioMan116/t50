<?php

namespace CatalogElementComponent\Ajax;

use rat\agregator\Formula;
use Agregator\Components\BaseAjaxComponent;
use Agregator\Product\Calculator\FormulaLoader;
use T50Date;
use T50GlobVars;

class FormulaInfo extends BaseAjaxComponent
{
	function loadFormula(){
		$valid = $this->prepare(["id" => "intval"])->validate(["id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$formulaLoader = new FormulaLoader();
		list($main, $childs) = $formulaLoader->loadData($this->input->id, true);
		if( empty($childs) )
			$childs = [$main];

		$blocks = array_map(function ($child){
			$result = [];
			foreach(FormulaLoader::$mapJsCodeToDbCodeValidator as $jsCode => $arrDbCodeValidator){
				$dbCode = $arrDbCodeValidator[0];
			    $result[$jsCode] = $child[$dbCode];
			}
			return $result;
		}, $childs);

		$current = [
			"id" => (int) $main["ID"],
			"title" => $main["UF_TITLE"],
			"date" => T50Date::bxdate($main["UF_DATE"]),
			"manager" => T50GlobVars::get("MANAGERS")[$main["UF_MANAGER_ID"]]["NAME"],
			"comment" => $main["UF_COMMENT"],
		];

		$result = array(
            "rrcSuppliers" => $main["UF_SUPPLIERS_RRC"],
            "blocks" => $blocks,
            "current" => $current,
            "calcFromSalePrice" => (boolean) $main["UF_USE_SUPPLIERS_RRC"],
            "priceType" => $main["UF_MODE"],
            "check_rrc" => (boolean) $main["UF_CHECK_RRC"]
        );

		$this->resultJson(true, "", $result);
	}

	function loadFormulasInfo(){
		$valid = $this->prepare(["city" => "htmlspecialchars"])->validate(["city" => "in:MSK,SPB"]);
		if( !$valid )
			$this->validateErrors();

		$managers = T50GlobVars::get("MANAGERS");
		$formulas = array_map(function($formula) use($managers){
			return [
				"id" => (int) $formula["ID"],
				"title" => $formula["UF_TITLE"],
				"canDelete" => ($formula["UF_UNDELETABLE"] == 0),
			];
		}, FormulaLoader::getFormulas(true));
		$formulas = array_values($formulas);

		$suppliers = array_map(function ($supplier){
			return [
				"id" => (int) $supplier["ID"],
				"title" => $supplier["NAME"],
			];
		}, T50GlobVars::get("CACHE_SUPPLIERS", $this->input->city));
		$suppliers = array_values($suppliers);

		$this->resultJson(true, "", compact("formulas", "suppliers"));
	}
}