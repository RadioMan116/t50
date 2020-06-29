<?php

namespace Agregator\Components;

use Agregator\Components\Validation\Validator;
use Bitrix\Main\Page\Asset;
use T50Html;

abstract class BaseAjaxComponent
{
	use Traits\ComponentData;

	public $arParams = array();
	protected $arResult = array();
	protected $input;
	protected $validator;

	function __construct($component = null){
		$this->input = new \StdClass;
		$this->validator = new Validator;

		if( isset(class_uses(static::class)[Traits\ComponentDI::class]) )
			static::registerAutoload();
	}

	protected function getName(){
		return static::class;
	}

	protected function getPath(){
		static $paths = [];
		if( isset($paths[static::class]) )
			return $paths[static::class];

		$reflector = new \ReflectionClass(static::class);
		$path = $reflector->getFileName();
		$paths[static::class] = dirname($path);
		return $paths[static::class];
	}

	protected function validateErrors(){
		$this->resultJson(false, "", null, $this->getErrors());
	}

	protected function resultJson($result, $message = "", $additional = null, $errors = []){
		if( is_bool($result) )
			$result = ( $result ? "success" : "fail" );
		$data = [
			"result" => $result,
			"message" => $message,
		];

		if( isset($additional) )
			$data["data"] = $additional;

		if( !empty($errors) )
			$data["errors"] = $errors;

		echo json_encode($data);
		die();
	}

	protected function includeTemplate($name, array $arResult = array()){
		$templatePath = $this->getPath() . "/templates/ajax/${name}.php";
		$jsPath = $this->getPath() . "/templates/ajax/${name}.js";
		require $templatePath;
		T50Html::includeJs($jsPath);
	}
}