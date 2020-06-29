<?php

namespace rat\agregator;

use ORM\Traits\PrepareType;

class OrderHistory extends \ORM\BaseDataManager
{
	public static function getTableName(){
		return 't50_order_history';
	}

	public static function getRefCode(){
		return "HSTR";
	}

	protected static function getRulesMap(){
		return array(
			"UF_ORDER_ID" => PrepareType::T_ANY,
			"UF_COMMENT" => PrepareType::T_STR,
			"UF_DATE_TIME" => PrepareType::T_DATETIME,
			"UF_AFTER" => PrepareType::T_STR,
			"UF_BEFORE" => PrepareType::T_STR,
			"UF_MANAGER_ID" => PrepareType::T_ANY,
			"UF_ENTITY_ID" => PrepareType::T_ANY,
			"UF_ENTITY" => PrepareType::T_ENUM,
			"UF_CODE" => PrepareType::T_STR,
		);
	}
}


