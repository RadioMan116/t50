<?php
namespace Agregator\Product;

use Agregator\IB\Elements;
use Agregator\IB\Element;
use Agregator\Logger;

abstract class AModel
{
	protected $id;
	protected $data = array();

	protected static $Updater;
	protected static $Logger;

	protected static $iblockCode;

	function __construct(){
		if( empty(static::$iblockCode) )
			throw new \RuntimeException("undefined iblock code");

		if( static::$Updater == null )
			static::$Updater = new Element(static::$iblockCode);
		if( static::$Logger == null )
			static::$Logger = new Logger(substr(static::$iblockCode, 0, -1));
	}

	function create($arFieldValue, &$errors){
		if( $this->id > 0 )
			$this->id = 0;

		list($props, $fields, $errors) = $this->prepareWrite($arFieldValue);
		if( !empty($errors) ){
			static::$Logger->log($errors);
			return false;
		}

		$this->id = static::$Updater->create($fields, $props, $errors);
		if( !empty($errors) )
			static::$Logger->log($errors);
		return $this->id;
	}

	function update($arFieldValue, &$errors){
		$this->id = (int) $this->id;
		if( $this->id <= 0 ){
			$errors = array("undefined " . substr(static::$iblockCode, 0, -1) . " id");
			static::$Logger->log($errors);
			return false;
		}

		list($props, $fields, $errors) = $this->prepareWrite($arFieldValue);
		if( !empty($errors) ){
			static::$Logger->log($errors);
			return false;
		}

		$result = static::$Updater->update($this->id, $fields, $props, $errors);
		if( !$result )
			static::$Logger->log($errors);
		return $result;
	}

	function getId(){
		return $this->id;
	}

	function setId(int $id){
		$this->id = $id;
		return $this;
	}

	function getData($code = ""){
		if( !empty($code) )
			return $this->data[$code];
		return $this->data;
	}

	protected function getValidFields(){
		return \T50Reflection::getConstants(get_class($this), "^F_");
	}

	protected function checkFields(array $fields){
		$notValid = array_diff($fields, $this->getValidFields());
		if( !empty($notValid) )
			throw new \InvalidArgumentException("undefined fields: " . implode(",", $notValid ) );
	}

	public static function elements(){
		return new Elements(static::$iblockCode);
    }
}