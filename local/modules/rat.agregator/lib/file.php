<?php

namespace rat\agregator;

use ORM\Traits\PrepareType;

class File extends \ORM\BaseDataManager
{
	public static function getTableName(){
		return 't50_files';
	}

	public static function getRefCode(){
		return "FILE";
	}

	protected static function getRulesMap(){
		return array(
			"UF_MONTH_PAY" => PrepareType::T_ANY,
			"UF_ORDER_ID" => PrepareType::T_ANY,
			"UF_REASON" => PrepareType::T_STR,
			"UF_AMOUNT" => PrepareType::T_ANY,
			"UF_RESPONSIBLE_ID" => PrepareType::T_ANY,
			"UF_INITIATOR_ID" => PrepareType::T_ANY,
			"UF_DATE" => PrepareType::T_DATETIME,
		);
	}
}


