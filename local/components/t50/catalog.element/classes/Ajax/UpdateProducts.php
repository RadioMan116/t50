<?php

namespace CatalogElementComponent\Ajax;

use Agregator\Components\BaseAjaxComponent;
use Agregator\Product\ProductsRecalc;
use rat\agregator\ProductPrice;
use Agregator\Product\Product;
use Agregator\Product\Brand;

class UpdateProducts extends BaseAjaxComponent
{
	function changeFormula(){
		$valid = $this->prepare([
			"formula_id" => "intval",
			"prices_id*" => "intval"
		])->validate([
			"formula_id" => "positive",
			"prices_id*" => "positive",
		]);
		if( !$valid )
			$this->validateErrors();

		$result = ProductsRecalc::changeFormula($this->input->formula_id, $this->input->prices_id);
		$unids = [];
		if( $result )
			$unids = ProductPrice::getUnidsByFromulaId($this->input->formula_id);

		$unids = array_map("intval", $unids);
		$this->resultJson($result, "", array_values($unids));
	}

	function getUnidsByFormula(){
		$valid = $this->prepare(["formula_id" => "intval"])->validate(["formula_id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$unids = ProductPrice::getUnidsByFromulaId($this->input->formula_id);
		$this->resultJson(true, "", array_values($unids));
	}

	function recalcProducts(){
		$valid = $this->prepare([
			"unids*" => "intval",
		])->validate([
			"unids" => "min:1",
			"unids*" => "positive",
		]);
		if( !$valid )
			$this->validateErrors();

		$result = ProductsRecalc::recalcByUnids($this->input->unids);
		$this->resultJson($result);
	}

	function setFormulaForBrand(){
		$valid = $this->prepare([
				"brand_id, formula_id" => "intval",
			])->validate([
				"brand_id" => "positive",
				"formula_id" => "min:0",
			]);
		if( !$valid )
			$this->validateErrors();

		$result = Brand::setFormula($this->input->brand_id, $this->input->formula_id);
		$this->resultJson($result);
	}

}