<?php

namespace rat\agregator;

use ORM\Traits\PrepareType;

class Complaint extends \ORM\BaseDataManager
{
	public static function getTableName(){
		return 't50_complaints';
	}

	public static function getRefCode(){
		return "CMPL";
	}

	protected static function getRulesMap(){
		return array(
			"UF_DESCRIPTION" => PrepareType::T_STR,
			"UF_MANAGER_ID" => PrepareType::T_MANAGER,
			"UF_DATE_REQUEST" => PrepareType::T_DATETIME,
			"UF_ERROR_TYPE" => PrepareType::T_ENUM,
			"UF_RESULT" => PrepareType::T_ENUM,
			"UF_FILES" => PrepareType::T_ANY,
			"UF_REQUIREMENT" => PrepareType::T_ENUM,
			"UF_REASON" => PrepareType::T_ENUM,
			"UF_DATE_FINISH" => PrepareType::T_DATETIME,
			"UF_DATE_START" => PrepareType::T_DATETIME,
			"UF_ORDER_ID" => PrepareType::T_ANY,
		);
	}
}


