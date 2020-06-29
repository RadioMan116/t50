<?php

namespace CatalogElementComponent\Ajax;

use rat\agregator\ProductComment;
use Agregator\Product\Prices;
use Agregator\Product\Product;
use Agregator\Components\BaseAjaxComponent;
use Agregator\Product\ProductUpdaterWithComment as UpdWithComment;

class EditManual extends BaseAjaxComponent
{
	function exec(){
		$this->prepare([
			"id, bind_id, value" => "intval",
			"date_end" => "null, htmlspecialchars",
			"comment, modal_key, city" => "htmlspecialchars, trim",
		]);

		$rules = [
			"modal_key" => "in:avail_shop,avail_supplier,sale,purchase",
			"id, bind_id" => "positive",
			"date_end" => "date_format:d.m.Y",
			"city" => "in:MSK,SPB",
			"comment" => "min:5",
			"value" => "positive",
		];
		if( $this->validate(["date_end" => "required|before_or_equal:today"]) ){
			$reset = true;
			unset($rules["comment"], $rules["value"]);
		}

		$valid = $this->validate($rules);
		if( !$valid )
			$this->validateErrors();

		$input = $this->input;

		$result = false;

		switch( $input->modal_key ){
			case "avail_shop":
			case "sale":
				$prices = new Prices();
				$prices->setProductId($input->id)->setShopId($input->bind_id)->setCity($input->city);
				if( $input->modal_key == "avail_shop" )
					$result = $prices->setManualAvail($input->value, $input->comment, $input->date_end);

				if( $input->modal_key == "sale" )
					$result = $prices->setManualPrice($input->value, $input->comment, $input->date_end);
			break;
			case "avail_supplier":
				$result = UpdWithComment::setManualAvailForSupplier(
					$input->id, $input->bind_id, $input->value, $input->comment, $input->date_end
				);
			break;
			case "purchase":
				$result = UpdWithComment::setManualPurchaseForSupplier(
					$input->id, $input->bind_id, $input->value, $input->comment, $input->date_end
				);
			break;
		}
		$message = "";
		if( $result && $reset )
			$message = "reset";

		$this->resultJson($result, $message);
	}
}