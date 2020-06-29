<?php

namespace Agregator\Integration\RemCity;

use rat\agregator\OrderProperty;
use rat\agregator\Order as OrderOrm;
use rat\agregator\Client as ClientOrm;
use rat\agregator\Installation as InstallationOrm;
use Agregator\Order\InstallationPrices;
use Agregator\IB\Section;
use Bitrix\Main\Entity\ReferenceField;
use T50DB;

class Order
{
    use Traits\Logger;

    const SEND_DISPATCHER = false;

    protected $api;
    protected $installationRows;
    protected $orderId;

    function __construct(int $orderId){
        $this->api = new Api;
        $this->orderId = $orderId;
    }

    function sync(){
        $remcityOrder = $this->buildOrder();
        return $this->sendOrder($remcityOrder);
    }

    function buildOrder(){
        $data = OrderOrm::clas()::getRow([
            "select" => ["*", "CL.*"],
            "filter" => ["ID" => $this->orderId],
            "runtime" => [new ReferenceField("CL", ClientOrm::clas(), ['=this.UF_CLIENT' => 'ref.ID'])]
        ]);

        $orderStruct = new Structures\Order;
        $orderStruct->name = iconv("utf-8", "cp1251", $data["ORDER_CL_UF_FIO"]);
        $orderStruct->phone = iconv("utf-8", "cp1251", $data["ORDER_CL_UF_PHONE"]);
        $orderStruct->address = iconv("utf-8", "cp1251", ClientOrm::buildFullAddress($data, "ORDER_CL_"));
        $orderStruct->email = $data["ORDER_CL_UF_EMAIL"];
        $orderStruct->floor = $data["ORDER_CL_UF_FLOOR"];
        $orderStruct->flat = $data["ORDER_CL_UF_APARTMENT"];
        $orderStruct->status = 1;
            $orderStruct->date_delivery = date("Y-m-d");
            $orderStruct->interval = 1;
        $orderStruct->services = $this->buildServices();

        return $orderStruct;
    }

    function buildServices(){
        $prices = new InstallationPrices;
        $prices->setProvider("remcity");

        $sections = Section::getList("remcity", true);

        $arResult = [];
        foreach($this->getInstallationRows($this->orderId) as $row){
            $flags = InstallationOrm::getFlags($row["UF_FLAGS"]);
            $isFree = $flags["US_COSTS"];

            $service = $prices->getServiceById($row["UF_SERVICE_ID"]);

            if( $isFree ){
                $price = (int) $service["PROPERTY_PRICE_VALUE"];
            } else {
                $price = (int) $row["UF_PRICE_SALE"];
            }

            $section = $sections[$service["IBLOCK_SECTION_ID"]];

            $serviceStruct = new Structures\Service;
            $serviceStruct->model_type_id = (int) $section["UF_REMCITY_ID"];
            $serviceStruct->service_price_id = (int) $service["PROPERTY_REMCITY_ID_VALUE"];
            $serviceStruct->price = $price;
            $serviceStruct->is_free = $isFree;
            $serviceStruct->comment = iconv("utf-8", "cp1251", $row["UF_COMMENT"]);

            $arResult[] = $serviceStruct;
        }

        return $arResult;
    }

    private function getInstallationRows(){
        if( isset($this->installationRows) )
            return $this->installationRows;

        $providersInfo = InstallationOrm::getEnum("UF_PROVIDER", false);

        $filter = [
            "UF_ORDER_ID" => $this->orderId,
            "UF_PROVIDER" => $providersInfo["remcity"]["id"],
            ">UF_SERVICE_ID" => 0,
        ];

        $this->installationRows = InstallationOrm::clas()::getList(compact("filter"))->fetchAll();

        return $this->installationRows;
    }


    protected function sendOrder(Structures\Order $order){
        $orderData = $order->getData();
        $orderData["services"] = array_map(function ($service){
            return $service->getData();
        }, $orderData["services"]);

        if( !$order->isValid() ){
            $this->log("order not valid " . var_export($orderData, true));
            return false;
        }

        T50DB::startTransaction();

        $id = (int) $this->api->sendPost("/orders/")["id"];
        if( $id <= 0 ){
            $this->log("create /orders/ return data.id <= 0");
            return T50DB::rollback();
        }

        if( !OrderProperty::set($this->orderId, "REMCITY_ORDER_ID", $id) ){
            $this->log("cannot save property REMCITY_ORDER_ID with value {$id}");
            return T50DB::rollback();
        }

        $number = (int) $this->api->sendPost("/orders/{$id}/", $orderData)["number"];
        if( $number <= 0 ){
            $this->log("save /orders/{$id}/ return data.number <= 0");
            return T50DB::rollback();
        }

        if( !OrderProperty::set($this->orderId, "REMCITY_ORDER_NUM", $number) ){
            $this->log("cannot save property REMCITY_ORDER_NUM with value {$number}");
            return T50DB::rollback();
        }

        if( self::SEND_DISPATCHER ){
            $data = $this->api->sendPost("/orders/{$id}/", ["status" => 7]);
            if( empty($data) ){
                $this->log("cannot send dispatcher id {$id}");
            }
        }

        return T50DB::commit();
    }

    static function isSent(int $orderId){
        return (OrderProperty::getInt($orderId, "REMCITY_ORDER_ID") > 0);
    }
}
