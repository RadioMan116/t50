<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach($arResult["VARIABLES"] as $code => $val)
	$arParams[$code] = $val;

$arParams["IS_NEW"] = false;

$APPLICATION->IncludeComponent("t50:news.edit", "", $arParams, $component);