<?php

namespace Agregator\Components\Validation;

trait Rules
{
	function validateCallable($value, $field, Callable $closure){
		return $closure($value);
	}

	function validateRequired($value, $field){
		if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif ( is_array($value) && count($value) == 0 ) {
			return false;
		}

		return true;
	}

	function validateRegex($value, $field, $pattern){
		if( !is_string($value) && !is_numeric($value) )
			return false;

		return preg_match($pattern, $value) > 0;
	}

	function validateNoRegex($value, $field, $pattern){
		return ! $this->validateRegex($value, $field, $pattern);
	}

	function validateEmail($value, $field){
		return filter_var($value, FILTER_VALIDATE_EMAIL);
	}

	function validateIn($value, $field, ...$array){
		return in_array($value, $array);
	}

	function validateFile($value, $field){
		return $this->isUploadedFile($value);
	}

	function validateMimes($value, $field, ...$mimes){
		if( !$this->isUploadedFile($value) )
			return false;

		$ext = GetFileExtension($value["name"]);
		$ext = strtolower($ext);
		$mimes = array_map("strtolower", $mimes);
		return in_array($ext, $mimes);
	}

	function validateMimetypes($value, $field, ...$mimetypes){
		if( !$this->isUploadedFile($value) )
			return false;

		$type = strtolower($value["type"]);
		$mimetypes = array_map("strtolower", $mimetypes);
		return in_array($type, $mimetypes);
	}

	function validateBool($value, $field){
		$acceptable = [true, false, 0, 1, '0', '1', 'Y', 'N', 'true', 'false'];
        return in_array($value, $acceptable, true);
	}

	function validateInteger($value, $field){
		return filter_var($value, FILTER_VALIDATE_INT) !== false;
	}

	function validateNumeric($value, $field){
		return is_numeric($value);
	}

	function validatePositive($value, $field){
		return is_numeric($value) && ( $value > 0 );
	}

	function validateMax($value, $field, $operand){
		return $this->getSize($value, $field) <= $operand;
	}

	function validateMin($value, $field, $operand){
		return $this->getSize($value, $field) >= $operand;
	}

	function validateBetween($value, $field, $min, $max){
		return $this->validateMin($value, $field, $min) && $this->validateMax($value, $field, $max);
	}

	function validateSize($value, $field, $size){
		return $this->getSize($value, $field) == $operand;
	}

	function validateDateFormat($value, $field, $format){
		$timestamp = strtotime($value);
		return $value == date($format, $timestamp);
	}

	function validateDateEqual($value, $field, $date){
		return $this->getTimestamp($value) == $this->getTimestamp($date);
	}

	function validateAfter($value, $field, $date){
		return $this->getTimestamp($value) > $this->getTimestamp($date);
	}

	function validateAfterOrEqual($value, $field, $date){
		return $this->getTimestamp($value) >= $this->getTimestamp($date);
	}

	function validateBefore($value, $field, $date){
		return $this->getTimestamp($value) < $this->getTimestamp($date);
	}

	function validateBeforeOrEqual($value, $field, $date){
		return $this->getTimestamp($value) <= $this->getTimestamp($date);
	}

	private function getSize($value, $field){
		$rules = @$this->rules[$field];
		if( empty($rules) )
			throw new \RuntimeException("undefined field \"{$field}\" in rules");

		if( is_int($value) || is_float($value) ){
			return $value;
		} elseif ( is_numeric($value) && isset($rules["Numeric"]) ){
			return $value;
        } elseif (is_array($value)) {
			if( isset($value["size"]) )
				return round($value["size"] / 1024);
            return count($value);
        }

        return mb_strlen($value);
	}

	private function getTimestamp($value, $field){
		if( is_int($value) )
			return $value;

		return strtotime($value);
	}

	private function isUploadedFile($value){
		if( !is_array($value) )
			return false;

		foreach(["name", "type", "tmp_name", "error", "size"] as $field){
			if( !isset($value[$field]) )
				return false;
		}
		if( $value["error"] != 0 )
			return false;

		return true;
	}
}