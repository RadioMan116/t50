<?php

namespace Agregator;

abstract class Cache
{
	private static $redis;
	private static $bxCache;
	protected $bxCacheTtl = 36000;
	private $mapKeyTtl = [];

	function __construct(){
		$this->initBXCache();
	}

	static function getRedis(){
		if( !isset(self::$redis) )
			self::$redis = new \Predis\Client(\T50Config::get("redis"));

		return self::$redis;
	}

	private function initBXCache(){
		if( self::$bxCache != null )
			return;

		self::$bxCache = \Bitrix\Main\Data\Cache::createInstance();
	}

	protected function getBxCache($key, $bxCacheTtl = 0){
		$bxCacheTtl = ( $bxCacheTtl ? $bxCacheTtl : $this->bxCacheTtl );
		$this->mapKeyTtl[$key] = $bxCacheTtl;
		if( self::$bxCache->initCache($bxCacheTtl, $key, "/bx_cache") )
			return self::$bxCache->getVars();
	}

	protected function saveBxCache($key, $data){
		if( TESTS_RUNNING === true )
			return $data;

		$ttl = $this->mapKeyTtl[$key] ?? false;
		if( empty($data) )
			return $data;

		if( self::$bxCache->startDataCache($ttl, $key) )
			self::$bxCache->endDataCache($data);

		return $data;
	}

}