<?php
class T50ArrayHelper
{
	public static function isEquals($a, $b){
		sort($a);
		sort($b);
		return $a == $b;
	}

	public static function isEmpty($arr){
		return ( empty($arr) || !is_array($arr) );
	}

	public static function getVal(){
		$args = func_get_args();
		$argsCount = count($args);
		if( $argsCount < 2 )
			return null;

		$data = $args[0];
		for($i = 1; $i < $argsCount; $i++){
			$key = $args[$i];
			$data = $data[$key];
		}

		return $data;
	}

	public static function setVal(){
		$args = func_get_args();
		$argsCount = count($args);
		if( $argsCount < 3 )
			throw new \InvalidArgumentException(
				"wrong number of arguments (must be >= 3)"
			);

		$data = $args[0];
		$replaceData = array($args[$argsCount - 2] => $args[$argsCount - 1]);
		for($i = $argsCount - 3; $i > 0; $i--)
			$replaceData = array($args[$i] => $replaceData);

		$data = array_replace_recursive($data, $replaceData);
		return $data;
	}

	public static function isInt($array, $moreThenNull = true){
		if( !is_array($array) )
			return false;

		foreach($array as $val){
			if( !is_numeric($val) )
				return false;

			if( is_string($val) ){
				if( substr_count($val, ".") )
					return false;
			} elseif( !is_int($val) ){
				return false;
			}

			$val = (int) $val;
			if( $moreThenNull && $val <= 0 )
				return false;
		}
		return true;
	}

	public static function addItem($array, $val){
		if( in_array($val, $array) )
			return $array;
		$array[] = $val;
		return $array;
	}

	public static function remItem($array, $val){
		$index = array_search($val, $array);
		if( $index === false )
			return $array;

		unset($array[$index]);
		return $array;
	}

	public static function isOverlaps($arr1, $arr2){
		$cross = array_intersect($arr1, $arr2);
		return  !empty($cross);
	}

	public static function simpleIncExc(array $data, $include = [], $exclude = []){
		if( empty($include) )
			$include = $data;
		$include = array_fill_keys($include, 1);
		$exclude = array_fill_keys($exclude, 1);
		return array_filter($data, function($val)use($include, $exclude){
			if( $exclude[$val] )
				return false;

			return $include[$val];
		});
	}

	public static function filterMap($arr, $callbckFilterMap, $resetIndexes = false){
		$arResult = array();
		foreach($arr as $key => $item){
			if( call_user_func_array($callbckFilterMap, array(&$item, $key)) ){
				if( $resetIndexes )
					$arResult[] = $item;
				else
					$arResult[$key] = $item;
			}
		}
		return  $arResult;
	}

	public static function inArray($needleArray, $haystackArray){
		$foreignElements = array_diff($needleArray, $haystackArray);
		return empty($foreignElements);
	}

	public static function inMultArray($assocArray, $keysTree, $value, $returnBaseKey = false){
		if( !is_array($keysTree) )
			$keysTree = array($keysTree);

		$valueIsArray = is_array($value);
		$foundKeys = array();

		foreach($assocArray as $baseKey => $item){
			foreach($keysTree as $innerKey){
				if( !isset($item[$innerKey]) )
					continue;

				$item = $item[$innerKey];
			}

			if( $valueIsArray && in_array($item, $value) ){
				if( !$returnBaseKey )
					return true;

				$foundKeys[] = $baseKey;
			}


			if( !$valueIsArray && $item == $value )
				return ( $returnBaseKey ? $baseKey : true );

		}

		return ( empty($foundKeys) ? false : $foundKeys );
	}

	public static function filterByKeys(array $data, array $keys, $exclude = false){
		$arResult = array();
		if( $exclude )
			$arResult = $data;

		foreach($keys as $key){
			if( !isset($data[$key]) )
				continue;

			if( $exclude ){
				unset($arResult[$key]);
			} else {
				$arResult[$key] = $data[$key];
			}
		}

		return $arResult;
	}

	public static function pluck($data, $field, $key = null){
		$result = array();
		foreach($data as $k => $item){
			if( isset($key) )
				$k = $item[$key];

		    $result[$k] = $item[$field];
		}

		return $result;
	}

	public static function keyBy($data, $field){
		$result = array();
		foreach($data as $item)
		    $result[$item[$field]] = $item;

		return $result;
	}

	public static function find(array $data, callable $filter){
		$data = array_filter($data, $filter);
		return ( empty($data) ? null : current($data) );
	}

	public static function toIndexed(array $data, array $map){
		$result = [];
		foreach($data as $key => $item){
			$row = [];
			if( isset($map[0]) )
				$row[$map[0]] = $key;

			if( is_array($item) ){
				foreach($item as $code => $value){
					if( isset($map[$code]) )
				    	$row[$map[$code]] = $value;
				}
			} elseif( isset($map[1]) ) {
				$row[$map[1]] = $item;
			}

			$result[] = $row;
		}
		return $result;
	}
}
?>
