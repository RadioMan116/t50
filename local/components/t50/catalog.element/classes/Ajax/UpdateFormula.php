<?php

namespace CatalogElementComponent\Ajax;

use rat\agregator\Formula;
use Agregator\Components\BaseAjaxComponent;
use Agregator\Product\Calculator\FormulaUpdate;

class UpdateFormula extends BaseAjaxComponent
{
	function update(){
		$this->prepare([
			"title, price_type, city, comment" => "htmlspecialchars",
			"id" => "null, intval",
			"calc_from_sale, check_rrc" => "boolean",
			"suppliers_calc_from_sale*" => "intval",
			"parameters*" => "pass",
		]);

		$rules = [
			"id" => "positive",
			"title" => "required",
			"comment" => "min:5",
			"price_type" => "in:free,rrc",
			"city" => "in:MSK,SPB",
			"suppliers_calc_from_sale*" => "positive",
			"calc_from_sale, check_rrc" => "bool",
			"parameters" => function ($parameters){
				if( !is_array($parameters) || empty($parameters) || !isset($parameters[0]) )
					return false;

				foreach($parameters as $parameter){
				    foreach($parameter as $val){
				    	if( $val === "" )
				    		$val = 0;

				        if( !is_numeric($val) || $val < 0 )
				        	return false;
				    }
				}

				return true;
			},
		];


		$valid = $this->validate($rules);
		if( !$valid )
			$this->validateErrors();

		$input = $this->input;

		$formulaUpdate = new FormulaUpdate();
		$formulaUpdate
			->setTitle($input->title)
			->setComment($input->comment)
			->setPriceType($input->price_type)
			->setModeCalcFromSalePrice($input->calc_from_sale)
			->setModeCheckRrc($input->check_rrc)
			->setSuppliersWithRRC($input->city, $input->suppliers_calc_from_sale)
			->setParameters($input->parameters);

		$message = "create";
		if( $input->id > 0 ){
			$formulaUpdate->setId($input->id);
			$message = "update";
		}

		$result = $formulaUpdate->save();

		$this->resultJson($result, $message, $formulaUpdate->getId());
	}

	function delete(){
		$valid = $this->prepare(["id" => "intval"])->validate(["id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$formulaUpdate = new FormulaUpdate();
		$result = $formulaUpdate->delete($this->input->id);
		$this->resultJson($result, $message);
	}
}