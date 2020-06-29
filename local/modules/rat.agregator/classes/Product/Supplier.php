<?php
namespace Agregator\Product;
use T50Date;

class Supplier extends AModel
{
	const F_NAME = "NAME";
	const F_MARKET_ID = "MARKET_ID";
	const F_CITY = "CITY";
	const F_CODE = "CODE";
	const F_MAIN_STORAGE = "MAIN_STORAGE";
	const F_UPD_DATE = "DATE_ACTIVE_FROM";


	protected static $iblockCode = "suppliers";
	protected static $Updater;
	protected static $Logger;

	function __construct(array $arFields = array()){
		parent::__construct();
		if( !empty($arFields) )
			$this->prepareRead($arFields);
	}

	protected function prepareWrite($arFieldValue){
		$this->checkFields(array_keys($arFieldValue));
		$propsEnumValue = \T50GlobVars::get("LPROPS_SUPPLIERS");

		$props = $fields = $errors = array();

		foreach($arFieldValue as $field => $value){
			$valid = true;
			switch($field){
				case self::F_NAME:
					if( empty($value) )
						$valid = false;
					$fields["NAME"] = $value;
				break;
				case self::F_CODE:
					$value = htmlspecialchars($value);
					if( empty($value) )
						$valid = false;
					$fields["CODE"] = $value;
				break;
				case self::F_MARKET_ID:
					$value = (int) $value;
					if( $value <= 0 )
						$valid = false;

					$props[$field] = $value;
				break;
				case self::F_CITY:
					$value = $propsEnumValue[$field]["VALUES"][$value];
					if( !isset($value) )
						$valid = false;

					$props[$field] = $value;
				break;
				case self::F_UPD_DATE:
					if( !T50Date::check($value, "d.m.Y H:i") )
						$valid = false;

					$fields[$field] = $value;
				break;
				case self::F_MAIN_STORAGE:
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
			throw new \InvalidArgumentException("supplier prepareRead: undefined supplier ID");

		$this->id = $fetchResult["ID"];

		foreach($this->getValidFields() as $field){
			if( $field == self::F_NAME || $field == self::F_CODE ){
				$value = @$fetchResult[$field];
			} else {
				$value = @$fetchResult["PROPERTY_{$field}_VALUE"];
			}
			$this->data[$field] = $value;
		}
	}

}