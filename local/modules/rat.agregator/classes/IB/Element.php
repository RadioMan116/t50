<?php
namespace Agregator\IB;

use \Bitrix\Main\Loader;

class Element
{
	private $iblockId;

	function __construct($iblockCode = ""){
		Loader::IncludeModule("iblock");
		if( !empty($iblockCode) )
			$this->iblockId = $this->getIblockId($iblockCode);
	}

	function create($fields = array(), $props = array(), &$errorVar){
		$errorVar = null;

		if( \T50ArrayHelper::isEmpty($fields) ){
			$errorVar = "empty fields array";
			return false;
		}

		if( empty($fields["IBLOCK_ID"]) )
			$fields["IBLOCK_ID"] = $this->iblockId;

		if( empty($fields["NAME"]) || $fields["IBLOCK_ID"] <= 0 ){
			$errorVar = "not set name or iblock_id";
			return false;
		}

		if( !\T50ArrayHelper::isEmpty($props) )
			$fields["PROPERTY_VALUES"] = $props;

		$el = new \CIBlockElement;

		if( empty($fields["MODIFIED_BY"]) )
			$fields["MODIFIED_BY"] = $GLOBALS["USER"]->GetID();

		if( empty($fields["ACTIVE"]) )
			$fields["ACTIVE"] = "Y";

		if( $productId = $el->Add($fields) )
			return $productId;

		$errorVar = $el->LAST_ERROR;
		return false;
	}

	function update($id, $fields = array(), $props = array(), &$errorVar){
		$id = (int) $id;
		if( $id <= 0 ){
			$errorVar = "invalid element id";
			return false;
		}

		$isEmptyFields = \T50ArrayHelper::isEmpty($fields);
		$isEmptyProps = \T50ArrayHelper::isEmpty($props);
		if( $isEmptyFields && $isEmptyProps ){
			$errorVar = "empty fields and props";
			return false;
		}

		if( empty($fields["IBLOCK_ID"]) )
			$fields["IBLOCK_ID"] = $this->iblockId;

		if( empty($fields["MODIFIED_BY"]) )
			$fields["MODIFIED_BY"] = $GLOBALS["USER"]->GetID();

		$el = new \CIBlockElement;


		if( !empty($props) )
			\CIBlockElement::SetPropertyValuesEx($id, $fields["IBLOCK_ID"], $props);

		if( empty($fields) )
			return true;

		if( $el->Update($id, $fields) )
			return true;

		$errorVar = $el->LAST_ERROR;
		return false;

	}

	function __get($prop){
		if( $prop == "iblockId" )
			return $this->iblockId;
	}

	private function getIblockId($code){
		static $objIBlocks;
		if( $objIBlocks == null )
			$objIBlocks = new IBlocks;

		return $objIBlocks->getIblockId($code);
	}

}
