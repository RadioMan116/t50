<?php
class T50GlobCache extends \Agregator\Cache
{
	private static function getInstance(){
		static $instance;
		if( empty($instance) )
			$instance = new T50GlobCache;

		return $instance;
	}

	public static function getBx(callable $method, $key, $ttl = 36000){
		$instance = self::getInstance();

		if( ($arResult = $instance->getBxCache($key, $ttl)) != null )
			return $arResult;

		return $instance->saveBxCache($key, call_user_func($method));
	}

	public static function getRds(callable $method, $key, $ttl = 3600, $delete = false){
		if( TESTS_RUNNING === true )
			return call_user_func($method);

		$redis = self::getRedis();

		if( $delete )
			$redis->del($key);

		if( ($arResult = $redis->get($key)) != null )
			return unserialize($arResult);

		$ttl = (int) $ttl;
		if( $ttl <= 0 )
			$ttl = 3600;

		$arResult = call_user_func($method);
		$redis->set($key, serialize($arResult));
		$redis->expire($key, $ttl);

		return $arResult;
	}
}
?>