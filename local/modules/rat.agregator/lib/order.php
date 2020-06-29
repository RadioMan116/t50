<?php

namespace rat\agregator;

use Bitrix\Main\Entity;
use ORM\Traits\PrepareType;
use Bitrix\Main\Entity\Event;

class Order extends \ORM\BaseDataManager
{
	public static function getTableName(){
		return 't50_orders';
	}

	public static function isExists(int $orderId){
		$row = self::clas()::getRowById($orderId);
		return !empty($row);
	}

	public static function getRef($class, $code = null){
		return new Entity\ReferenceField(
			$code ?? $class::getRefCode(),
			$class::clas(),
			['=this.ID' => 'ref.UF_ORDER_ID']
		);
	}

	protected static function getRulesMap(){
		return array(
			"UF_MANAGER_ID" => PrepareType::T_ANY,
			"UF_FLAGS" => PrepareType::T_ENUM,
			"UF_DATE_CREATE" => PrepareType::T_DATETIME,
			"UF_CITY" => PrepareType::T_ENUM,
			"UF_CLIENT" => PrepareType::T_ANY,
			"UF_STATUS" => PrepareType::T_ENUM,
			"UF_TEST" => PrepareType::T_BOOL,
			"UF_SOURCE" => PrepareType::T_ENUM,
			"UF_REMOTE_ORDER" => PrepareType::T_ANY,
			"UF_SHOP" => PrepareType::T_SHOP,
		);
	}

	public static function onBeforeUpdate(Event $event){
		parent::onBeforeUpdate($event);
		return self::checkRequiredFields($event, ["UF_STATUS", "UF_SOURCE", "UF_SHOP"]);
	}

	public static function onBeforeAdd(Event $event){
		parent::onBeforeAdd($event);
		return self::checkRequiredFields($event, ["UF_STATUS", "UF_SOURCE", "UF_SHOP"]);
	}

}
