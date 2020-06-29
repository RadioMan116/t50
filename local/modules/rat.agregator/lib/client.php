<?php

namespace rat\agregator;

use ORM\Traits\PrepareType;

class Client extends \ORM\BaseDataManager
{
	public static function getTableName(){
		return 't50_clients';
	}

	protected static function getRulesMap(){
		return array(
			"UF_IS_ENTITY" => PrepareType::T_BOOL,
			"UF_ELEVATOR" => PrepareType::T_ENUM,
			"UF_INTERCOM" => PrepareType::T_STR,
			"UF_APARTMENT" => PrepareType::T_STR,
			"UF_FLOOR" => PrepareType::T_STR,
			"UF_PORCH" => PrepareType::T_STR,
			"UF_HOUSE_NUMBER" => PrepareType::T_STR,
			"UF_STREET" => PrepareType::T_STR,
			"UF_CITY" => PrepareType::T_STR,
			"UF_PHONE2" =>PrepareType::T_STR ,
			"UF_FIO2" => PrepareType::T_STR,
			"UF_FIO" => PrepareType::T_STR,
			"UF_PHONE" => PrepareType::T_STR,
			"UF_REQUISITES" => PrepareType::T_STR,
			"UF_EMAIL" => PrepareType::T_STR,
		);
	}

	static function buildFullAddress(array $data, $prefix = ""){
		$fields = [
			"UF_CITY" => "г. ",
			"UF_STREET" => "ул. ",
			"UF_HOUSE_NUMBER" => "д. ",
			"UF_PORCH" => "под-д ",
			"UF_INTERCOM" => "домофон ",
			"UF_FLOOR" => "этаж ",
			"UF_APARTMENT" => "кв. ",
		];
		$result = [];
		foreach($fields as $field => $short){
			$value = trim($data[$prefix . $field]);
			if( empty($value) )
				continue;

		    $result[] = $short . $value;
		}

		return implode(", ", $result);
	}
}
