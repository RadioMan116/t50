<?php

namespace rat\agregator;

use ORM\Traits\PrepareType;
use T50GlobVars;
use T50Date;
use Bitrix\Main\Entity\Event;

class ProductComment extends \ORM\BaseDataManager
{
	const DELETE = "DELETE";
	const UPDATE = "UPDATE";
	const CREATE = "CREATE";

	private static $lastOperation;

	public static function getTableName(){
		return 't50_product_comments';
	}

	protected static function getRulesMap(){
		return array(
			"UF_SUPPLIER_ID" => PrepareType::T_SUPPLIER,
			"UF_SHOP_ID" => PrepareType::T_SHOP,
			"UF_COMMENT" => PrepareType::T_STR,
			"UF_DATE_RESET" => PrepareType::T_DATETIME,
			"UF_DATE_CREATE" => PrepareType::T_DATETIME,
			"UF_MANAGER_ID" => PrepareType::T_MANAGER,
			"UF_KEY" => PrepareType::T_ENUM,
			"UF_PRODUCT_ID" => PrepareType::T_ANY,
			"UF_CITY" => PrepareType::T_ENUM,
		);
	}

	public static function setCommentForShopAvail(int $productId, int $shopId, $city, $comment, $date = null){
		return self::setCommentWithBind($productId, "avail_shop", $shopId, $city, $comment, $date);
	}

	public static function setCommentForShopSale(int $productId, int $shopId, $city, $comment, $date = null){
		return self::setCommentWithBind($productId, "sale", $shopId, $city, $comment, $date);
	}

	public static function setCommentForSupplierPurchase(int $productId, int $supplierId, $comment, $date = null){
		$city = self::getCityForSupplier($supplierId);
		return self::setCommentWithBind($productId, "purchase", $supplierId, $city, $comment, $date);
	}

	public static function setCommentForSupplierAvail(int $productId, int $supplierId, $comment, $date = null){
		$city = self::getCityForSupplier($supplierId);
		return self::setCommentWithBind($productId, "avail_supplier", $supplierId, $city, $comment, $date);
	}

	private static function getCityForSupplier($supplierId){
		static $suppliers;
		if( !isset($suppliers) )
			$suppliers = T50GlobVars::get("CACHE_SUPPLIERS");

		return $suppliers[$supplierId]["PROPERTY_CITY_VALUE"];
	}

	private static function setCommentWithBind(int $productId, $key, int $bindId, $city, $comment, $date){
		self::$lastOperation = null;
		$keyId = T50GlobVars::get("HLPROPS")[self::getTableName()]["UF_KEY"][$key]["id"];
		$cityId = T50GlobVars::get("HLPROPS")[self::getTableName()]["UF_CITY"][$city]["id"];

		if( $keyId <= 0 || $productId <= 0 || $bindId <= 0 || $cityId <= 0 )
			return false;

		$bindField = ( ($key == "sale" || $key == "avail_shop") > 0 ? "UF_SHOP_ID" : "UF_SUPPLIER_ID" );
		$filter = ["UF_KEY" => $keyId, "UF_PRODUCT_ID" => $productId, $bindField => $bindId, "UF_CITY" => $cityId ];
		$current = self::clas()::getRow(compact("filter"));

		if( isset($date) ){

			if( !\T50Date::check($date) )
				return false;

			$resultCompareDates = T50Date::compareDates($date, "<=", date("d.m.Y"));

			if( !isset($resultCompareDates) )
				return false;

			if( $resultCompareDates ){
				self::$lastOperation = self::DELETE;
				if( !isset($current) )
					return true;

				return self::clas()::delete($current["ID"])->isSuccess();
			}
		}

		if( empty($comment) )
			return false;

		$data = [
			"UF_COMMENT" => $comment,
			"UF_DATE_RESET" => $date,
			"UF_DATE_CREATE" => date("d.m.Y"),
			"UF_MANAGER_ID" => $GLOBALS["USER"]->getId(),
		] + $filter;

		if( isset($current) ){
			self::$lastOperation = self::UPDATE;
			return self::clas()::update($current["ID"], $data)->isSuccess();
		}

		self::$lastOperation = self::CREATE;
		return self::clas()::add($data)->isSuccess();
	}


	public static function setDiscontinuedComment(int $productId, $comment){
		return self::setCommentWithoutBind($productId, "discontinued", $comment);
	}

	public static function setAnalogComment(int $productId, $comment){
		return self::setCommentWithoutBind($productId, "analog", $comment);
	}

	private static function setCommentWithoutBind(int $productId, $key, $comment){
		$keyId = T50GlobVars::get("HLPROPS")[self::getTableName()]["UF_KEY"][$key]["id"];
		$cityId = T50GlobVars::get("HLPROPS")[self::getTableName()]["UF_CITY"]["MSK"]["id"];
		if( $keyId <= 0 )
			return false;

		if( empty($comment) )
			return false;

		$filter = ["UF_PRODUCT_ID" => $productId, "UF_KEY" => $keyId, "UF_CITY" => $cityId];
		$current = self::clas()::getRow(compact("filter"));
		$data = [
			"UF_COMMENT" => $comment,
			"UF_DATE_CREATE" => date("d.m.Y"),
			"UF_MANAGER_ID" => $GLOBALS["USER"]->getId(),
		] + $filter;

		if( isset($current) ){
			self::$lastOperation = self::UPDATE;
			return self::clas()::update($current["ID"], $data)->isSuccess();
		}

		self::$lastOperation = self::CREATE;
		return self::clas()::add($data)->isSuccess();
	}

	public static function getByProductId(int $productsId, $city){
		$result = self::getByProductsId([$productsId], $city);
		return $result[$productsId];
	}

	static function getByProductsId(array $productsId, $city){
		if( !\T50ArrayHelper::isInt($productsId) )
			return [];

		$cityId = T50GlobVars::get("HLPROPS")[self::getTableName()]["UF_CITY"][$city]["id"];
		if( $cityId <= 0 )
			return [];

		$arResult = array();
		$res = self::clas()::getList(["filter" => ["UF_PRODUCT_ID" => $productsId, "UF_CITY" => $cityId]]);
		while( $result = $res->Fetch() ){
			$item = [];

			$productId = $result["UF_PRODUCT_ID"];
			$data = self::prepare($result);
			$key = $data["UF_KEY"]["code"];
			$bindId = 0;
			switch ($key) {
				case "avail_shop":
				case "sale":
					$group = "shops";
					$bindId = $data["UF_SHOP_ID"]["ID"];
				break;
				case "avail_supplier":
				case "purchase":
					$group = "suppliers";
					$bindId = $data["UF_SUPPLIER_ID"]["ID"];
				break;
				case "discontinued":
				case "analog":
					$group = "common";
				break;
				default:
					throw new \RuntimeException("undefined comment key '{$key}'");
			}

			$dataSet = [
				"MANAGER" => $data["UF_MANAGER_ID"]["NAME"],
				"DATE_CREATE" => \T50Date::bxdate($data["UF_DATE_CREATE"]),
				"DATE_RESET" =>  \T50Date::bxdate($data["UF_DATE_RESET"]),
				"COMMENT" =>  $data["UF_COMMENT"],
			];

			if( $bindId > 0 ){
				$arResult[$productId][$group][$bindId][$key] = $dataSet;
			} else{
				$arResult[$productId][$group][$key] = $dataSet;
			}
		}

		return $arResult;
	}

	static function getLastOperation(){
		return self::$lastOperation;
	}

	public static function onBeforeUpdate(Event $event){
		parent::onBeforeUpdate($event);
		return self::checkRequiredFields($event, ["UF_PRODUCT_ID", "UF_KEY", "UF_CITY"]);
	}

	public static function onBeforeAdd(Event $event){
		parent::onBeforeAdd($event);
		return self::checkRequiredFields($event, ["UF_PRODUCT_ID", "UF_KEY", "UF_CITY"]);
	}
}