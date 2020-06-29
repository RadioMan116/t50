<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$page = $this->getComponent()->GetTemplatePage();

foreach($arResult["VARIABLES"] as $code => $val)
	$arParams[$code] = $val;

$APPLICATION->IncludeComponent("t50:orders.{$page}", "", $arParams, $component);