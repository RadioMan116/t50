<?php
namespace Agregator\Product;

class Category extends AModel
{
	const F_NAME = "NAME";

	protected static $iblockCode = "categories";
	protected static $Updater;
	protected static $Logger;

	function __construct(array $arFields = array()){
		parent::__construct();
		if( !empty($arFields) )
			$this->prepareRead($arFields);
	}

	protected function prepareWrite($arFieldValue){
		$this->checkFields(array_keys($arFieldValue));

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
			throw new \InvalidArgumentException("category prepareRead: undefined category ID");

		$this->id = $fetchResult["ID"];

		foreach($this->getValidFields() as $field){
			if( $field == self::F_NAME )
				$value = @$fetchResult["NAME"];
			else
				$value = @$fetchResult["PROPERTY_{$field}_VALUE"];
			$this->data[$field] = $value;
		}
	}
}