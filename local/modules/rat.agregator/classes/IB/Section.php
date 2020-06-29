<?php
namespace Agregator\IB;

use \Bitrix\Main\Loader;
use CIBlockSection;

class Section
{
	private static function getIblockId($code){
		static $arCodeId = [];
		if( !isset($arCodeId[$code]) )
			$arCodeId[$code] = (new IBlocks)->getIblockId($code);

		return $arCodeId[$code];
	}

	static function getList($iblockCode, $detail = false){
		$iblockId = self::getIblockId($iblockCode);
		$select = [];
		if( $detail )
			$select = ["ID", "NAME", "UF_*"];

		$res = CIBlockSection::getList(
			["NAME" => "ASC"],
			["IBLOCK_ID" => $iblockId, "ACTIVE" => "Y"],
			false, $select
		);
		$arResult = [];
		while( $result = $res->Fetch() ){
			if( $detail ){
				$arResult[$result["ID"]] = $result;
			} else {
				$arResult[$result["ID"]] = $result["NAME"];
			}
		}

		return $arResult;
	}

	static function add($iblockCode, array $fields, &$errorVar){
		$iblockId = self::getIblockId($iblockCode);
		if( $iblockId <= 0 )
			return false;

		$fields["IBLOCK_ID"] = $iblockId;
		$bs = new CIBlockSection;
		$id = $bs->Add($fields);
		$errorVar = $bs->LAST_ERROR;
		return $id;
	}

	static function update($iblockCode, array $fields, &$errorVar){
		$iblockId = self::getIblockId($iblockCode);
		if( $iblockId <= 0 || $fields["ID"] <= 0 )
			return false;

		$fields["IBLOCK_ID"] = $iblockId;
		$bs = new CIBlockSection;
		$result = $bs->update($fields["ID"], $fields);
		$errorVar = $bs->LAST_ERROR;
		return $result;
	}
}
