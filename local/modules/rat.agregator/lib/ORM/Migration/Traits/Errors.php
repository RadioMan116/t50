<?php

namespace ORM\Migration\Traits;

trait Errors
{
	protected $errors = array();

	function getErrors(){
		return $this->errors;
	}

	function hasErrors(){
		return !empty($this->errors);
	}

}