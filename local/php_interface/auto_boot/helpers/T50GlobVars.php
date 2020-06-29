<?php
class T50GlobVars
{
	private static $poolCallbacks = array();
	private static $cache = array();

	static function set($varName, $value){
		if( is_callable($value) )
			self::$poolCallbacks[$varName] = $value;
		else
			self::$cache[$varName] = $value;

		if( self::isStartedClearCache() )
			self::get($varName);
	}

	static function del($varName){
		unset(self::$cache[$varName]);
	}

	static function get($varName, ...$args){
		if( !isset(self::$poolCallbacks[$varName]) )
			return self::$cache[$varName];

		$argsKey = serialize($args);
		if( isset(self::$cache[$varName][$argsKey]) )
			return self::$cache[$varName][$argsKey];

		self::$cache[$varName][$argsKey] = call_user_func_array(self::$poolCallbacks[$varName], $args);
		return self::$cache[$varName][$argsKey];
	}

	static function isStartedClearCache(){
		return ( $_GET["clear_cache"] == "Y" && $GLOBALS["USER"]->getId() == 1 );
	}
}
