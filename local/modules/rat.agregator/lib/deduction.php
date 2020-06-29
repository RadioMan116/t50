<?php

namespace rat\agregator;

use ORM\Traits\PrepareType;
use Bitrix\Main\Entity;

class Deduction extends \ORM\BaseDataManager
{
	public static function getTableName(){
		return 't50_deductions';
	}

	protected static function getRulesMap(){
		return array(
			"UF_ORDER_ID" => PrepareType::T_ANY,
			"UF_AMOUNT" => PrepareType::T_ANY,
			"UF_TYPE" => PrepareType::T_ENUM,
			"UF_COMMENT" => PrepareType::T_STR,
			"UF_MANAGER_ID" => PrepareType::T_ANY,
			"UF_DATE" => PrepareType::T_DATETIME,
		);
	}

	static function getSum(int $orderId){
		$data = self::clas()::getRow([
			"select" => ["UF_ORDER_ID", "SUM"],
			"filter" => ["UF_ORDER_ID" => $orderId],
			"runtime" => [new Entity\ExpressionField("SUM", "SUM(UF_AMOUNT)")],
			"group" => ["UF_ORDER_ID"]
		]);
		if( empty($data) )
			return 0;

		return (int) $data["SUM"];
	}
}

