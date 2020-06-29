<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


use Agregator\Components\BaseComponent;
use Agregator\Components\Traits\ComponentDI;
use Agregator\Components\Traits\ComponentData;

class NewsEditComponent extends BaseComponent
{
	use ComponentDI;
	use ComponentData;

	private function save(){
		if( $_POST["send_form"] != "Y" )
			return;

		$valid = $this->prepare([
				"brand" => "intval",
				"theme" => "intval",
				"group*" => "intval",
				"title" => "htmlspecialchars",
				"text" => "pass",
				"fix_in_header" => "null, boolean",
				"comment" => "htmlentities",
			])->validate([
				"brand, theme" => "positive",
				"group" => "required",
				"group*" => "positive",
				"title, text" => "required",
				"fix_in_header" => "bool",
				"comment" => function ($comment){
					if( $this->arParams["IS_NEW"] )
						return true;
					return !empty($comment);
				},
			]);

		foreach($this->input as $code => $value)
		    $this->arResult["FIELDS_VALUE"][$code] = $value;

		if( !$valid ){
			$this->addError("Ошибка");
			$fields = array_keys($this->getErrors());
			$this->arResult["FIELDS_ERROR"] = array_fill_keys($fields, true);
			return;
		}

		$newCode = $this->Storage->save($this->arResult["ID"], $this->input);
		if( $newCode ){
			LocalRedirect("/news/");
			exit();
		} else {
			$this->addError("Ошибка. Не сохранилось!");
		}
	}

	function executeComponent(){
		$this->arResult = $this->InitialData->getData();
		if( empty($this->arResult) )
			$this->addError("Ошибка инициализации!");

		$this->arResult["FIELDS_VALUE"] = array(
			"brand" => $this->arResult["PROPERTY_BRAND_VALUE"],
			"theme" => $this->arResult["PROPERTY_THEME_ENUM_ID"],
			"group" => $this->arResult["PROPERTY_TARGET_MAN_GROUPS_VALUE"],
			"title" => $this->arResult["NAME"],
			"text" => $this->arResult["DETAIL_TEXT"],
			"comment" => $this->arResult["PREVIEW_TEXT"],
			"fix_in_header" => $this->arResult["PROPERTY_FIX_IN_HEADER_VALUE"],
		);
		if( $this->arParams["IS_NEW"] )
			$this->arResult["FIELDS_VALUE"]["title"] = "";

		$this->save();

		$this->addToJs("ID", $this->arResult["ID"]);
		$this->addToJs("GROUPS", $this->arResult["FIELDS_VALUE"]["group"]);

		$this->IncludeComponentTemplate();
	}

	private function addError($message){
		if( !isset($this->arResult["ERRORS"]) )
			$this->arResult["ERRORS"] = array();

		$this->arResult["ERRORS"][] = $message;
	}

	private function addToJs($var, $value){
		if( !isset($this->arResult["JS_COMMON_DATA"]) )
			$this->arResult["JS_COMMON_DATA"] = array();

		$this->arResult["JS_COMMON_DATA"][$var] = $value;
	}
}
/*
/news/edit/asdasd/
IS_NEW = false
ID = 1351

/news/add/
IS_NEW = ture
ID = detectId

*/