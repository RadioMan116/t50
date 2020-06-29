<?php

namespace rat\agregator;

use ORM\Traits\PrepareType;

class Installation extends \ORM\BaseDataManager
{
	public static function getTableName(){
		return 't50_installations';
	}

	public static function getRefCode(){
		return "INST";
	}

	protected static function getRulesMap(){
		return array(
			"UF_SERVICE_ID" => PrepareType::T_ANY,
			"UF_PROVIDER" => PrepareType::T_ENUM,
			"UF_PRODUCT_ID" => PrepareType::T_ANY,
			"UF_BASKET_ID" => PrepareType::T_ANY,
			"UF_PRICE_PURCHASE" => PrepareType::T_ANY,
			"UF_COMMENT" => PrepareType::T_STR,
			"UF_COMMISSION" => PrepareType::T_ANY,
			"UF_MASTER" => PrepareType::T_ANY,
			"UF_MKAD_PRICE" => PrepareType::T_ANY,
			"UF_MKAD_KM" => PrepareType::T_ANY,
			"UF_PRICE_SALE" => PrepareType::T_ANY,
			"UF_FLAGS" => PrepareType::T_ENUM,
			"UF_TYPE" => PrepareType::T_ENUM,
			"UF_DATE" => PrepareType::T_DATETIME,
			"UF_MANAGER_ID" => PrepareType::T_ANY,
			"UF_ORDER_ID" => PrepareType::T_ANY,
			"UF_COM_SUPPLIER" => PrepareType::T_ANY,
			"UF_MONTH_ACC_VP" => PrepareType::T_ANY,
			"UF_MONTH_ACC_ZP" => PrepareType::T_ANY,
		);
	}
}