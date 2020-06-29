<?php

namespace OrdersDetailComponent\AJAX;

use rat\agregator\Basket as BasketOrm;
use Agregator\Components\BaseAjaxComponent;
use Agregator\Order\History;
use Agregator\Order\Basket as OrderBasket;
use Agregator\Order\BasketProductInfo;
use T50GlobVars;

class Basket extends BaseAjaxComponent
{
	function load($status = true){
		$valid = $this->prepare(["order_id" => "intval"])->validate(["order_id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$result = $this->loadByOrderId($this->input->order_id);
		$this->resultJson($status, "", $result);
	}

	function loadByOrderId(int $orderId){
		$items = $this->loadItems($orderId);

		$result = $this->calcSum($items);
		$result["items"] = $items;
		$result["pay_types"] = BasketOrm::getEnumForJson("UF_PAYMENT_TYPE");
		return $result;
	}

	private function calcSum($items){
		$result = array_fill_keys(["sale_sum", "purchase_sum", "commission_sum"], 0);
		foreach($items as $item){
			if( $item["claim"] )
				continue;
		    $result["sale_sum"] += $item["sale"] * $item["quantity"];
	    	$result["purchase_sum"] += $item["purchase"] * $item["quantity"];
	    	$result["commission_sum"] += $item["commission"];
		}
		return $result;
	}

	private function loadItems($orderId){
		$data = (new OrderBasket)->getList($orderId);
		$data = array_map(function ($item){
			$item["UF_COMMISSION"] *= $item["UF_QUANTITY"];
			return $item;
		}, $data);

		$historyData = (new History())->getComments($orderId);

		$data = $this->resortList($data);
		array_walk($data, [$this, "convertToJson"], $historyData);
		return $data;
	}

	private function resortList($data){
		$exchangeItems = array_filter($data, function ($item){
			return $item["UF_CLAIM_EXCHANGE"] > 0;
		});
		foreach($exchangeItems as $indexExchange => $itemExchange){
		    foreach($data as $indexClaim => $itemClaim){
		        if( $itemClaim["UF_CLAIM"] == 1 && $itemClaim["ID"] == $itemExchange["UF_CLAIM_EXCHANGE"] ){
		        	$tmp = array_splice($data, $indexExchange, 1);
		        	array_splice($data, $indexClaim + 1, 0, $tmp);
		        }
		    }
		}
		return $data;
	}

	private function convertToJson(&$item, $key, $historyData){
		$result = [];
		$flags = BasketOrm::getFlags($item["UF_FLAGS"]);
		$result["id"] = $item["ID"];
		$result["supplier_id"] = $item["UF_SUPPLIER"];
		$result["product_id"] = $item["UF_PRODUCT_ID"];
		$result["quantity"] = $item["UF_QUANTITY"];
		$result["sale"] = $item["UF_PRICE_SALE"];
		$result["purchase"] = $item["UF_PRICE_PURCHASE"];
		$result["is_manual_sale"] = $flags["manual_sale"];
		$result["is_manual_purchase"] = $flags["manual_purchase"];
		$result["commission"] = $item["UF_COMMISSION"];
		$result["pay_type"] = $item["UF_PAYMENT_TYPE"];
		$result["claim"] = ( $item["UF_CLAIM"] > 0 );
		$result["claim_replace"] = ( $item["UF_CLAIM_EXCHANGE"] > 0 );
		$result["comments"] = $historyData[$item["ID"]];
		$result["title"] = $item["UF_NAME"];
		$result["url"] = ( $item["UF_PRODUCT_ID"] > 0 ? $item["UF_PRODUCT_URL"] : null );
		$item = $result;
	}

	function create(){
		$valid = $this->prepare([
				"order_id, product_price_id" => "intval",
				"exchange_basket_id" => "null, intval",
			])->validate([
				"order_id, product_price_id, exchange_basket_id" => "positive"
			]);
		if( !$valid )
			$this->validateErrors();

		$productInfo = new BasketProductInfo;
		$productInfo->setPriceId($this->input->product_price_id);

		$basketId = (new OrderBasket)->create(
			$this->input->order_id,
			$productInfo,
			(int) $this->input->exchange_basket_id
		);

		if( $basketId > 0 )
			$this->resultJson(true, "", ["id" => $basketId]);

		$this->resultJson(false, "cannot create basket");
	}

	function setManual(){
		$valid = $this->prepare([
				"order_id, id, price" => "intval",
				"comment, modal_key" => "htmlspecialchars",
			])->validate([
				"order_id, id" => "positive",
				"price" => "min:0",
				"comment" => "min: 5",
				"modal_key" => "in: basket_sale, basket_purchase",
			]);
		if( !$valid )
			$this->validateErrors();

		$basket = new OrderBasket();
		$basket->init($this->input->order_id, $this->input->id);

		if( $this->input->modal_key == "basket_sale" )
			$result = $basket->setManualSale($this->input->price, $this->input->comment);

		if( $this->input->modal_key == "basket_purchase" )
			$result = $basket->setManualPurchase($this->input->price, $this->input->comment);

		$this->resultJson($result);
	}

	function update(){
		$valid = $this->prepare([
				"multiple_update_mode" => "null, boolean",
				"order_id, id" => "intval",
				"code" => "htmlspecialchars",
				"value" => "intval",
			])->validate([
				"multiple_update_mode" => "bool",
				"order_id, id" => "positive",
				"code" => "in: supplier_id, quantity, pay_type, claim, month_vp, month_zp, suppl_commission",
			]);
		if( !$valid )
			$this->validateErrors();

		$basket = new OrderBasket();
		$basket->init($this->input->order_id, $this->input->id);

		switch ($this->input->code) {
			case "supplier_id":
				if( $this->input->multiple_update_mode === true )
					$basket->setMutilpleUpdateMode();
				$this->resultJson($basket->setSupplier($this->input->value));
			case "quantity":
				$this->resultJson($basket->setQuantity($this->input->value));
			case "pay_type":
				$this->resultJson($basket->setPaymentType($this->input->value));
			case "claim":
				$this->resultJson($basket->setIsClaim((bool) $this->input->value));
			case "month_vp":
				$this->resultJson($basket->setMonthVP($this->input->value));
			case "month_zp":
				$this->resultJson($basket->setMonthZP($this->input->value));
			case "suppl_commission":
				$this->resultJson($basket->setSupplierCommission($this->input->value));
		}
	}

	function delete(){
		$valid = $this->prepare([
				"order_id, id" => "intval",
			])->validate([
				"order_id, id" => "positive",
			]);
		if( !$valid )
			$this->validateErrors();

		$basket = new OrderBasket();
		$basket->init($this->input->order_id, $this->input->id);
		$success = $basket->delete();
		$this->load($success);
	}
}