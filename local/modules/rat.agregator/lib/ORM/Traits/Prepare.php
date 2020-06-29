<?php

namespace ORM\Traits;

use T50GlobVars;

trait Prepare
{
	static function prepare($data){
		$rules = static::getRulesMap();
		$rules["ID"] = PrepareType::T_ANY;
		foreach($data as $code => $value){
			if( !isset($rules[$code]) ){
				$table = static::getTableName();
				throw new \RuntimeException("System: not prepared column '{$code}' for table '{$table}'");
			}

		    if( $rules[$code] == PrepareType::T_STR )
		    	$data["~" . $code] = $value;

		    $rule = $rules[$code];

		    if( is_array($value) ){
		    	foreach($value as $k => $val)
		    	    $value[$k] = self::prepareVal($rule, $code, $val);
		    } else {
		    	$value = self::prepareVal($rule, $code, $value);
		    }

		    $data[$code] = $value;
		}

		return $data;
	}

	private static function prepareVal($rule, $code, $value){
		if( is_callable($rule) )
			return $rule($value);

		switch ($rule) {
			case PrepareType::T_DATETIME:
			case PrepareType::T_ANY:
				return $value;
			case PrepareType::T_ENUM:
				return self::prepareValEnum($value, $code);
			case PrepareType::T_BOOL:
				return ( $value === "1" || $value === 1 );
			case PrepareType::T_STR:
				return htmlspecialchars(trim($value));
			case PrepareType::T_SHOP:
				return self::prepareValCache($value, "SHOPS");
			case PrepareType::T_CATEGORY:
				return self::prepareValCache($value, "CATEGORIES");
			case PrepareType::T_SUPPLIER:
				return self::prepareValCache($value, "SUPPLIERS");
			case PrepareType::T_BRAND:
				return self::prepareValCache($value, "BRAND_NAMES");
			case PrepareType::T_MANAGER:
				return self::prepareManager($value);
        }
	}

	private function prepareManager($managerId){
		static $managers;
		$managers = $managers ?? T50GlobVars::get("MANAGERS");
		return $managers[$managerId];
	}

	private static function prepareValEnum($val, $code){
		static $enums;
		$result = [];
		$val = (int) $val;
		if( $val < 0 )
			return $result;

		$enums = $enums ?? T50GlobVars::get("HLPROPS");
		$enumValues = $enums[static::getTableName()][$code];

		foreach($enumValues as $code => $enumValue){
		    if( $enumValue["id"] == $val ){
		    	$result = $enumValue;
		    	$result["code"] = $code;
		    	break;
		    }
		}

		return $result;
	}

	private static function prepareValCache($val, $cacheCode){
		$result = [];
		$val = (int) $val;
		if( $val < 0 )
			return $result;

		$data = T50GlobVars::get("CACHE_" . $cacheCode);
		if( isset($data[$val]) )
			$result = \T50ArrayHelper::filterByKeys($data[$val], ["ID", "NAME", "CODE"]);

		return $result;
	}
}
