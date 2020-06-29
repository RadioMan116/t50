<?php
namespace Agregator\Product\JSON;

use T50GlobVars;
use T50Date;

class ProductMarketEngine
{
	const KEY_AVAIL            = "A";
	const KEY_PURCHASE_PRICE   = "P";
	const KEY_SALE_PRICE       = "S";
	const KEY_DATE_SUPPLY      = "DS";

	const TRIPLE_POSITION_MODE = 0;
	const TRIPLE_POSITION_AUTO_VALUE = 1;
	const TRIPLE_POSITION_MANUAL_VALUE = 2;
	const TRIPLE_CALC_POSITION = "TRIPLE_CALC_POSITION";

	private static $validSuppliers;
	protected static $mapCitySupplierId = [];

	protected $data = array();
	protected $errors = array();
	protected $supplierId;
	protected $initialData;

	function __construct($data = null){
		if( !isset(self::$validSuppliers) ){
			self::$validSuppliers = \T50GlobVars::get("CACHE_SUPPLIERS");
			self::$mapCitySupplierId = array_fill_keys(["MSK", "SPB"], []);
			foreach(self::$validSuppliers as $id => $item)
			    self::$mapCitySupplierId[$item["PROPERTY_CITY_VALUE"]][] = $id;
		}

		if( isset($data) )
			$this->setRawData($data);
	}

	function setRawData($data){
		if( is_string($data) ){
			$data = json_decode($data, 1);
			if( $data === null )
				$data = array();
		}

		if( !is_array($data) )
			throw new \InvalidArgumentException("empty raw market data");

		$this->data = $this->prepareData($data);
		if( !isset($this->initialData) )
			$this->initialData = $this->toJSON();
		return $this;
	}

	function setSupplier(int $supplierId){
		$this->supplierId = null;

		if( isset(self::$validSuppliers[$supplierId]) )
			$this->supplierId = $supplierId;

		return $this;
	}

	function clearAllAutoAvail(){
		foreach($this->data as $id => $item){
			$this->supplierId = $id;
			$this->setAvailAuto(AVAIL_OUT_OF_STOCK);
		}
		$this->supplierId = null;
	}

	protected function prepareData($data){
		static $codes;
		if( !isset($codes) )
			$codes = \T50Reflection::getConstants(__CLASS__, "^KEY_");

		foreach($data as $supplierId => $supplierData){
			if( !isset(self::$validSuppliers[$supplierId]) ){
				unset($data[$supplierId]);
				continue;
			}

			$checkedSupplierData = array();
			foreach($codes as $code){
				if( !isset($supplierData[$code]) )
					continue;

				$value = $this->checkValue($code, $supplierData[$code]);
				if( empty($value) )
					continue;

				$checkedSupplierData[$code] = $value;
			}
			$data[$supplierId] = $checkedSupplierData;
		}

		$this->errors = [];

		return $data;
	}

	protected function checkValue($code, $value){
		switch($code){
			case self::KEY_AVAIL:
				$value = $this->prepareTriple($value, [$this, "prepareAvail"]);
			break;
			case self::KEY_PURCHASE_PRICE:
				$value = $this->prepareTriple($value, [$this, "preparePrice"]);
			break;
			case self::KEY_SALE_PRICE:
				$value = $this->preparePrice($value);
			break;
			case self::KEY_DATE_SUPPLY:
				if( !T50Date::check($value, "d.m.Y") )
					$value = false;
			break;
		}
		return $value;
	}

	protected function setValue($value, $code, $tripplePosition = null){
		if( !isset($this->supplierId) )
			throw new \RuntimeException("not selected supplier");

		if( isset($tripplePosition) )
			$value = $this->prepareTripleArray($code, $value, $tripplePosition);

		$newValue = $this->checkValue($code, $value);
		if( $newValue === false )
			return false;

		$oldValue = $this->data[$this->supplierId][$code];
		if( $newValue == $oldValue )
			return true;

		if( !isset($newValue) )
			unset($this->data[$this->supplierId][$code]);

		$this->data[$this->supplierId][$code] = $newValue;
		return true;
	}

	protected function getValue($code, $tripplePosition = null){
		if( !isset($this->supplierId) )
			throw new \RuntimeException("not selected supplier");

		$value = $this->data[$this->supplierId][$code];

		if( isset($tripplePosition) ){
			if( !isset($value) )
				$value = $this->prepareTripleArray($code);

			if( $tripplePosition === self::TRIPLE_CALC_POSITION ){
				$value = ( $value[0] ? $value[1] : $value[2] );
			} else {
				$value = $value[$tripplePosition];
			}
		}

		return $value;
	}

	protected function prepareAvail($availCode){
		if( !isset($availCode) )
			return AVAIL_OUT_OF_STOCK;

		if( !in_array($availCode, [AVAIL_IN_STOCK, AVAIL_BY_REQUEST, AVAIL_OUT_OF_STOCK]) ){
			$this->addError("prepareAvail", $availCode);
			return false;
		}

		return $availCode;
	}

	protected function preparePrice($priceValue){
		if( !isset($priceValue) )
			return null;

		if( $priceValue <= 0  ){
			$this->addError("preparePrice", $priceValue);
			return false;
		}

		$priceValue = (int) $priceValue;
		return $priceValue;
	}

	protected function prepareTriple($value, callable $prepareMethod){
		if( !is_array($value) || count($value) != 3 ){
			$this->addError("prepareTriple", $value);
			return false;
		}

		list($mode, $autoValue, $manualValue) = $value;
		$mode = ( $mode ? 1 : 0 );

		$autoValue = $prepareMethod($autoValue);
		if( $autoValue === false )
			return false;

		$manualValue = $prepareMethod($manualValue);
		if( $manualValue === false )
			return false;

		return [$mode, $autoValue, $manualValue];
	}

	private function prepareTripleArray($code, $value = null, $index = null){
		$result = [1, null, null];
		$currentData = $this->data[$this->supplierId][$code];
		if( isset($currentData) )
			$result = $currentData;

		if( !isset($index) )
			return $result;

		$result[$index] = $value;
		return $result;
	}

	protected function addError($text, $value){
		$this->errors[] = "ProductMarket error: " . $text . ". Value = " . var_export($value, 1) . " (supplierId={$this->supplierId})";
	}
}