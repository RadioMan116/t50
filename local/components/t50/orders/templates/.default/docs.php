<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach($arResult["VARIABLES"] as $code => $val)
	$arParams[$code] = $val;

$APPLICATION->setTitle("Заказ №" . $arParams["ORDER_ID"]);

$APPLICATION->IncludeComponent("t50:orders.docs", "", $arParams, $component);