<?php

namespace rat\agregator;

use ORM\Traits\PrepareType;
use Bitrix\Main\Entity\Event;
use Bitrix\Main\Entity;

class ProductPrice extends \ORM\BaseDataManager
{
	public static function getTableName(){
		return 't50_product_prices';
	}

	protected static function getRulesMap(){
		return array(
			"UF_MANUAL_AVAIL" => PrepareType::T_BOOL,
			"UF_MANUAL_PRICE" => PrepareType::T_BOOL,
			"UF_PRICE_SALE" => PrepareType::T_ANY,
			"UF_PRICE_SALE_M" => PrepareType::T_ANY,
			"UF_AVAIL" => PrepareType::T_ANY,
			"UF_AVAIL_M" => PrepareType::T_ANY,
			"UF_PRICE_PURCHASE" => PrepareType::T_ANY,
			"UF_RRC" => PrepareType::T_ANY,
			"UF_FORMULA" => PrepareType::T_ANY,
			"UF_SHOP" => PrepareType::T_SHOP,
			"UF_CITY" => PrepareType::T_ENUM,
			"UF_PRODUCT_ID" => PrepareType::T_ANY,
			"UF_FLAG_FREE_INSTALL" => PrepareType::T_BOOL,
			"UF_FLAG_FREE_DELIVER" => PrepareType::T_BOOL,
		);
	}

	public static function onBeforeUpdate(Event $event){
		parent::onBeforeUpdate($event);
		return self::checkRequiredFields($event, ["UF_PRODUCT_ID"]);
	}

	public static function onBeforeAdd(Event $event){
		parent::onBeforeAdd($event);
		return self::checkRequiredFields($event, ["UF_PRODUCT_ID"]);
	}

	public static function getRuntime(){
		$prepareSelector = function ($modeColumn, $manualColumn, $autoColumn){
			$params = compact("modeColumn", "manualColumn", "autoColumn");
			$params = array_map(function ($columnName){
				return "product_pr." . $columnName;
			}, $params);

			return "IF({$params['modeColumn']} = 1, {$params['manualColumn']}, {$params['autoColumn']})";
		};

		$priceSelector = $prepareSelector("UF_MANUAL_PRICE", "UF_PRICE_SALE_M", "UF_PRICE_SALE");
		$commissionSelector = "({$priceSelector} - product_pr.UF_PRICE_PURCHASE)";
		$availSelector = $prepareSelector("UF_MANUAL_AVAIL", "UF_AVAIL_M", "UF_AVAIL");

		return [
			new Entity\ReferenceField("PR", self::clas(), ['=this.ID' => 'ref.UF_PRODUCT_ID']),
			new Entity\ExpressionField("SALE", $priceSelector),
			new Entity\ExpressionField("COMMISSION", $commissionSelector),

			new Entity\ExpressionField("AVAIL", $availSelector),

			new Entity\ExpressionField("SALE_FROM", "MIN({$priceSelector})"),
			new Entity\ExpressionField("SALE_TO", "MAX({$priceSelector})"),
			new Entity\ExpressionField("COMMISSION_FROM", "MIN{$commissionSelector}"),
			new Entity\ExpressionField("COMMISSION_TO",  "MAX{$commissionSelector}"),
			new Entity\ExpressionField("PURCHASE_FROM", "MIN(product_pr.UF_PRICE_PURCHASE)"),
			new Entity\ExpressionField("PURCHASE_TO", "MAX(product_pr.UF_PRICE_PURCHASE)"),
		];
	}

	public static function getUnidsByFromulaId(int $formulaId){
		if( $formulaId <= 0 )
			return [];

		$res = self::clas()::getList(["filter" => ["UF_FORMULA" => $formulaId], "select" => ["UF_PRODUCT_ID"]]);
		$unids = [];
		while( $result = $res->Fetch() )
			$unids[] = $result["UF_PRODUCT_ID"];
		$unids = array_unique($unids);

		return $unids;
	}

}