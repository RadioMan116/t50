<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Agregator\Manager\Manager;

$arResult["SHOPS"] = Manager::getAvailableShops();