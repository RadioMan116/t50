<?php

namespace rat\agregator;

use ORM\Traits\PrepareType;

class Account extends \ORM\BaseDataManager
{
	public static function getTableName(){
		return 't50_accounts';
	}

	public static function getRefCode(){
		return "ACC";
	}

	protected static function getRulesMap(){
		return array(
			"UF_SHIPMENT" => PrepareType::T_BOOL,
			"UF_IN_STOCK" => PrepareType::T_BOOL,
			"UF_ACCOUNT_TN_TK" => PrepareType::T_STR,
			"UF_OFFICIAL_PARTNERS" => PrepareType::T_STR,
			"UF_OFFICIAL_OUR" => PrepareType::T_STR,
			"UF_ACCOUNT_DELIVERY" => PrepareType::T_STR,
			"UF_DATE_ARRIVAL" => PrepareType::T_DATETIME,
			"UF_ACCOUNT_PRODUCT" => PrepareType::T_STR,
			"UF_ACCOUNT" => PrepareType::T_STR,
			"UF_BASKET_ID" => PrepareType::T_ANY,
			"UF_ORDER_ID" => PrepareType::T_ANY,
		);
	}

	static function prepareValueForHistory($code, $value){
		if( $code == "UF_DATE_ARRIVAL" )
			return str_replace("01.01.1970", " ", $value);
		return $value;
	}
}