<?php

namespace Agregator\Integration\RemCity\Structures;

use Agregator\Integration\RemCity\Api;
use T50Date;

class Order extends Structure
{
    static $requiredFields = [
        "name", "phone", "address",
        "date_delivery", "interval", "status",
        "services"
    ];

    static $optionalFields = [
        "email", "floor", "flat", "comment",
    ];

    protected static $statusesId;
    protected static $intervalsId;

    function __construct(){
        $this->prepareDict();
    }

    private function prepareDict(){
        if( isset(self::$statusesId) && isset(self::$intervalsId) )
            return;

        $api = new Api;
        $data = $api->getDict();

        self::$statusesId = [];
        foreach($data["statuses"] as $item)
           self::$statusesId[] = $item["id"];

        self::$intervalsId = [];
        foreach($data["intervals"] as $item)
           self::$intervalsId[] = $item["id"];
    }


    protected function check($name, $value): bool{
        switch ($name) {
            case "services":
                return $this->checkServices($value);

            case "status":
                return in_array($value, self::$statusesId);

            case "interval":
                return in_array($value, self::$intervalsId);

            case "date_delivery":
                return T50Date::check($value, "Y-m-d");

            case "email":
                return filter_var($value, FILTER_VALIDATE_EMAIL);

            case "floor":
            case "flat":
                return $value > 0;
        }

        return true;
    }

    protected function preparePhone($phone){
        return preg_replace(["#[^\d]#", "#^8#"], ["", "7"], $phone);
    }

    private function checkServices($services){
        if( !is_array($services) || empty($services) )
            return false;

        foreach($services as $service){
            if( !($service instanceOf Service) )
                return false;

            if( !$service->isValid() )
                return false;
        }

        return true;
    }

}