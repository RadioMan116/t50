<?php

use Bitrix\Main\Page\Asset;

class T50Html
{
	const T_JS = "JS";
	const T_CSS = "CSS";

	static function getAssets($path){
		if( substr($path, 0, 2) == "//" )
			return substr($path, 1);

		$mainTemplateRoot = $mainTemplateRoot ?? str_replace("light", "main", SITE_TEMPLATE_PATH) . "/assets";
		return $mainTemplateRoot . $path;
	}

	static function getSvg($hash){
		return self::getAssets("/images/icon.svg#{$hash}");
	}

	static function includeJs($absPath){
		if( !file_exists($absPath) )
			return;

		$absPath = realpath($absPath);
		$docRoot = realpath($_SERVER["DOCUMENT_ROOT"]);
		$webUrl = str_replace($docRoot, "", $absPath);
		$webUrl = str_replace("\\", "/", $webUrl);
		echo '<script type="text/javascript" src="' . $webUrl . '"></script>';
	}

	static function includeAssets($type, $arrPathRootFromAssets = array()){
		$asset = Asset::getInstance();
		if( $type == T50Html::T_JS ){
			foreach($arrPathRootFromAssets as $path)
				$asset->addJs(self::getAssets($path));
		}
		if( $type == T50Html::T_CSS ){
			foreach($arrPathRootFromAssets as $path)
				$asset->addCss(self::getAssets($path));
		}
	}

	static function inc($name, array $args = [], $once = true){
		self::incPath($_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . "/inc/{$name}.php", $args);
	}

	static function incPath($path, array $args = [], $once = true){
		extract($args);
		( $once ? require_once $path : require $path );
	}

	static function onceShow($name){
		static $names = [];
		$show = ( !isset($names[$name]) );
		$names[$name] = true;
		return $show;
	}

	static function fnum($number, $nullDefault = "-"){
		if( $number == 0 )
			return $nullDefault;

		return number_format($number, 0, ".", " ");
	}

	static function dataAttrs($data = array()){
		if( empty($data) )
			return "";

		$result = array();
		foreach($data as $code => $val){
			$code = T50Text::snakeCase($code);
		    $result[] = "data-{$code}=\"{$val}\"";
		}

		return implode(" ", $result);
	}

	static function uniqStr($name){
		static $counter = 0;
		return $name . "_" . ++ $counter;
	}

	static function select($name = "", $data = array(), $options = array()){
		$attributes = self::generateAttributes($name, $options);
		$html = "<select {$attributes} >";

		$value = $options["val"] ?? $_REQUEST[$name];

		if( isset($options["prepend"]) && is_array($options["prepend"]) )
			$data = $options["prepend"] + $data;

		if( isset($options["empty"]) )
			$data = ["" => $options["empty"]] + $data;

		$multiple = in_array("mult", $options, true);

		foreach($data as $val => $text){
			if( $multiple ){
				$selected = ( in_array($val, $value) ? "selected " : "" );
			} else {
				$selected = ( $val == $value ? "selected " : "" );
			}
			$text = htmlspecialchars($text);
			$html .= "<option value=\"{$val}\" {$selected}>{$text}</option>";
		}
		$html .= "</select>";
		return $html;
	}

	static function mselect($name = "", $data = array(), $options = array()){
		$options[] = "mult";
		return self::select($name, $data, $options);
	}

	static function checkbox($name = "", $val = "", $options = array()){
		return self::checkedInput("checkbox", $name, $val, $options);
	}

	static function radio($name = "", $val = "", $options = array()){
		return self::checkedInput("radio", $name, $val, $options);
	}

	static function checkboxLabel($name = "", $val = "", $optInput = [], $text = "", $optLabel = []){
		return self::checkedInputLabel("checkbox", $name, $val, $optInput, $text, $optLabel);
	}

	static function radioLabel($name = "", $val = "", $optInput = [], $text = "", $optLabel = []){
		return self::checkedInputLabel("radio", $name, $val, $optInput, $text, $optLabel);
	}

	private static function checkedInput($type = "", $name = "", $val = "", $options = array()){
		$attributes = self::generateAttributes($name, $options);
		$multiple = (bool) substr_count($name, "[]");
		if( $multiple ){
			$value = $options["val"] ?? $_REQUEST[trim(str_replace("[]", "", $name))];
			$checked = ( in_array($val, $value) ? "checked " : "" );
		} else {
			$value = $options["val"] ?? $_REQUEST[$name];
			$checked = ( $value == $val ? "checked " : "" );
		}

		$html = "<input type=\"{$type}\" {$attributes} value=\"{$val}\" {$checked}/>";
		return $html;
	}

	private static function checkedInputLabel($type="", $name="", $val="", $optInp=[], $text="", $optLabel=[]){
		static $counterFor = 0;
		$optInp = self::optionsToArray($optInp);
		$optLabel = self::optionsToArray($optLabel);
		if( !isset($optInp["id"]) && !isset($optInp["idcls"]) )
			$optInp["id"] = $optLabel["for"] = $name . "_" . ++ $counterFor;

		$input = self::checkedInput($type, $name, $val, $optInp);
		$labelAttrs = self::generateAttributes("", $optLabel);
		$label = "<label {$labelAttrs} >{$text}</label>";
		return $input . $label;
	}

	private static function optionsToArray($options){
		if( is_string($options) )
			$options = ["cls" => $options];

		return $options;
	}

	private static function generateAttributes($name, $options){
		$attributes = array();
		$options = self::optionsToArray($options);

		$multiple = in_array("mult", $options, true);

		if( !empty($name) )
			$attributes["name"] = $name . ( $multiple ? "[]" : "" );

		if( isset($options["cls"]) ){
			if( $options["cls"] == "i" )
				$options["cls"] = "check-elem__input";
			if( $options["cls"] == "l" )
				$options["cls"] = "check-elem__label";
			if( $options["cls"] == "s" )
				$options["cls"] = "js-select form__select";
			$attributes["class"] = $options["cls"];
		}

		if( in_array("dis", $options, true) )
			$attributes["disabled"] = "disabled";

		if( $multiple )
			$attributes["multiple"] = "multiple";

		if( isset($options["idcls"]) ){
			$attributes["id"] = $options["idcls"];
			$attributes["class"] = $options["idcls"];
		}

		$validAttrs = array("id", "for");
		foreach($validAttrs as $attr){
		    if( isset($options[$attr]) )
				$attributes[$attr] = $options[$attr];
		}

		if( isset($options["data"]) ){
			foreach($options["data"] as $code => $val)
			    $attributes["data-{$code}"] = $val;
		}

		$attrs = array();
		foreach($attributes as $code => $val)
		    $attrs[] = $code . '="' . htmlspecialchars($val) . '"';

		return implode(" ", $attrs);
	}

	static function isHtmlNeedClassResize(){
		global $APPLICATION;
		$url = $APPLICATION->GetCurDir();

		if( preg_match("#^/catalog/#", $url) )
			return  true;

		if( preg_match("#^/orders/(?(?=\d+/)\d+/)$#", $url) )
			return  true;

		return false;
	}
}
