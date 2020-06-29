<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

use Bitrix\Main;

$eventManager = Main\EventManager::getInstance();
$registerEvent = function($class, $event) use($eventManager) {
	$className = str_replace("rat\\agregator\\", "", $class);
	$eventName = $className . $event;
	$eventManager->addEventHandler("", $eventName, [$class, lcfirst($event)]);
};

$registerEvent(rat\agregator\OrderProperty::class, "OnBeforeAdd");

$registerEvent(rat\agregator\Order::class, "OnBeforeUpdate");
$registerEvent(rat\agregator\Order::class, "OnBeforeAdd");

$registerEvent(rat\agregator\Product::class, "OnBeforeUpdate");
$registerEvent(rat\agregator\Product::class, "OnBeforeAdd");

$registerEvent(rat\agregator\ProductComment::class, "OnBeforeUpdate");
$registerEvent(rat\agregator\ProductComment::class, "OnBeforeAdd");

$registerEvent(rat\agregator\ProductPrice::class, "OnBeforeUpdate");
$registerEvent(rat\agregator\ProductPrice::class, "OnBeforeAdd");

$registerEvent(rat\agregator\Formula::class, "OnBeforeDelete");
$registerEvent(rat\agregator\Formula::class, "OnAfterDelete");