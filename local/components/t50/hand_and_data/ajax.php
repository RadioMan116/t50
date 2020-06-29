<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Agregator\Components\BaseAjaxComponent;
use Agregator\Components\Traits\ComponentDI;
use Agregator\Manager\Manager;
use rat\agregator\Product;
use rat\agregator\ProductPrice;
use rat\agregator\ProductComment;
use Agregator\Product\JSON\ProductMarket;
use T50GlobVars;

class HandAndDataComponent extends BaseAjaxComponent
{
	function actionRefresh(){
		$valid = $this->prepare([
				"modal_key, city" => "htmlspecialchars",
				"bind_id, id" => "intval",
			])->validate([
				"modal_key" => "in: avail_supplier, purchase, avail_shop, sale",
				"city" => "in: MSK, SPB",
				"bind_id, id" => "positive",
			]);

		if( !$valid )
			$this->stopError();

		$allComments = ProductComment::getByProductId($this->input->id, $this->input->city);

		$params = array_fill_keys(["VALUE", "AUTO_VALUE", "IS_MANUAL"], null);

		if( in_array($this->input->modal_key, ["avail_shop", "sale"]) ){
			$cityId = ProductPrice::getEnum("UF_CITY", 0)[$this->input->city]["id"];
			$data = ProductPrice::clas()::getRow(["filter" => [
				"UF_PRODUCT_ID" => $this->input->id,
				"UF_SHOP" => $this->input->bind_id,
				"UF_CITY" => $cityId,
			]]);

			if( empty($data) )
				$this->stopError();

			if( $this->input->modal_key == "avail_shop" ){
				$params["VALUE"] = ( $data["UF_MANUAL_AVAIL"] ? $data["UF_AVAIL_M"] : $data["UF_AVAIL"] );
				$params["AUTO_VALUE"] = $data["UF_AVAIL"];
				$params["IS_MANUAL"] = $data["UF_MANUAL_AVAIL"];
			}

			if( $this->input->modal_key == "sale" ){
				$params["VALUE"] = ( $data["UF_MANUAL_PRICE"] ? $data["UF_PRICE_SALE_M"] : $data["UF_PRICE_SALE"] );
				$params["AUTO_VALUE"] = $data["UF_PRICE_SALE"];
				$params["IS_MANUAL"] = $data["UF_MANUAL_PRICE"];
			}

			$comments = $allComments["shops"][$this->input->bind_id] ?? [];
		}

		if( in_array($this->input->modal_key, ["avail_supplier", "purchase"]) ){
			$product = Product::clas()::getRowById($this->input->id);
			if( empty($product) )
				$this->stopError();

			$marketData = new ProductMarket($product["UF_DATA_MARKET"]);
			try{
				$marketData->setSupplier($this->input->bind_id);
			} catch(\Exception $e){
				$this->stopError();
			}

			if( $this->input->modal_key == "avail_supplier" ){
				$params["VALUE"] = $marketData->getAvail();
				$params["AUTO_VALUE"] = $marketData->getAvailAuto();
				$params["IS_MANUAL"] = $marketData->isAvailInManualMode();
			}

			if( $this->input->modal_key == "purchase" ){
				$params["VALUE"] = $marketData->getPurchase();
				$params["AUTO_VALUE"] = $marketData->getPurchaseAuto();
				$params["IS_MANUAL"] = $marketData->isPurchaseInManualMode();
			}

			$comments = $allComments["suppliers"][$this->input->bind_id] ?? [];
		}

		global $APPLICATION;
		$componentParams = [
			"KEY" => $this->input->modal_key,
			"PRODUCT_ID" => $this->input->id,
			"BIND_ID" => $this->input->bind_id,
			"COMMENT" => $comments ?? [],
			"CITY" => $this->input->city,
			"IS_AJAX" => true,
		];
		$componentParams += $params;
		$APPLICATION->includeComponent("t50:hand_and_data", "", $componentParams);
	}

	private function stopError($error){
		$error = $error ?? "error";
		echo "<font color='red'>${error}</font>";
		die();
	}

}
