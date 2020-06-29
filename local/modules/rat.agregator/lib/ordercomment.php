<?php

namespace rat\agregator;

use ORM\Traits\PrepareType;

class OrderComment extends \ORM\BaseDataManager
{
	public static function getTableName(){
		return 't50_order_comments';
	}

	public static function getRefCode(){
		return "ORD_CMNT";
	}

	protected static function getRulesMap(){
		return array(
			"UF_DATE_REMIND" => PrepareType::T_DATETIME,
			"UF_DATE_CREATE" => PrepareType::T_DATETIME,
			"UF_MANAGER_ID" => PrepareType::T_ANY,
			"UF_COMMENT" => PrepareType::T_STR,
			"UF_KEY" => PrepareType::T_ENUM,
			"UF_ORDER_ID" => PrepareType::T_ANY,
		);
	}
}

