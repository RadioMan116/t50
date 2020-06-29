<?php
namespace Agregator\Product\Calculator;

class CalculationResult
{
	private $data;

	const AVAIL = "AVAIL";
	const PRICE = "PRICE";
	const PURCHASE = "PURCHASE";
	const SUPPLIER = "SUPPLIER";

	function __construct($data = null){
		if( isset($data) ){
			$this->data = $data;
			return;
		}

		$data = [];
		$data[self::AVAIL] = AVAIL_OUT_OF_STOCK;
		$data[self::PRICE] = null;
		$data[self::PURCHASE] = null;
		$data[self::SUPPLIER] = null;
		$this->data = $data;
	}

	function setPrice(int $price){
		$this->data[self::PRICE] = $price;
	}

	function setAvail(int $avail){
		$this->data[self::AVAIL] = $avail;
	}

	function setSupplier(int $supplierId){
		$this->data[self::SUPPLIER] = $supplierId;
	}

	function setPurchase(int $purchase){
		$this->data[self::PURCHASE] = $purchase;
	}

	function getData($code){
		if( isset($code) )
			return $this->data[$code];

		return $this->data;
	}
}