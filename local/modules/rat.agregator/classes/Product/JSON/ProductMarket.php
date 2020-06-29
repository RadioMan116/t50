<?php
namespace Agregator\Product\JSON;

class ProductMarket extends ProductMarketEngine
{
	function getDetail(){
		$arResult = array();
		foreach($this->getSuppliersId() as $id){
			$this->setSupplier($id);
			$arResult[$id] = array(
				"avail" => $this->getAvail(),
				"purchase" => $this->getPurchase(),
				"sale" => $this->getSale(),
			);
		}
		return $arResult;
	}

	function getPurchase(){
		return $this->getValue(self::KEY_PURCHASE_PRICE, self::TRIPLE_CALC_POSITION);
	}

	function getAvail(){
		$avail = $this->getValue(self::KEY_AVAIL, self::TRIPLE_CALC_POSITION);
		if( !isset($avail) )
			$avail = AVAIL_OUT_OF_STOCK;

		return $avail;
	}



	function setPurchaseAuto($value){
		return $this->setValue($value, self::KEY_PURCHASE_PRICE, self::TRIPLE_POSITION_AUTO_VALUE);
	}

	function getPurchaseAuto(){
		return $this->getValue(self::KEY_PURCHASE_PRICE, self::TRIPLE_POSITION_AUTO_VALUE);
	}


	function setPurchaseManual($value){
		return $this->setValue($value, self::KEY_PURCHASE_PRICE, self::TRIPLE_POSITION_MANUAL_VALUE);
	}

	function getPurchaseManual(){
		return $this->getValue(self::KEY_PURCHASE_PRICE, self::TRIPLE_POSITION_MANUAL_VALUE);
	}


	function setPurchaseMode($value){
		return $this->setValue($value, self::KEY_PURCHASE_PRICE, self::TRIPLE_POSITION_MODE);
	}

	function isPurchaseInAutoMode(){
		return (bool) $this->getValue(self::KEY_PURCHASE_PRICE, self::TRIPLE_POSITION_MODE);
	}

	function isPurchaseInManualMode(){
		return !$this->isPurchaseInAutoMode();
	}


	function setAvailAuto($value){
		return $this->setValue($value, self::KEY_AVAIL, self::TRIPLE_POSITION_AUTO_VALUE);
	}

	function getAvailAuto(){
		return $this->getValue(self::KEY_AVAIL, self::TRIPLE_POSITION_AUTO_VALUE);
	}


	function setAvailManual($value){
		return $this->setValue($value, self::KEY_AVAIL, self::TRIPLE_POSITION_MANUAL_VALUE);
	}

	function getAvailManual(){
		return $this->getValue(self::KEY_AVAIL, self::TRIPLE_POSITION_MANUAL_VALUE);
	}


	function setAvailMode($value){
		return $this->setValue($value, self::KEY_AVAIL, self::TRIPLE_POSITION_MODE);
	}

	function isAvailInAutoMode(){
		return (bool) $this->getValue(self::KEY_AVAIL, self::TRIPLE_POSITION_MODE);
	}

	function isAvailInManualMode(){
		return !$this->isAvailInAutoMode();
	}


	function setSale($value){
		return $this->setValue($value, self::KEY_SALE_PRICE);
	}

	function getSale(){
		return $this->getValue(self::KEY_SALE_PRICE);
	}


	function setDateSupply($value){
		return $this->setValue($value, self::KEY_DATE_SUPPLY);
	}

	function getDateSupply(){
		return $this->getValue(self::KEY_DATE_SUPPLY);
	}


	function isChanged(){
		return ( $this->initialData !== $this->toJSON() );
	}

	function getData(){
		return $this->prepareData($this->data);
	}

	function toJSON(){
		return json_encode($this->getData());
	}

	function getErrors(){
		return $this->errors;
	}

	function hasErrors(){
		return !empty($this->errors);
	}

	function getSuppliersId($city = "MSK"){
		$citySuppliersId = self::$mapCitySupplierId[$city];
		$allSuppliersId = array_keys($this->data);
		return array_intersect($citySuppliersId, $allSuppliersId);
	}
}