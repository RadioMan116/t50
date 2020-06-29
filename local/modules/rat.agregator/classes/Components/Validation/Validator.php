<?php

namespace Agregator\Components\Validation;

class Validator
{
	use Rules;

	private $errors = [];
	private $data = [];
	private $rules = [];

	protected $validRules = [
		'Required', 'Integer', 'Numeric', 'Bool',
		'Size', 'Between', 'Min', 'Max', 'Positive',
		'File', 'Mimes', 'Mimetypes',
		'DateFormat', 'DateEqual', 'After', 'AfterOrEqual', 'Before', 'BeforeOrEqual',
		'Email', 'In', 'Regex', 'NoRegex'
	];

	function setData($data){
		$this->data = (object) $data;
		return $this;
	}

	function setRules($rules){
		$this->rules = $this->parseRules($rules);
		return $this;
	}

	function validate(){
		$this->errors = [];
		foreach($this->rules as $field => $rules){
			$eachValueFromArray = false;
			if( substr($field, -1) == "*" ){
				$eachValueFromArray = true;
				$field = substr($field, 0, -1);
			}

			$value = $this->data->$field;
			if( !isset($value) && !isset($rules["Required"]) )
				continue;

			foreach($rules as $rule => $params){
				$method = "validate" . $rule;
				if( $eachValueFromArray ){
					$success = is_array($value);
					foreach($value as $valueItem){
						if( !$this->{$method}($valueItem, $field . "*", ...$params) ){
							$success = false;
							break;
						}
					}
				} else {
					$success = $this->{$method}($value, $field, ...$params);
				}
				if( !$success ){
					if( !isset($this->errors[$field]) )
						$this->errors[$field] = array();

					$params = array_map(function ($param){
						return ( !is_string($param) && is_callable($param) ? "eval" : $param );
					}, $params);

					$this->errors[$field][] = $rule . "(" .  implode(", ", $params). ")";
				}
			}
		}

		return empty($this->errors);
	}

	function getData(){
		return $this->data;
	}

	function getErrors(){
		return $this->errors;
	}

	private function isMultiple($rawField){
		$flag = substr($rawField, -1);
		$field = $rawField;
		if( $flag == "*" || $flag == "~" ){
			$field = substr($rawField, 0, -1);
		} else {
			$flag = "";
		}
		return [$field, $flag];
	}

	private function parseRules($rawFieldsRules){
		$arResult = array();

		// prepare fields
		foreach($rawFieldsRules as $rawField => $rule){
			$fields = array_map("trim", explode(",", $rawField));
			foreach($fields as $field)
				$arResult[$field] = $rule;
		}

		// prepare rules
		foreach($arResult as $field => $rawRules){
			if( is_string($rawRules) ){
				$arResult[$field] = $this->prepareStringRules($rawRules, $field);
				continue;
			}

			if( is_callable($rawRules) )
				$rawRules = [$rawRules];

			$arResult[$field] = $this->prepareArrayRules($rawRules);
		}

		// check rules
		foreach($arResult as $field => $rules){
			foreach($rules as $rule => $params){
				if( $rule == "Callable" )
					continue;

				if( !in_array($rule, $this->validRules) )
					throw new \RuntimeException("undefined validation rule \"{$rule}\" ({$field})");
			}
		}

		return $arResult;
	}

	private function prepareArrayRules($rules, $field){
		$ruleAr = array();
		foreach($rules as $code => $rule){
			$params = [];
			if( is_string($code) ){
				$params = [$rule];
				$rule = $code;
			}

			if( is_callable($rule) ){
				$ruleAr["Callable"] = [$rule];
			} else {
				$rule = \T50Text::camelCase($rule);
				$ruleAr[$rule] = $params;
			}
		}
		return $ruleAr;
	}

	private function prepareStringRules($ruleStr, $field){
		$ruleAr = array();
		$rules = explode("|", $ruleStr);
		foreach($rules as $rawRule){
			list($rule, $ruleParams) = explode(":", $rawRule, 2);
			$rule = \T50Text::camelCase($rule);
			$rule = trim($rule);
			if( isset($ruleParams) ){
				$ruleParams = array_map("trim", explode(",", $ruleParams));
			} else {
				$ruleParams = [];
			}

			$ruleAr[$rule] = $ruleParams;
		}
		return $ruleAr;
	}
}