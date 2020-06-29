<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult["BRANDS"] = [1 => "ВНЕ БРЕНДА"] + $arResult["BRANDS"];

array_unshift($arResult["GROUPS"], [
	"NAME" => "Все",
	"ID" => 0,
]);

