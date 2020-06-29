<?php

use ReflectionClass;

class T50Reflection
{
	public static function getConstants($className, $pattern = ""){
		static $save = array();
		$key = $className . $pattern;
		if( isset($save[$key]) )
			return $save[$className . $pattern];

		$ref = new ReflectionClass($className);
		if( empty($pattern) ){
			$save[$className] = $ref->getConstants();
			return $save[$className];
		}

		$constants = array();
		foreach($ref->getConstants() as $constant => $field){
			if( preg_match("#{$pattern}#", $constant) )
				$constants[$constant] = $field;
		}
        $save[$key] = $constants;
		return $save[$key];
	}

	public static function getConstantsFl($className, $pattern = ""){
		return array_flip(self::getConstants($className, $pattern));
	}

	public static function hasConstant($className, $const){
		$constants = self::getConstantsFl($className);
		return isset($constants[$const]);
	}

	public static function getArgs($func){
		if( is_array($func) ){
			list($class, $method) = $func;
	    	$ref = new ReflectionMethod($class, $method);
		} else {
	    	$ref = new ReflectionFunction($func);
	    }
	    $result = array();
	    foreach ($ref->getParameters() as $param)
	        $result[] = $param->name;

	    return $result;
	}
}
?>
