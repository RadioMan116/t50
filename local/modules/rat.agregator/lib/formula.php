<?php

namespace rat\agregator;

use Bitrix\Main\Entity\Event;
use Bitrix\Main\Entity;
use ORM\Traits\PrepareType;
use Agregator\Product\Brand;
use Agregator\Product\ProductsRecalc;

class Formula extends \ORM\BaseDataManager
{
	public static function getTableName(){
		return 't50_formulas';
	}

	protected static function getRulesMap(){
		return array(
			"UF_MANAGER_ID" => PrepareType::T_ANY,
			"UF_DATE" => PrepareType::T_DATETIME,
			"UF_COMMENT" => PrepareType::T_STR,
			"UF_PERCENT" => PrepareType::T_ANY,
			"UF_MIN_COMMISSION" => PrepareType::T_ANY,
			"UF_MAX_COMMISSION" => PrepareType::T_ANY,
			"UF_MAX_PURCHASE" => PrepareType::T_ANY,
			"UF_MIN_PURCHASE" => PrepareType::T_ANY,
			"UF_PARENT_ID" => PrepareType::T_ANY,
			"UF_CHECK_RRC" => PrepareType::T_BOOL,
			"UF_MODE" => PrepareType::T_ENUM,
			"UF_USE_SUPPLIERS_RRC" => PrepareType::T_BOOL,
			"UF_SUPPLIERS_RRC" => PrepareType::T_SUPPLIER,
			"UF_TITLE" => PrepareType::T_STR,
		);
	}

	public static function OnBeforeDelete(Event $event){
		parent::OnBeforeDelete($event);
		$result = new Entity\EventResult;
		$params = $event->getParameters();
		$data = self::clas()::getRowById($params["id"]["ID"]);
		if( $data["UF_UNDELETABLE"] > 0 )
			$result->addError(new Entity\EntityError("blocked delete by field UF_UNDELETABLE"));

		return $result;
	}

	public static function OnAfterDelete(Event $event){
		$params = $event->getParameters();
		$id = $params["id"]["ID"];
		Brand::deleteFormula($id);
		ProductsRecalc::deleteFormula($id);
	}
}

