<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$APPLICATION->setTitle("Рекламации");

$APPLICATION->IncludeComponent("t50:orders.claims", "", $arParams, $component);