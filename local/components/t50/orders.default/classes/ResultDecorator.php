<?php

namespace OrdersDefaultComponent;
use rat\agregator\Order;
use T50Date;
use rat\agregator\Basket;

use T50GlobVars;

class ResultDecorator
{
	function decorateOrder(array $data){
        static $shops, $sources, $statuses, $cities, $managers;

        $shops = $shops ?? T50GlobVars::get("CACHE_SHOPS");
        $data["SHOP"] = $shops[$data["UF_SHOP"]]["NAME"];

        $sources = $sources ?? Order::getEnum("UF_SOURCE", true, true);
        $data["SOURCE"] = $sources[$data["UF_SOURCE"]];

        $statuses = $statuses ?? Order::getEnum("UF_STATUS", true, true);
        $data["STATUS"] = $statuses[$data["UF_STATUS"]];

        $cities = $cities ?? Order::getEnum("UF_CITY", true, true);
        $data["CITY"] = $cities[$data["UF_CITY"]];

        $managers = $managers ?? T50GlobVars::get("MANAGERS");
        $data["MANAGER"] = $managers[$data["UF_MANAGER_ID"]]["NAME"];

        $data["DATE_CREATE"] = T50Date::bxDate($data["UF_DATE_CREATE"], "d.m.Y H:i:s");

        $data["FLAGS"] = Order::getFlags($data["UF_FLAGS"]);

        return $data;
    }

    function decorateBasket(array $data){
        static $suppliers, $paymentTypes;

        $suppliers = $suppliers ?? T50GlobVars::get("CACHE_SUPPLIERS");
        $data["SUPPLIER"] = $suppliers[$data["UF_SUPPLIER"]]["NAME"];

        $paymentTypes = $paymentTypes ?? Basket::getEnum("UF_PAYMENT_TYPE", true, true);
        $data["PAYMENT_TYPE"] = $paymentTypes[$data["UF_PAYMENT_TYPE"]];

        $data["UF_PRICE_SALE"] *= $data["UF_QUANTITY"];
        $data["UF_PRICE_PURCHASE"] *= $data["UF_QUANTITY"];
        $data["UF_COMMISSION"] *= $data["UF_QUANTITY"];

        return $data;
    }

    function decorateDelivery(array $data){
        $data["DATE"] = T50Date::bxDate($data["UF_DATE"], "d.m.Y");
    	return $data;
    }

    function decorateAccount(array $data){
    	return $data;
    }
}