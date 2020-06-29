<?php

class T50Config
{
	private static $configDir;
	private static $configCache = array();

	static function get(string $args){
		$keys = self::detectKeys($args);
		$name = array_shift($keys);

		if( !isset(self::$configDir) ) // for tests
			self::$configDir = __DIR__ . "/../config";

		$path = realpath(self::$configDir . "/{$name}.php");
		if( !file_exists($path) )
			return;

		if( !isset(self::$configCache[$name]) )
			self::$configCache[$name] = require $path;

		$config = self::$configCache[$name];

		foreach($keys as $key){
			if( !isset($config[$key]) )
				return;

			$config = $config[$key];
		}

		return $config;
	}

	private static function detectKeys(string $keys){
		if( strpos($keys, "[") === false )
			return explode(".", $keys);

		preg_match_all("#(?:\[([^\]]+)\])|(?:([^.]+))#", $keys, $matches);
		$result = [];
		for($i = 0; $i < count($matches[0]); $i++)
			$result[] = ( empty($matches[1][$i]) ? $matches[2][$i] : $matches[1][$i] );

		return $result;
	}
}