<?php

namespace OrdersDocsComponent;
use Agregator\Order\Docs;
use Agregator\Common\NumberText;

abstract class Base
{
	protected $errors = array();

	protected function getGenerator(){
		$templateName = static::getTemplateName();

		$ext = strtolower(pathinfo($templateName, PATHINFO_EXTENSION));
		if( in_array($ext, ["doc", "docx"]) )
			$obj = new Docs\Word();

		if( in_array($ext, ["xls", "xlsx"]) )
			$obj = new Docs\Excel();

		if( !isset($obj) )
			return ;

		$path = $this->arParams["TEMPLATE_DIR"] . "/" . $templateName;
		if( !file_exists($path) )
			return ;

		$obj->init($path);
		return $obj;
	}

	abstract function generate();
	abstract function getTemplateName();

	protected function numToText(int $number, $genus = null){
		$numberText = new NumberText();
		$numberText->setNum($number);
		if( isset($genus) )
			$numberText->setGenus($genus);
		return $numberText->getText();
	}

	protected function addError(string $error){
		$this->errors[] = $error;
	}

	function getErrors(){
		return $this->errors;
	}
}
