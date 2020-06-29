<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$page = $this->getComponent()->GetTemplatePage();

foreach($arResult["VARIABLES"] as $code => $val)
	$arParams[$code] = $val;


if( !rat\agregator\Order::isExists($arParams["ORDER_ID"]) ){
	@define("ERROR_404", "Y");
	CHTTP::SetStatus("404 Not Found");
	return;
}

$APPLICATION->setTitle("Заказ №" . $arParams["ORDER_ID"]);

$APPLICATION->IncludeComponent("t50:orders.{$page}", "", $arParams, $component);