<?php

namespace ORM\Migration;

use Bitrix\Main\Entity;
use Bitrix\Highloadblock\DataManager as HLDataManager;
use Bitrix\Highloadblock\HighloadBlockTable as HBT;


class Updater
{
	use Traits\Errors;

	private $importObj;

	function __construct(Import $importObj){
		$this->importObj = $importObj;
	}

	function exec(){
		$this->create();
		$this->update();
	}

	private function update(){
		$fields = $this->importObj->getFieldsForUpdate();
		foreach($fields as $field){
			$this->updateField($field);
			$this->updateEnum($field);
		}

	}

	private function updateEnum($data){
		if( !isset($data["ENUM"]) )
			return true;

		$id = (int) $data["USER_FIELD_ID"];
		$createEnums = $this->prepareXmlFieldData($data["ENUM"]->create);
		$this->setEnums($id, $createEnums);

		$updateEnums = array();
		foreach($data["ENUM"]->update as $item){
			$item = $item["DATA"];
			$updateEnums[$item["ID"]] = $item;
		}

		$this->setEnums($id, $updateEnums, false);
	}

	private function updateField($data){
		$id = (int) $data["USER_FIELD_ID"];
		if( $id <= 0 ){
			$this->errors[] = "undefined USER_FIELD_ID";
			return false;
		}

		$updFields = array();
		foreach($data["CHANGES"] as $code => $item){
			if( is_string(key($item)) ){
				$updFields[$code] = array();
				foreach($item as $subCode => $subItem){
					$updFields[$code][$subCode] = $subItem[1];
				}
			} else {
				$updFields[$code] = $item[1];
			}
		}
		//echo "id = "; var_dump($id);
		//echo "updFields = "; print_r($updFields);die();
		$userField  = new \CUserTypeEntity;
		$success = $userField->update($id, $updFields);
		if( !$success && $e = $GLOBALS["APPLICATION"]->GetException()  ){
			foreach($e->messages as $item)
				$this->errors[] = $item["id"] . ": " . $item["text"];
		}

		return $success;
	}

	private function create(){
		$fields = $this->importObj->getFieldsForCreate();
		foreach($fields as $field){
			$field = $this->prepareXmlFieldData($field);
			$id = $this->createField($field);
			if( $id && $field["BASE_TYPE"] == "enum" ){
				$this->setEnums($id, $field["enums"]["item"]);
			}
		}
	}

	private function createField($data){
		unset($data["ID"], $data["enums"], $data["enum_values"]);
		$data["ENTITY_ID"] = 'HLBLOCK_' . $this->importObj->getEntity()["ID"];

		$userField  = new \CUserTypeEntity;
		$id = $userField->add($data);
		if( $id == false && $e = $GLOBALS["APPLICATION"]->GetException() ){
			foreach($e->messages as $item)
				$this->errors[] = $item["id"] . ": " . $item["text"];

			return false;
		}

		return $id;
	}

	private function setEnums($fieldId, $data, $modifyData = true){
		if( empty($data) )
			return;

		$saveData = $data;
		if( $modifyData ){
			$saveData = array();
			foreach($data as $i => $item){
				unset($item["ID"], $item["USER_FIELD_ID"]);
				$saveData["n{$i}"] = $item;
			}
		}

		$obEnum = new \CUserFieldEnum();
		$result = $obEnum->SetEnumValues($fieldId, $saveData);
		return $result;
	}

	private function prepareXmlFieldData($data){
		$data = json_decode(json_encode($data), 1);
		return array_map([$this, "emptyArrayToNull"], $data);
	}

	private function emptyArrayToNull($item){
		if( is_array($item) ){

			if( empty($item) )
				return null;

			return array_map([$this, "emptyArrayToNull"], $item);
		}

		return $item;
	}

}