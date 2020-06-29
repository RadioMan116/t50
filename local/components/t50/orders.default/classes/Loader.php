<?php

namespace OrdersDefaultComponent;

use rat\agregator\Order;
use rat\agregator\Basket;
use rat\agregator\Account;
use rat\agregator\Delivery;
use rat\agregator\Installation;
use rat\agregator\Complaint;
use rat\agregator\Fine;
use rat\agregator\Client;
use rat\agregator\OrderProperty;
use ORM\ORMInfo;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\UI\PageNavigation;
use T50Date;
use T50GlobVars;

class Loader
{
    private $filter;
    private $nav;
    private $decorator;

    function __construct(){
        $this->decorator = new ResultDecorator;
    }

    function setFilter(Filter $filter){
        $this->filter = $filter;
        return $this;
    }

    function load(){
        $ids = $this->loadForPagen();
        return $this->loadDetails($ids);
    }

    private function loadForPagen(){
        $ids = [];

        // ORMInfo::sqlTracker("start");
        $this->nav = new PageNavigation("products");
        $this->nav->allowAllRecords(true)->setPageSizes([10, 20, 30])->setPageSize(20)->initFromUri();
        $res = Order::clas()::getList([
            "select" => ["ID"],
            "runtime" => $this->getRunTime(true),
            "filter" => $this->filter->getFilter(),
            "count_total" => true,
            'offset' => $this->nav->getOffset(),
            'limit' => $this->nav->getLimit(),
            'group' => ["ID"],
            "order" => ["ID" => "DESC"],
        ]);
        $this->nav->setRecordCount($res->getCount());
        // ORMInfo::sqlTracker("show");
        $result =  $res->fetchAll();
        $ids = array_column($result, "ID");
        return $ids;
    }

    private function loadDetails(array $ids = []){
        $select = $this->prepareSelect(array(
            "*",
            "BK" => [
                "ID", "UF_PRODUCT_URL", "UF_SUPPLIER", "UF_COMMISSION", "UF_PAYMENT_TYPE", "UF_NAME",
                "UF_COM_SUPPLIER", "UF_PRICE_PURCHASE", "UF_PRICE_SALE", "UF_START_PRICE_SALE", "UF_QUANTITY"
            ],
            "AC" => ["ID", "UF_ACCOUNT", "UF_BASKET_ID"],
            "DL" => ["ID", "UF_DATE", "UF_BASKET_ID"],
            "CL" => ["ID", "UF_FIO"],
        ));

        $filter = array_merge(["ID" => $ids, $this->filter->getInnerFilter()]);

        // ORMInfo::sqlTracker("start");
        $res = Order::clas()::getList([
            "select" => $select,
            "runtime" => $this->getRunTime(),
            "filter" => $filter,
            "order" => ["ID" => "DESC"],
        ]);
        // ORMInfo::sqlTracker("show");

        $arResult = [];

        while( $result = $res->Fetch() ){
            $orderId = $result["ID"];

            if( !isset($arResult[$orderId]) ){
                $order = array();
                foreach($result as $code => $value){
                    if( substr($code, 0, 6) != "ORDER_" )
                        $order[$code] = $value;

                    if( substr($code, 0, 9) == "ORDER_CL_" )
                       $order["CLIENT"][substr($code,9)] = $value;
                }

                $order = $this->decorator->decorateOrder($order);
                $order["CLIENT"] = [];
                $order["BASKET"] = [];
                $order["DELIVERY"] = [];
                $order["ACCOUNT"] = [];

                $arResult[$orderId] = $order;
            }

            $basket = $delivery = $account = array();
            foreach($result as $code => $value){
                $prefix = substr($code, 6, 2);
                $code = substr($code,9);
                if( $prefix == "BK" )
                   $basket[$code] = $value;

                if( $prefix == "DL" )
                   $delivery[$code] = $value;

                if( $prefix == "AC" )
                   $account[$code] = $value;
            }

            $arResult[$orderId]["BASKET"][] = $this->decorator->decorateBasket($basket);

            $delivery = $this->decorator->decorateDelivery($delivery);
            $arResult[$orderId]["DELIVERY"][$delivery["UF_BASKET_ID"]] = $delivery;

            $account = $this->decorator->decorateAccount($account);
            $arResult[$orderId]["ACCOUNT"][$account["UF_BASKET_ID"]] = $account;
        }

        $installations = $this->loadInstallations($ids);
        foreach($arResult as $orderId => $item)
            $arResult[$orderId]["INSTALLATIONS"] = $installations[$orderId];

        $fines = $this->loadFines($ids);
        foreach($arResult as $orderId => $item)
            $arResult[$orderId]["FINES"] = $fines[$orderId];

        return $arResult;
    }

    private function loadInstallations(array $ordersId){
        if( empty($ordersId) )
            return [];

        $res = Installation::clas()::getList(["filter" => ["UF_ORDER_ID" => $ordersId]]);
        $arResult = [];
        while( $result = $res->Fetch() ){
            $orderId = $result["UF_ORDER_ID"];
            $basketId = $result["UF_BASKET_ID"];
            if( !isset($arResult[$orderId]) )
                $arResult[$orderId] = array("COMMON" => []);

            $result = $this->prepareInstallation($result);

            if( $basketId > 0 ){
                $arResult[$orderId][$basketId] = $result;
            } else {
                $arResult[$orderId]["COMMON"][] = $result;
            }
        }
        return $arResult;
    }

    private function loadFines(array $ordersId){
        // ORMInfo::sqlTracker("start");
        $res = Fine::clas()::getList([
            "select" => ["UF_ORDER_ID", "SUM"],
            "filter" => ["UF_ORDER_ID" => $ordersId, ">UF_AMOUNT" => 0],
            "runtime" => [new ExpressionField('SUM', 'SUM(UF_AMOUNT)')],
            "group" => "UF_ORDER_ID"
        ]);
        // ORMInfo::sqlTracker("show");
        $arResult = [];
        while( $result = $res->Fetch() ){
            $arResult[$result["UF_ORDER_ID"]] = $result["SUM"];
        }

        return $arResult;
    }

    private function prepareInstallation(array $data){
        return $data;
    }

    function getNav(){
        return $this->nav;
    }

    private function prepareSelect(array $select, $prefix = ""){
        $result = [];
        foreach($select as $k => $field){
            if( is_int($k) ){
                $result[] = $prefix . $field;
            } else {
                $result = array_merge($result, $this->prepareSelect($field, "{$k}."));
            }
        }
        return $result;
    }

    private function getRunTime($full = false){
        $runtime = [
            Order::getRef(Basket::class, "BK"),
            new ReferenceField("AC", Account::clas(), ['=this.BK.ID' => 'ref.UF_BASKET_ID']),
            new ReferenceField("DL", Delivery::clas(), ['=this.BK.ID' => 'ref.UF_BASKET_ID']),
            new ReferenceField("CL", Client::clas(), ['=this.UF_CLIENT' => 'ref.ID']),
        ];
        if( $full ){
            $runtime[] = new ReferenceField("FN", Fine::clas(), ['=this.ID' => 'ref.UF_ORDER_ID']);
            $runtime[] = new ReferenceField("INST", Installation::clas(), ['=this.ID' => 'ref.UF_ORDER_ID']);
            $runtime[] = new ReferenceField("CLAIM", Complaint::clas(), ['=this.ID' => 'ref.UF_ORDER_ID']);
            $propertiesRuntime = $this->filter->getPropertyBuilder()->getRuntime();
            $runtime = array_merge($runtime, $propertiesRuntime);
        }
        return $runtime;
    }
}

