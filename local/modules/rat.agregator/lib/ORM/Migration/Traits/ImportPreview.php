<?php

namespace ORM\Migration\Traits;

trait ImportPreview
{
	function getPreview(){
		$arResult = array();

		$arResult[] = array(
			"title" => "Will be created",
			"items" => $this->getPreviewForCreate(),
		);

		$arResult[] = array(
			"title" => "Will be updated",
			"items" => $this->getPreviewForUpdate(),
		);

		return $arResult;
	}

	private function getPreviewForCreate(){
		$arResult = array();
		foreach($this->create as $code => $item)
			$arResult[] = "{$code} | " . $item->USER_TYPE_ID->__toString();

		return $arResult;
	}

	private function getPreviewForUpdate(){
		$arResult = array();
		foreach($this->update as $code => $fields){
			if( isset($fields["ENUM"]) )
				$arResult[] = $this->getPreviewForEnumUpdate($code, $fields["ENUM"]);

			foreach($fields["CHANGES"] as $field => $item){
				if( is_string(key($item))  ){
					foreach($item as $subCode => $subItem)
						$arResult[] = "{$code} | {$field} | {$subCode}: {$subItem[0]} > {$subItem[1]}";
				} else {
					$arResult[] = "{$code} | {$field}: {$item[0]} > {$item[1]}";
				}
			}
		}

		return $arResult;
	}

	private function getPreviewForEnumUpdate($fieldCode, $enumData){
		$create = array();
		$update = array();

		foreach($enumData->create as $item){
			$xmlId = $item->XML_ID->__toString();
			$value = $item->VALUE->__toString();
			$create[] = "{$xmlId} | {$value}";
		}

		foreach($enumData->update as $code => $fields){
			foreach($fields["CHANGES"] as $field => $val){
				$update[] = "{$code} {$field}: {$val[0]} > {$val[1]}";
			}
		}

		$arResult = array();

		if( !empty($create) ){
			$arResult[] = array(
				"title" => "[{$fieldCode}] new enums",
				"items" => $create,
			);
		}

		if( !empty($update) ){
			$arResult[] = array(
				"title" => "[{$fieldCode}] update enums",
				"items" => $update,
			);
		}

		return $arResult;
	}

}