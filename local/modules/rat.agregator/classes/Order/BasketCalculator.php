<?php

namespace Agregator\Order;

use rat\agregator\Basket as BasketOrm;
use Agregator\Product\JSON\ProductMarket;

class BasketCalculator
{
	private $data;
	private $history;
	private $market;
	private $flags;

	function setHistory(History $history){
		$this->history = $history;
	}

	function setData(array $data){
		$this->data = $data;
		$this->market = new ProductMarket($data["BASKET_PROD_UF_DATA_MARKET"]);
		$this->flags = BasketOrm::getFlags($data["UF_FLAGS"]);
	}

	function getData(){
		if( !isset($this->history) || !isset($this->data) )
			throw new \RuntimeException("BasketCalculator not initialized");

		$data = [];
		$data["UF_PRICE_PURCHASE"] = $this->calcPurchase();
		$data["UF_PRICE_SALE"] = $this->calcSale();
		$data["UF_COMMISSION"] = $this->calcCommission();
		return $data;
	}

	private function calcPurchase(){
		$purchase = $this->data["UF_PRICE_PURCHASE"];
		if( $this->flags["manual_purchase"] )
			return $purchase;

		$supplierId = $this->data["UF_SUPPLIER"];
		if( $supplierId > 0 )
			$newPurchase = $this->market->setSupplier($supplierId)->getPurchase();

		// calc ...

		if( isset($newPurchase) && $newPurchase != $purchase ){
			$this->history->addChanges(
				BasketOrm::getTableName(),
				"UF_PRICE_PURCHASE",
				array($purchase, $newPurchase),
				false
			);
			$this->data["UF_PRICE_PURCHASE"] = $newPurchase;
			return $newPurchase;
		}

		return $purchase;
	}

	private function calcSale(){
		$sale = $this->data["UF_PRICE_SALE"];
		if( $this->flags["manual_sale"] )
			return $sale;

		$newSale = $sale;

		// calc ...

		if( $newSale != $sale ){
			$this->history->addChanges(
				BasketOrm::getTableName(), "UF_PRICE_SALE",
				array($sale, $newSale), false
			);
			$this->data["UF_PRICE_SALE"] = $newSale;
			return $newSale;
		}

		return $sale;
	}

	private function calcCommission(){
		$this->data["UF_COMMISSION"] = $this->data["UF_PRICE_SALE"] - $this->data["UF_PRICE_PURCHASE"];
		return $this->data["UF_COMMISSION"];
	}
}
