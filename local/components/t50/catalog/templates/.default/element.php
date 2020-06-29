<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach($arResult["VARIABLES"] as $code => $val)
	$arParams[$code] = $val;

$arParams["CITY"] = "MSK";
$city = htmlspecialchars($_GET["city"]);
if( in_array($city, ["MSK", "SPB"]) )
	$arParams["CITY"] = $city;

$APPLICATION->IncludeComponent("t50:catalog.element", "", $arParams, $component);