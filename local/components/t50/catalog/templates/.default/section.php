<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach($arResult["VARIABLES"] as $code => $val)
	$arParams[$code] = $val;

$APPLICATION->IncludeComponent("t50:catalog.default", "", $arParams, $component);