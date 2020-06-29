<?php

namespace Agregator\Components;

use Bitrix\Main\HttpRequest;

class InputManager
{
	private $data = array();
	private $requests;
	private $mapPrepare = array();

	private static $systemRules = ["null", "pass"];

	function setMapPrepare(array $mapPrepare){
		$this->mapPrepare = $this->parseMapPrepare($mapPrepare);
		return $this;
	}

	function setData(array $requests){
		$this->requests = $requests;
		return $this;
	}

	function getData(){
		$this->prepareData();
		return $this->data;
	}

	function singlePrepare($value, $method){
		$data = $this->setData(["field" => $value])->setMapPrepare(["field" => $method])->getData();
		return $data["field"];
	}

	private function prepareData(){
		$requests = $this->getRequest();

		foreach($this->mapPrepare as $field => $methods){
			$isArray = false;
			if( substr($field, -1) == "*" ){
				$isArray = true;
				$field = substr($field, 0, -1);
			}

			$value = @$requests[$field];
			if( !isset($value) && $isArray )
				$value = [];

			if( ( !isset($value) || $value === "" ) && in_array("null", $methods) ){
				$value = null;
				$methods = [];
			}
			foreach($methods as $method){
				if( in_array($method, self::$systemRules) )
					continue;

				if( $isArray ){
					$value = array_map($method, $value);
				} else {
					$value = call_user_func_array($method, [$value]);
				}
			}

			$this->data[$field] = $value;
		}

		if( !empty($_FILES) ){
			foreach($_FILES as $field => $file)
				$this->data[$field] = $file;
		}
	}

	private function getRequest(){
		if( isset($this->requests) )
			return $this->requests;

		return array_merge($_GET, $_POST);
	}

	private function parseMapPrepare($mapPrepare){
		$result = array();
		foreach($mapPrepare as $fields => $functions){
			$fields = $this->convertStrToArray($fields);
			$functions = $this->prepareFunctions($this->convertStrToArray($functions));
			foreach($fields as $field)
				$result[$field] = $functions;
		}
		return  $result;
	}

	private function prepareFunctions($functions){
		static $checkedFunctions = [];
		foreach($functions as $k => $function){
			if( in_array($function, self::$systemRules) )
				continue;

			if( in_array($function, $checkedFunctions) )
				continue;

			if( !function_exists($function) ){
				$method = $this->getMethod($function);
				if( method_exists($method[0], $method[1]) ){
					$functions[$k] = $method;
					continue;
				}

				throw new \RuntimeException('Unknow prepare function "' . $function . '"');
			} else {
				$checkedFunctions[] = $function;
			}
		}

		return $functions;
	}

	private function convertStrToArray($str){
		$array = explode(",", $str);
		$array = array_map("trim", $array);
		return $array;
	}

	private function getMethod($functionName){
		static $cache = [];
		if( isset($cache[$functionName]) )
			return $cache[$functionName];

		$method = "convertTo" . \T50Text::camelCase($functionName);
		$cache[$functionName] = [$this, $method];
		return $cache[$functionName];
	}

	/**
		Custom prepared methods vvv
	*/
	private function convertToBoolean($value){
		$value = (string) $value;
		$value = strtolower(trim($value));

		if( in_array($value, ["Y", "true", "1", "y", "yes"]) )
			return true;

		if( in_array($value, ["N", "false", "0", "n", "no"]) )
			return false;

		return "";
	}

	private function convertToStrToInt($value){
		$value = preg_replace("#[^\d-]#", "", $value);
		return (int) $value;
	}
}
