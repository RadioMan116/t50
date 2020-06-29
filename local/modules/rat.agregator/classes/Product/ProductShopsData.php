<?php
namespace Agregator\Product;

use T50Reflection;
use T50GlobVars;

class ProductShopsData
{
	private $data = array();
	private $shopId;
	private $city;

	private $isValid = false;


	const RRC = "RRC";
	const FORMULA = "FORMULA";
	const AVAIL_MANUAL_MODE = "AVAIL_MANUAL_MODE";
	const PRICE_MANUAL_MODE = "PRICE_MANUAL_MODE";

	function setShopId(int $shopId){
		if( $shopId > 0 )
			$this->shopId = $shopId;
		return $this;
	}

	function setCity(string $city){
		if( in_array($city, ["MSK", "SPB"]) )
			$this->city = $city;
		return $this;
	}

	function setData(array $data){
		$this->checkInitialize();

		$valid = true;
		foreach(T50Reflection::getConstants(__CLASS__) as $code){
			$val = 0;
			if( isset($data[$code]) )
		    	$val = (int) $data[$code];

		    switch ($code) {
		    	case self::RRC:
		    		if( $val < 0 )
		    			$valid = false;
		    	break;
		    	case self::FORMULA:
		    		if( !isset(\T50GlobVars::get("FORMULAS")[$val]) )
		    			$valid = false;
		    	break;
		    	case self::AVAIL_MANUAL_MODE:
		    	case self::PRICE_MANUAL_MODE:
		    		if( $val != 0 && $val != 1 )
		    			$valid = false;
		    	break;
		    }

		    $this->data[$this->shopId][$this->city][$code] = $val;
		}

		$this->isValid = $valid;
	}

	function __get($prop){
		if( $prop == "isValid" )
			return $this->isValid;
	}

	function getData($code){
		$this->checkInitialize();

		if( isset($code) )
			return $this->data[$this->shopId][$this->city][$code];

		return $this->data[$this->shopId][$this->city];
	}

	private function checkInitialize(){
		if( !isset($this->shopId) || !isset($this->city) )
			throw new \RuntimeException("not initialized params");

		if( !isset($this->data[$this->shopId]) )
			$this->data[$this->shopId] = array();

		if( !isset($this->data[$this->shopId][$this->city]) )
			$this->data[$this->shopId][$this->city] = array();
	}

}