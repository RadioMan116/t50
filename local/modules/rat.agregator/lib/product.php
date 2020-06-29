<?php

namespace rat\agregator;

use ORM\Traits\PrepareType;
use Bitrix\Main\Entity\Event;
use T50GlobVars;

class Product extends \ORM\BaseDataManager
{
	public static function getTableName(){
		return 't50_products';
	}

	protected static function getRulesMap(){
		return array(
			"UF_REMOTE_IDS" => PrepareType::T_STR,
			"UF_DISCONTINUED" => PrepareType::T_BOOL,
			"UF_ANALOG_ID" => PrepareType::T_ANY,
			"UF_DATA_MARKET" => PrepareType::T_STR,
			"UF_MODEL_PRINT" => PrepareType::T_STR,
			"UF_MODEL" => PrepareType::T_STR,
			"UF_BRAND" => PrepareType::T_BRAND,
			"UF_CATEGORIES" => PrepareType::T_CATEGORY,
			"UF_SHOPS" => PrepareType::T_SHOP,
			"UF_TITLE" => PrepareType::T_STR,
			"UF_CODE" => PrepareType::T_STR,
			"UF_FLAG_BUILD_IN" => PrepareType::T_BOOL,
			"UF_FLAG_NEW" => PrepareType::T_BOOL,
			"UF_FLAG_PROMO" => PrepareType::T_BOOL,
			"UF_FLAG_PROF" => PrepareType::T_BOOL,
		);
	}

	public static function onBeforeUpdate(Event $event){
		parent::onBeforeUpdate($event);
		return self::checkRequiredFields($event, ["UF_TITLE", "UF_CODE", "UF_MODEL", "UF_BRAND"]);
	}

	public static function onBeforeAdd(Event $event){
		parent::onBeforeAdd($event);
		return self::checkRequiredFields($event, ["UF_TITLE", "UF_CODE", "UF_MODEL", "UF_BRAND"]);
	}

	public static function prepareFlags($flagsId){
		static $flags;
		if( !isset($flags) )
			$flags = T50GlobVars::get("HLPROPS")[self::getTablename()]["UF_FLAGS"];

		$result = [];
		foreach($flags as $code => $item)
		    $result[$code] = in_array($item["id"], $flagsId);

		return $result;
	}

	public static function buildDefaultUrl(array $productRow){
		return self::buildUrl(
			current($productRow["UF_SHOPS"]),
			current($productRow["UF_CATEGORIES"]),
			$productRow["UF_CODE"]
		);
	}

	public static function buildUrl($shop, $category, $productCode){
		static $shops;
		static $categories;

		if( empty($productCode) )
			return;

		if( is_numeric($shop) ){
			$shops = $shops ?? T50GlobVars::get("CACHE_SHOPS");
			$shop = $shops[$shop]["CODE"];
		}
		if( empty($shop) )
			return;

		if( is_numeric($category) ){
			$categories = $categories ?? T50GlobVars::get("CACHE_CATEGORIES");
			$category = $categories[$category]["CODE"];
		}
		if( empty($category) )
			return;

		return "/catalog/${shop}/{$category}/{$productCode}.html";
	}
}