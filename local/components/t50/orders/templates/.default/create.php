<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Agregator\Order\Order;

$shopId = (int) $_POST["shop"];
$order = new Order();
$orderId = (int) $order->create($shopId);

if( $orderId <= 0 ){
	@define("ERROR_403", "Y");
	CHTTP::SetStatus("403 Forbidden");
	return;
}

LocalRedirect("/orders/{$orderId}/");
exit();