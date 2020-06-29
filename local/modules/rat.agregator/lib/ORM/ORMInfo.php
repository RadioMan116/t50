<?php

namespace ORM;

use Bitrix\Main\Entity;
use Bitrix\Main\Application;
use Bitrix\Highloadblock\DataManager as HLDataManager;
use Bitrix\Highloadblock\HighloadBlockTable as HBT;


class ORMInfo
{
	static function getHLTables(){
		$list = HBT::getList()->fetchAll();
		$arResult = array();
		foreach($list as $item)
			$arResult[$item['TABLE_NAME']] = $item;

		return $arResult;
	}

	static function getHLEnum(){
		$arResult = array();
		$HLTablesByEntity = self::getHLTablesByEntity();
		$res = \CUserTypeEntity::GetList(array(), array("USER_TYPE_ID" => "enumeration"));
		while( $result = $res->Fetch() ){
			if( $result["ENTITY_ID"] == "USER" )
				$code = "USER";
			else
				$code = $HLTablesByEntity[$result["ENTITY_ID"]];

			if( !isset($arResult[$code]) )
				$arResult[$code] = array();

			$arResult[$code][$result["FIELD_NAME"]] = $result["ID"];
		}

		$allEnums = array();
		$res = \CUserFieldEnum::GetList(["sort" => "asc"]);
		while( $result = $res->Fetch() ){
			if( !isset($allEnums[$result["USER_FIELD_ID"]]) )
				$allEnums[$result["USER_FIELD_ID"]] = array();

			$allEnums[$result["USER_FIELD_ID"]][$result["XML_ID"]] = array(
				"id" => $result["ID"],
				"val" => $result["VALUE"],
			);
		}

		foreach($arResult as $code => $items){
			$newItems = array();
			foreach($items as $propCode => $id){
				if( !isset($allEnums[$id]) )
					continue;

				$newItems[$propCode] = $allEnums[$id];
			}
			$arResult[$code] = $newItems;
		}

		return $arResult;
	}

	static function getHlFlagCodes(){
		$res = \CUserTypeEntity::GetList([], ["USER_TYPE_ID" => "boolean"]);
		$arResult = [];
		while( $result = $res->Fetch() )
			$arResult[$result["FIELD_NAME"]] = true;

		return $arResult;
	}

	static function getHLTablesByEntity(){
		$arResult = array();
		foreach(\T50GlobVars::get("HL_TABLES") as $code => $item){
			$entity = "HLBLOCK_{$item['ID']}";
			$arResult[$entity] = $code;
		}
		return $arResult;
	}

	static function sqlTracker($command){
		static $connection, $tracker;
		if( $connection == null ){
			$connection = Application::getConnection();
			$tracker = $connection->startTracker();
		}

		if( $command == "start" ){
			$tracker = $connection->startTracker(true);
			return;
		}

		if( $command != "show" || $tracker == null )
			return;

		$connection->stopTracker();
		\T50Debug::dumpTracker($tracker);
	}

	static function getFieldNames(){
		$sql = "
			SELECT
				b_hlblock_entity.TABLE_NAME as 'TABLE',
				b_user_field.FIELD_NAME as CODE,
				b_user_field_lang.LIST_FILTER_LABEL as NAME
			FROM
					b_user_field_lang
				LEFT JOIN
					b_user_field
						ON b_user_field_lang.USER_FIELD_ID = b_user_field.ID
				LEFT JOIN
					b_hlblock_entity
						ON concat('HLBLOCK_', b_hlblock_entity.ID) = b_user_field.ENTITY_ID
			WHERE
					b_user_field_lang.LANGUAGE_ID = 'ru'
				AND
					b_user_field_lang.LIST_FILTER_LABEL != ''
				AND
					b_hlblock_entity.TABLE_NAME != ''
		";

		$arResult = [];
		$res = Application::getConnection()->query($sql);
		while( $item = $res->fetch() ){
			if( !isset($arResult[$item["TABLE"]]) )
				$arResult[$item["TABLE"]] = [];

			$arResult[$item["TABLE"]][$item["CODE"]] = $item["NAME"];
		}

		return $arResult;
	}
}