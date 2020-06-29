<?php

namespace rat\agregator;

use ORM\Traits\PrepareType;

class Delivery extends \ORM\BaseDataManager
{
	public static function getTableName(){
		return 't50_delivery';
	}

	static function getRulesMap(){
		return array(
			"UF_LIFT" => PrepareType::T_ANY,
			"UF_ORDER_ID" => PrepareType::T_ANY,
			"UF_VIP" => PrepareType::T_ANY,
			"UF_MKAD_PRICE" => PrepareType::T_ANY,
			"UF_MKAD_KM" => PrepareType::T_ANY,
			"UF_COSTS" => PrepareType::T_ANY,
			"UF_FLAGS" => PrepareType::T_ENUM,
			"UF_PICKUP_ADDRESS" => PrepareType::T_STR,
			"UF_CONDITIONS" => PrepareType::T_ENUM,
			"UF_TIME" => PrepareType::T_STR,
			"UF_DATE" => PrepareType::T_DATETIME,
			"UF_BASKET_ID" => PrepareType::T_ANY,
		);
	}

	static function calcSumClientShop(array $data, $prefix = ""){
		$arResult = ["client" => 0, "shop" => 0];
		$getType = function($shop) use($prefix) {
			return $shop ? "shop" : "client";
		};
		foreach($data as $item){
			$flags = self::getFlags($item[$prefix . "UF_FLAGS"]);
			$arResult[$getType($flags["US_COSTS"])] += $item[$prefix . "UF_COSTS"];
			$arResult[$getType($flags["US_MKAD"])] +=
							$item[$prefix . "UF_MKAD_KM"] * $item[$prefix . "UF_MKAD_PRICE"];
			$arResult[$getType($flags["US_VIP"])] += $item[$prefix . "UF_VIP"];
			$arResult[$getType($flags["US_LIFT"])] += $item[$prefix . "UF_LIFT"];
		}

		return $arResult;
	}

	static function detectDateTime(array $data, $prefix = ""){
		usort($data, function ($a, $b) use($prefix){
			$code = $prefix . "UF_DATE";
			$a = ( isset($a[$code]) ? $a[$code]->getTimestamp() : 0 );
			$b = ( isset($b[$code]) ? $b[$code]->getTimestamp() : 0 );
			return $a > $b ? -1 : 1;
		});

		$date = \T50Date::bxdate($data[0][$prefix . "UF_DATE"]);
		$time = $data[0][$prefix . "UF_TIME"];

		return compact("date", "time");
	}
}