<?php
namespace Agregator\IB;

use \Bitrix\Main\Loader;
use CIBlockSection;

class IBlocks extends \Agregator\Cache
{
	public function getIblockId($code){
		$key = __METHOD__ . $code;
		if( ($iblockId = $this->getBxCache($key)) != null )
			return $iblockId;

		$code = htmlspecialchars($code);
		if( empty($code) )
			return 0;

		$res = \CIBlock::getList(array(), array("=CODE" => $code));
		$iblockId = false;
		$result = $res->Fetch();
		if( isset($result["ID"]) && $result["CODE"] == $code )
			$iblockId = $result["ID"];

		return $this->saveBxCache($key, $iblockId);
	}

	public function getPropsTypeList($mixCodeId){
		Loader::IncludeModule("iblock");
		if( ($iblockId = $this->detectIblockId($mixCodeId)) <= 0 )
			return false;

		$key = __METHOD__ . $iblockId;
		if( ($arResult = $this->getBxCache($key)) != null )
			return $arResult;

		$arResult = array();
		$res = \CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $iblockId, "PROPERTY_TYPE" => "L"));
		while( $result = $res->GetNext() )
			$arResult[$result["CODE"]] = array(
				"ID" => $result["ID"],
				"VALUES" => array(),
				"CODE_VALUES" =>array(),
			);

		$res = \CIBlockPropertyEnum::GetList(array("SORT"=>"ASC"), array("IBLOCK_ID" => $iblockId, "CODE" => array_keys($arResult) ));
		while( $result = $res->GetNext() ){
			$arResult[$result["PROPERTY_CODE"]]["VALUES"][$result["VALUE"]] = $result["ID"];
			$extId = $result["EXTERNAL_ID"];
			if( strlen($extId) < 25 )
				$arResult[$result["PROPERTY_CODE"]]["CODE_VALUES"][$extId] = $result["ID"];
		}

		return $this->saveBxCache($key, $arResult);
	}

	private function detectIblockId($mixVar){
		if( is_numeric($mixVar) ){
			$iblockId = (int) $mixVar;
		} else {
			$iblockId = (int) $this->getIblockId($mixVar);
		}
		return $iblockId;
	}
}
