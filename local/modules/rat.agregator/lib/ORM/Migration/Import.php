<?php

namespace ORM\Migration;

use Bitrix\Main\Entity;
use Bitrix\Highloadblock\DataManager as HLDataManager;
use Bitrix\Highloadblock\HighloadBlockTable as HBT;
use Agregator\Common\ReportXml;

class Import extends Migration
{
	use Traits\ImportPreview;

	private $xml;
	private $create = array();
	private $update = array();

	function exec(){
		if( empty($this->entity) ){
			$this->errors[] = "entity not initialized";
			return false;
		}

		$path = "/upload/" . $this->entity["NAME"] . ".xml";
		$path = $_SERVER["DOCUMENT_ROOT"] . $path;
		if( !file_exists($path) ){
			$this->errors[] = "file {$path} not exists";
			return false;
		}

		$this->xml = simplexml_load_file($path);
		if( $this->isInvalidHiblock() )
			return false;

		$currentData = $this->getData(true);
		foreach($this->xml->fields->item as $field){
			$fName = $field->FIELD_NAME->__toString();
			if( isset($currentData[$fName]) ){
				$needUpdate = $this->compare($field, $currentData[$fName]);
				if( $needUpdate )
					$this->update[$fName] = $needUpdate;
			} else {
				$this->create[$fName] = $field;
			}
		}

		$this->prepareUpdate($currentData);

		return true;
	}


	function getFieldsForCreate(){
		return $this->create;
	}

	function getFieldsForUpdate(){
		return $this->update;
	}

	function needUpdate(){
		return ( !empty($this->create) ||  !empty($this->update) );
	}

	private function prepareUpdate($currentData){
		$langKeys = ["EDIT_FORM_LABEL", "LIST_COLUMN_LABEL", "LIST_FILTER_LABEL", "ERROR_MESSAGE", "HELP_MESSAGE"];

		foreach($this->update as $code => $item){
			if( \T50ArrayHelper::isOverlaps(array_keys($item["CHANGES"]), $langKeys) ){
				foreach($langKeys as $langKey){
					foreach(["ru", "en"] as $lang){
						if( empty($item["CHANGES"][$langKey][$lang]) ){
							$oldValue = $currentData[$code][$langKey][$lang];
							$this->update[$code]["CHANGES"][$langKey][$lang] = [$oldValue, $oldValue];
						}
					}
				}
			}

			if( isset($item["CHANGES"]["SETTINGS"]) ){
				foreach($currentData[$code]["SETTINGS"] as $key => $val){
					if( isset($item["CHANGES"]["SETTINGS"][$key]) )
						continue;

					$this->update[$code]["CHANGES"]["SETTINGS"][$key] = [$val, $val];
				}
			}
		}
	}

	private function compare($fieldXml, $fieldDb){
		$arResult = array("USER_FIELD_ID" => $fieldDb["ID"]);
		$changes = array();

		$unchangeableCodes = array("USER_TYPE_ID", "MULTIPLE");
		foreach($unchangeableCodes as $code){
			$difference = $this->getDifference($fieldXml, $fieldDb, $code);
			if( $difference != false ){
				$this->errors[] = "cannot change unchangeable param {$code} from {$difference[0]} to {$difference[1]}";
			}
		}

		$changeableCodes = array("SORT", "MANDATORY", "SHOW_FILTER", "SHOW_IN_LIST", "EDIT_IN_LIST", "IS_SEARCHABLE", "SETTINGS", "EDIT_FORM_LABEL", "LIST_COLUMN_LABEL", "LIST_FILTER_LABEL", "ERROR_MESSAGE", "HELP_MESSAGE");

		foreach($changeableCodes as $code){
			$difference = $this->getDifference($fieldXml, $fieldDb, $code);
			if( $difference != false )
				$changes[$code] = $difference;
		}

		if( $fieldXml->BASE_TYPE->__toString() == "enum" ){
			$diffEnum = $this->getDifferenceEnum($fieldXml, $fieldDb);
			if( $diffEnum != false )
				$arResult["ENUM"] = $diffEnum;
		}

		if( empty($changes) && empty($arResult["ENUM"]) )
			return false;

		$arResult["CHANGES"] = $changes;
		return $arResult;
	}

	private function getDifference($fieldXml, $fieldDb, $code){
		$xmlValue = json_encode($fieldXml->$code);
		$xmlValue = json_decode($xmlValue, 1);
		if( isset($xmlValue[0]) )
			$xmlValue = $xmlValue[0];

		$dbValue = $fieldDb[$code];
		if( !is_array($xmlValue) ){
			if( $dbValue == $xmlValue )
				return false;

			return [$dbValue, $xmlValue];
		}

		$arResult = array();
		foreach($xmlValue as $subCode => $value){
			if( $subCode == "IBLOCK_ID" )
				continue;

			if( empty($value) )
				$value = null;

			if( $dbValue[$subCode] == $value )
				continue;

			$arResult[$subCode] = [$dbValue[$subCode], $value];
		}

		if( empty($arResult) )
			return false;

		return $arResult;
	}

	private function getDifferenceEnum($fieldXml, $fieldDb){
		$create = array();
		$update = array();

		$dbEnums = array();
		foreach($fieldDb["enums"] as $item)
			$dbEnums[$item["XML_ID"]] = $item;

		foreach($fieldXml->enums->item as $item){
			$xmlId = $item->XML_ID->__toString();
			if( !isset($dbEnums[$xmlId]) ){
				$create[] = $item;
				continue;
			}
			$dbEnumsItem = $dbEnums[$xmlId];

			$upd = array();
			foreach(array("VALUE", "DEF", "SORT") as $code){
				$xmlValue = $item->$code->__toString();
				if( $xmlValue != $dbEnumsItem[$code] ){
					$upd[$code] = [$dbEnumsItem[$code], $xmlValue];
					$dbEnumsItem[$code] = $xmlValue;
				}
			}

			if( !empty($upd) ){
				$update[$xmlId] = array(
					"DATA" => $dbEnumsItem,
					"CHANGES" => $upd,
				);
			}
		}

		if( empty($create) && empty($update))
			return null;


		$result = new \StdClass;
		$result->create = $create;
		$result->update = $update;
		return $result;
	}

	private function isInvalidHiblock(){
		$table = $this->xml->hiblock->TABLE_NAME->__toString();
		$name = $this->xml->hiblock->NAME->__toString();
		$entity = $this->entity;
		if( $table != $entity["TABLE_NAME"] || $name != $entity["NAME"]){
			$this->errors[] = "not valid hiblock params [table={$table}, name={$name}] must be [table={$entity['TABLE_NAME']}, name={$entity['NAME']}]";
			return true;
		}

		return false;
	}
}