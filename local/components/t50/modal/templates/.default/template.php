<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if( file_exists(__DIR__ . $arResult["DATA_PATH"]) )
	require __DIR__ . $arResult["DATA_PATH"];

require __DIR__ . $arResult["TEMPLATE_PATH"];

$this->addExternalJS($this->GetFolder() . "/" . $arResult["JS_PATH"]);
$this->addExternalCss($this->GetFolder() . "/" . $arResult["CSS_PATH"]);