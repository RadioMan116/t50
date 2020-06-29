<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Agregator\Manager\Manager;

if( !Manager::canWorkWithNews() ){
	CHTTP::SetStatus("403 Forbidden");
	include $_SERVER["DOCUMENT_ROOT"] . "/.error_pages/access_denied.php";
	return;
}

foreach($arResult["VARIABLES"] as $code => $val)
	$arParams[$code] = $val;

$arParams["IS_NEW"] = true;

$APPLICATION->IncludeComponent("t50:news.edit", "", $arParams, $component);