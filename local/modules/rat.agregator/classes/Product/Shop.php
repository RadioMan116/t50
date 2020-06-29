<?php
namespace Agregator\Product;

use Bitrix\Highloadblock\HighloadBlockTable as HBT;

class Shop extends AModel
{
	const F_NAME = "NAME";
	const F_HOST = "HTTP_HOST";
	const F_CATEGORIES = "CATEGORIES";
	const F_IS_SYNC = "SYNCHRONIZED";
	const F_BRANDS = "BRANDS";
	const F_FORMULA = "FORMULA";

	protected static $iblockCode = "shops";
	protected static $Updater;
	protected static $Logger;

	function __construct(array $arFields = array()){
		parent::__construct();
		if( !empty($arFields) )
			$this->prepareRead($arFields);
	}

	protected function prepareWrite($arFieldValue){
		$this->checkFields(array_keys($arFieldValue));
		$propsEnumValue = \T50GlobVars::get("LPROPS_SHOPS");

		$props = $fields = $errors = array();

		foreach($arFieldValue as $field => $value){
			$valid = true;
			switch($field){
				case self::F_NAME:
					$value = htmlspecialchars($value);
					if( empty($value) )
						$valid = false;
					$fields["NAME"] = $value;
				break;
				case self::F_HOST:
					$value = htmlspecialchars($value);
					$parseUrl = parse_url($value);
					if( count($parseUrl) != 2
						|| empty($parseUrl["scheme"])
						|| empty($parseUrl["host"])
					){
						$valid = false;
					}

					$props[$field] = $value;
				break;
				case self::F_BRANDS:
				case self::F_CATEGORIES:
					if( \T50ArrayHelper::isEmpty($value) )
						$value = array();

					if( !\T50ArrayHelper::isInt($value) )
						$valid = false;

					$props[$field] = $value;
				break;
				case self::F_IS_SYNC:
					if( is_bool($value) && $value)
						$value = "Y";

					if( empty($value) )
						$value = false;
					else
						$value = $propsEnumValue[$field]["VALUES"][$value];

					$props[$field] = $value;
				break;
			}

			if( !$valid )
				$errors[] = "Invalid value for {$field} [" . var_export($value, true) . "]";
		}

		if( $this->id <= 0 && empty($fields["CODE"]) ){

			$fields["CODE"] = \CUtil::translit($fields["NAME"]);
		}

		return array($props, $fields, $errors);
	}

	protected function prepareRead($fetchResult){
		if( $fetchResult["ID"] <= 0 )
			throw new \InvalidArgumentException("shop prepareRead: undefined shop ID");

		$this->id = $fetchResult["ID"];

		foreach($this->getValidFields() as $field){
			if( $field == self::F_NAME ){
				$value = @$fetchResult["NAME"];
			} else {
				$value = @$fetchResult["PROPERTY_{$field}_VALUE"];
				if( $field == self::F_IS_SYNC )
					$value = !empty($value);
			}
			$this->data[$field] = $value;
		}
	}

	function setIsSync($boolVal){
		if( $this->id <= 0 || self::$Updater->iblockId <= 0 ){
			self::$Logger->log("setIsSync fail (invalid id or iblock_id)");
			return false;
		}

		$propsEnumValue = \T50GlobVars::get("LPROPS_SHOPS");
		$val = false;
		if( $boolVal )
			$val = $propsEnumValue[self::F_IS_SYNC]["VALUES"]["Y"];

		\CIBlockElement::SetPropertyValuesEx($this->id, self::$Updater->iblockId, array(
			self::F_IS_SYNC	=> $val)
		);
		return true;
	}

	function getCategories($index = false){
		if( empty($this->data["CATEGORIES"]) )
			return array();

		$sections = \T50GlobVars::get("PROD_SECTIONS");
		$arResult = array();
		foreach($this->data["CATEGORIES"] as $catId){
			$section = $sections[$catId];
			if( $index ){
				$arResult[$section[$index]] = $section;
			} else {
				$arResult[] = $section;
			}
		}

		return $arResult;
	}

	static function getShopCities($shopId = 0){
		static $shopCities;
		if( !isset($shopCities) ){
			$shopCities = array();
			$res = self::elements()->props("CITIES")->get();
			while( $result = $res->Fetch() )
				$shopCities[$result["ID"]] = array_values($result["PROPERTY_CITIES_VALUE"]);
		}

		if( $shopId > 0 )
			return $shopCities[$shopId];

		return $shopCities;
	}

	static function getPhonesCode(int $phoneId){
		$data = self::elements()->props("CITIES", "PHONE_CODE_MSK", "PHONE_CODE_SPB")
			->getOneFetchById($phoneId);

		if( empty($data) )
			return [];

		$result = [];
		foreach($data["PROPERTY_CITIES_VALUE"] as $cityCode){
		    $phoneCode = trim($data["PROPERTY_PHONE_CODE_{$cityCode}_VALUE"]);
		    if( !empty($phoneCode) )
		    	$result[$cityCode] = $phoneCode;
		}
		return $result;
	}

	// static function getFormulas(){
	// 	$res = HBT::getList([ 'filter' => ['NAME' => 'Formulas'] ]);
	// 	if( !($hlblock = $res->fetch()) )
	// 		return array();

	// 	$entity = HBT::compileEntity($hlblock);
	// 	$entityClass = $entity->getDataClass();

	// 	return $entityClass::getList()->fetchAll();
	// }
}