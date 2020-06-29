<?php
namespace Agregator\Product;

use Agregator\IB\IBlocks;
use rat\agregator\Product;

class ProductShopsAttachment
{
	static function attach(int $shopId, $xmlItem, $t50Product){
		if( !isset($xmlItem["id"]) || !isset($xmlItem["url"]) )
			return false;

		$data = self::getData($t50Product);
		if( !$data->valid )
			return false;

		$data->shops[] = $shopId;
		$data->shops = array_unique($data->shops);
		$data->ids[$shopId] = $xmlItem["id"];
		$data->urls[$shopId] = $xmlItem["url"];

		$ids = \T50ArrayHelper::filterByKeys($data->ids, $data->shops);
		$jsonIds = json_encode($ids);

		$urls = \T50ArrayHelper::filterByKeys($data->urls, $data->shops);
		$jsonUrls = json_encode($urls);

		$result = Product::clas()::update($t50Product["ID"], [
			"UF_REMOTE_IDS" => $jsonIds,
			"UF_REMOTE_URLS" => $jsonUrls,
			"UF_SHOPS" => $data->shops,
		]);
		return $result->isSuccess();
	}

	static function getRemoteIds($arFields){
		$data = self::getData($arFields);
		if( !$data->valid )
			return false;

		return $data->ids;
	}

	static function getRemoteUrls($arFields){
		$data = self::getData($arFields);
		if( !$data->valid )
			return [];

		return $data->urls;
	}

	private static function getData($arFields){
		$data = new \StdClass;
		$data->valid = false;
		$data->shops = array();
		$data->ids = array();
		$data->urls = array();

		if( $arFields["ID"] <= 0 )
			return $data;

		$data->shops = $arFields["UF_SHOPS"];
		if( empty($data->shops) )
			return $data;

		if( !array_key_exists("UF_REMOTE_IDS", $arFields) || !array_key_exists("UF_REMOTE_URLS", $arFields) )
			return $data;

		$json = $arFields["UF_REMOTE_IDS"];
		if( !empty($json) )
			$data->ids = json_decode($json, true);

		if( $data->ids === null )
			return $data;

		$json = $arFields["UF_REMOTE_URLS"];
		if( !empty($json) )
			$data->urls = json_decode($json, true);

		if( $data->urls === null )
			$data->urls = [];

		$data->valid = true;
		return $data;
	}

	static function updateUrl(int $shopId, $url, $t50Product){
		if( empty($url) )
			return false;

		$data = self::getData($t50Product);
		if( !$data->valid )
			return false;

		if( $data->urls[$shopId] == $url )
			return true;

		$data->urls[$shopId] = $url;

		$urls = \T50ArrayHelper::filterByKeys($data->urls, $data->shops);
		$jsonUrls = json_encode($urls);

		$result = Product::clas()::update($t50Product["ID"], ["UF_REMOTE_URLS" => $jsonUrls]);
		return $result->isSuccess();
	}
}