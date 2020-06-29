<?php

namespace ORM;

use Bitrix\Main\Entity;
use Bitrix\Highloadblock\DataManager as HLDataManager;
use Bitrix\Highloadblock\HighloadBlockTable as HBT;
use Bitrix\Main\Entity\Event;
use Bitrix\Main\Entity\EventResult;
use T50GlobVars;


abstract class BaseDataManager extends HLDataManager
{
	use Traits\Prepare;

	static function clas(){
		static $container = array();
		$table = static::getTableName();
		if( isset($container[$table]) )
			return $container[$table];

		$hlblock = \T50GlobVars::get("HL_TABLES")[$table];
		if( empty($hlblock) )
			throw new \RuntimeException("System: undefined table '{$table}'");

		$entity = HBT::compileEntity($hlblock);
		$container[$table] = $entity->getDataClass();
		return $container[$table];
	}

	static function q($select = null){
		$query = new Entity\Query(static::clas()::getEntity());
		if( is_bool($select) && $select )
			$query->setSelect(array("*"));
		if( is_array($select) )
			$query->setSelect($select);
		return $query;
	}

	static function getListIndexed(array $params = array(), $index = "ID"){
		$arResult = array();
		$res = static::clas()::getList($params);
		while( $result = $res->Fetch() )
			$arResult[$result[$index]] = $result;

		return $arResult;
	}

	protected static function checkRequiredFields(Entity\Event $event, array $fieldNames){
		$result = new Entity\EventResult;
		$params = $event->getParameters();

		$id = ( isset($params["id"]) ? $params["id"]["ID"] : 0 );
		$fields = $params["fields"];

		if( $id > 0 ){
			foreach($fieldNames as $fieldName){
			    if( !isset($fields[$fieldName]) )
					$fields[$fieldName] = "filled";
			}
		}

		foreach($fieldNames as $fieldName){
			if( empty($fields[$fieldName]) )
				$result->addError(new Entity\EntityError("Не заполнено поле {$fieldName}"));
		}

		if( !empty($result->getErrors()) )
			return $result;
	}

	static function getRefCode(){}

	static function updateOrCreate($whereFields, $allFields){
		$current = self::clas()::getRow(["filter" => $whereFields]);
		if( isset($current) ){
			$result = self::clas()::update($current["ID"], $allFields);
		} else {
			$result = self::clas()::add($allFields);
		}

		if( $result->isSuccess() )
			return $result->getId();

		return false;
	}

	static function getEnum($code, $modeIdCode = true, $useVal = false){
		$data = T50GlobVars::get("HLPROPS")[static::getTablename()][$code];
		if( $modeIdCode ){
			$tmp = [];
			foreach($data as $code => $item){
				$value = ( $useVal ? $item["val"] : $code );
			    $tmp[$item["id"]] = $value;
			}

			$data = $tmp;
		}
		return $data;
	}

	static function getEnumForJson($code){
		$data = self::getEnum($code, false);
		$data = array_values($data);
		return array_map(function ($item){
			return ["val" => $item["id"], "title" => $item["val"]];
		}, $data);
	}

	static function getFlags($ids){
		static $flags;
		if( !is_array($ids) )
			$ids = [];

		$flags = $flags ?? self::getEnum("UF_FLAGS");
		$result = array_fill_keys($flags, false);
		foreach($ids as $id)
		    $result[$flags[$id]] = true;

		return $result;
	}

	static function getClassByTable($table){
		$hlblock = \T50GlobVars::get("HL_TABLES")[$table];
		if( !isset($hlblock) )
			return false;

		$class = "rat\\agregator\\" . $hlblock["NAME"];
		return $class;
	}

	static function prepareValueForHistory($code, $value){
		return $value;
	}
}
