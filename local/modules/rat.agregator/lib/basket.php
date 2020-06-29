<?php

namespace rat\agregator;

use ORM\Traits\PrepareType;
use T50GlobVars;

class Basket extends \ORM\BaseDataManager
{
	public static function getTableName(){
		return 't50_baskets';
	}

	protected static function getRulesMap(){
		return array(
			"UF_CLAIM_EXCHANGE" => PrepareType::T_ANY,
			"UF_CLAIM" => PrepareType::T_BOOL,
			"UF_PAYMENT_TYPE" => PrepareType::T_ENUM,
			"UF_FLAGS" => PrepareType::T_ENUM,
			"UF_COM_SUPPLIER" => PrepareType::T_ANY,
			"UF_COMISSION" => PrepareType::T_ANY,
			"UF_PRICE_PURCHASE" => PrepareType::T_ANY,
			"UF_START_PRICE_SALE" => PrepareType::T_ANY,
			"UF_PRICE_SALE" => PrepareType::T_ANY,
			"UF_QUANTITY" => PrepareType::T_ANY,
			"UF_PRODUCT_ID" => PrepareType::T_ANY,
			"UF_ORDER_ID" => PrepareType::T_ANY,
			"UF_NAME" => PrepareType::T_STR,
			"UF_SUPPLIER" => PrepareType::T_SUPPLIER,
			"UF_COMMISSION" => PrepareType::T_ANY,
			"UF_MONTH_ACC_ZP" => PrepareType::T_ANY,
			"UF_MONTH_ACC_VP" => PrepareType::T_ANY,
		);
	}

	static function prepareValueForHistory($code, $value){
		if( $code == "UF_SUPPLIER" ){
			$name = T50GlobVars::get("CACHE_SUPPLIERS")[$value]["NAME"];
			return ( $value > 0 && !isset($name) ? "{unknow {$value}}" : $name );
		}

		return $value;
	}
}