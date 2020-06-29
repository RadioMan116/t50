<?php

namespace ReportsBestModelSupplierComponent;

trait TraitData
{
	private $input;
	private $initilData = [];

	function setInput(\StdClass $input){
		$this->input = $input;
		return $this;
	}

	function setData(array $initilData){
		$this->initilData = $initilData;
		return $this;
	}
}