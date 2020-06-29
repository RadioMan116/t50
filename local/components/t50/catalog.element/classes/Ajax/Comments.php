<?php

namespace CatalogElementComponent\Ajax;

use rat\agregator\Product;
use Agregator\IB\Element;
use Agregator\IB\Elements;
use Agregator\Components\BaseAjaxComponent;

class Comments extends BaseAjaxComponent
{
	function load(){
		$valid = $this->prepare(["product_id" => "intval",])->validate(["product_id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$this->_load($this->input->product_id);
	}

	private function _load(int $id){
		$product = Product::clas()::getRowById($id);
		if( empty($product) )
			$this->resultJson(false, "product not found");

		$comments = [];

		if( !empty($product["UF_COMMENT"]) )
			$comments["product"] = $product["UF_COMMENT"];

		$brandId = intval($product["UF_BRAND"]);
		if( $brandId > 0 ){
			$brand = (new Elements("brands"))->select("PREVIEW_TEXT")->getOneFetchById($brandId);
			if( !empty($brand["PREVIEW_TEXT"]) )
			 	$comments["brand"] = $brand["PREVIEW_TEXT"];
		}

		$data = [];
		foreach($comments as $code => $comment)
		    $data[] = ["code" => $code, "text" => $comment];

		$this->resultJson(true, "", ["comments" => $data]);
	}

	function update(){
		$valid = $this->prepare([
				"product_id, id" => "intval",
				"comment_type, comment" => "htmlspecialchars",
				"clear" => "null, boolean",
			])->validate([
				"product_id, id" => "positive",
				"comment_type" => "in:product, brand",
				"clear" => "bool",
				"comment" => "min:5",
			]);

		if( !$valid )
			$this->validateErrors();

		$comment = $this->input->comment;
		if( $this->input->clear )
			$comment = "";

		$success = false;
		switch ($this->input->comment_type) {
			case "product":
				$success = Product::clas()::update($this->input->id, ["UF_COMMENT" => $comment])->isSuccess();
			break;
			case "brand":
				$success = (new Element("brands"))->update($this->input->id, ["PREVIEW_TEXT" => $comment]);
			break;
		}

		if( !$success )
			$this->resultJson(false, "cannot update for type " . $this->input->comment_type);

		$this->_load($this->input->product_id);
	}

}