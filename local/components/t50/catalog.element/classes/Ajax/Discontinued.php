<?php

namespace CatalogElementComponent\Ajax;

use rat\agregator\Product;
use rat\agregator\ProductComment;
use Agregator\Components\BaseAjaxComponent;
use ORM\ORMInfo;
use Agregator\Product\ProductUpdaterWithComment;

class Discontinued extends BaseAjaxComponent
{
	function loadAnalogs(){
		$this->prepare(["search" => "htmlspecialchars"]);
		$this->resultJson(true, "", $this->_loadAnalogs($this->input->search));
	}

	private function _loadAnalogs($str){
		if( mb_strlen($str) < 2 )
			return [];

		$arResult = [];
		$res = Product::clas()::getList([
			"select" => ["ID", "UF_TITLE"],
			"filter" => ["%=UF_TITLE" => "%{$str}%"],
			"limit" => 7,
		]);
		while( $result = $res->fetch() )
			$arResult[] = ["id" => $result["ID"], "text" => $result["UF_TITLE"]];

		return $arResult;
	}

	function loadAnalog(){
		$valid = $this->prepare(["product_id" => "intval"])->validate(["product_id" => "positive"]);
		if( !$valid )
			return [];

		$baseProductId = Product::clas()::getRowById($this->input->product_id);
		$product = [];
		if( $baseProductId["UF_ANALOG_ID"] > 0 ){
			$product = Product::clas()::getRowById($baseProductId["UF_ANALOG_ID"]);
			$product["URL"] = Product::buildDefaultUrl($product);
		}

		$comment = ProductComment::getByProductId($this->input->product_id, "MSK");
		$comment = $comment["common"]["analog"];

		return ["PRODUCT" => $product, "COMMENT" => $comment, "PRODUCT_ID" => $this->input->product_id];
	}

	function setDiscontinued(){
		$valid = $this->prepare([
				"product_id" => "intval",
				"discontinued" => "boolean",
				"comment" => "htmlspecialchars",
			])->validate([
				"product_id" => "positive",
				"discontinued" => "bool",
				"comment" => "min:5",
			]);

		if( !$valid )
			$this->validateErrors();

		$result = ProductUpdaterWithComment::setDiscontinued(
			$this->input->product_id,
			$this->input->discontinued,
			$this->input->comment
		);
		$this->resultJson($result);
	}

	function setAnalog(){
		$valid = $this->prepare([
				"product_id, analog_id" => "intval",
				"comment" => "htmlspecialchars",
			])->validate([
				"product_id, analog_id" => "positive",
				"comment" => "min:5",
			]);

		if( !$valid )
			$this->validateErrors();

		$result = ProductUpdaterWithComment::setAnalog(
			$this->input->product_id,
			$this->input->analog_id,
			$this->input->comment
		);
		$this->resultJson($result);
	}
}