<?php

namespace rat\agregator;

use Bitrix\Main\Entity;
use ORM\BaseDataManager;
use ORM\DBQuery;
use Agregator\Order\Utils\OrderPropertyBuilder;

class OrderProperty extends BaseDataManager
{
	public static function getTableName(){
		return 't50_orderprops';
	}

	static function set(int $orderId, $code, $val){
		$props = \T50GlobVars::get("HLPROPS")[self::getTableName()]["UF_PROPERTY"];
		if( empty($props) )
			return false;

		if( !isset($props[$code]) )
			return false;

		$orderId = (int) $orderId;

		$Props = self::clas();
		$data = array(
			"UF_ORDER_ID" => $orderId,
			"UF_PROPERTY" => $props[$code]["id"],
			"UF_STRING" => htmlspecialchars($val),
			"UF_NUMBER" => (int) $val,
		);

		$existProp = self::getById($orderId, $props[$code]["id"]);
		if( $existProp["ID"] > 0 )
			return $Props::update($existProp["ID"], $data)->isSuccess();

		$result = $Props::add($data);
		if( $result->isSuccess() )
			return $result->getId();

		return false;
	}

	static function getById(int $orderId, int $propertyId){
		$orderId = (int) $orderId;
		$propertyId = (int) $propertyId;
		$filter = ["UF_ORDER_ID" => $orderId, "UF_PROPERTY" => $propertyId];
		return self::clas()::getRow(compact("filter"));
	}

	static function get(int $orderId, $code){
		$props = \T50GlobVars::get("HLPROPS")[self::getTableName()]["UF_PROPERTY"];
		$propertyId = $props[$code]["id"];
		return self::getById($orderId, (int) $props[$code]["id"]);
	}

	static function getStr(int $orderId, $code){
		return self::get($orderId, $code)["UF_STRING"];
	}

	static function getInt(int $orderId, $code){
		return self::get($orderId, $code)["UF_NUMBER"];
	}

	static function delete(int $orderId, $code){
		$current = self::get($orderId, $code);
		if( !isset($current) )
			return false;

		return self::clas()::delete($current["ID"])->isSuccess();
	}

	public static function onBeforeAdd(Entity\Event $event){
		parent::onBeforeAdd($event);
		$Props = self::clas();
		$result = new Entity\EventResult;
		$data = $event->getParameter("fields");

		if( !Order::isExists($data["UF_ORDER_ID"]) ){
			$result->addError(new Entity\EntityError("Заказ " . $data["UF_ORDER_ID"] . " не существует." ));
			return $result;
		}

		$existProp = self::getById((int) $data["UF_ORDER_ID"], (int) $data["UF_PROPERTY"]);
		if( $existProp["ID"] > 0 ){
			$messages = ['Свойство существует.'];
			$updResult = $Props::update($existProp["ID"], $data);
			if( $updResult->isSuccess() ){
				$messages[] = 'Обновлено свойство с ID ' . $existProp['ID'];
				$link = "http://{$_SERVER['HTTP_HOST']}/bitrix/admin/highloadblock_row_edit.php?ENTITY_ID={$_REQUEST['ENTITY_ID']}&ID={$existProp['ID']}&lang=ru";
				$messages[] = $link;
			} else {
				$messages[] = 'Ошибка одновления свойства с ID ' . $existProp["ID"] . ': ';
				$messages[] = implode("\n", $result->getErrorMessages());
			}
			$result->addError(new Entity\EntityError(implode("\n", $messages)));
			return $result;
		}
	}

	static function buildPropertiesData(array $filter = array(), array $fields = array()){
		$builder = new OrderPropertyBuilder();
		return $builder->setFields($fields)->setFilter($filter)->build();
	}
}