<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Agregator\Components\BaseComponent;
use Agregator\Components\Traits\ComponentDI;
use rat\agregator\Order as OrderOrm;
use Agregator\Order\Order;

class OrdersDetailComponent extends BaseComponent
{
    use ComponentDI;

    function executeComponent(){
        $orderId = (int) $this->arParams["ORDER_ID"];

        if( !Order::hasAccess($orderId) ){
            CHTTP::SetStatus("403 Forbidden");
            die();
        }

        Order::take($orderId);

    	if( $_POST["order_view"] == "send" ){
    		$this->OrderView->update();
    		localredirect($GLOBALS["APPLICATION"]->getCurPage());
    	}

    	$this->arResult["ORDER_VIEW"] = $this->OrderView->getData();
    	$this->arResult["ORDER_VIEW_BLOCKS"] = $this->OrderView->blocksGrouped();
		$this->IncludeComponentTemplate();
	}
}