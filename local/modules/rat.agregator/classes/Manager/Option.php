<?php
namespace Agregator\Manager;


use CUserOptions;
use T50Reflection;
use RuntimeException;

class Option
{
    const CATEGORY = "t50";
    const ORDER_TABS = "t50_order_tabs";
    const ORDERS_LIST_COLUMNS = "t50_orders_list_columns";

    function set($name, array $data, $userId = false){
        $this->checkName($name);
        $result = CUserOptions::SetOption(self::CATEGORY, $name, $data, false, $userId);
        return $result;
    }

    function delete($name, $userId = false){
        $this->checkName($name);
        return CUserOptions::DeleteOption(self::CATEGORY, $name, false, $userId);
    }

    function get($name, $default = null, $userId = false){
        $this->checkName($name);
        $result = CUserOptions::GetOption(self::CATEGORY, $name, false, $userId);
        if( !isset($result) || $result === false  )
            return $default;

        return $result;
    }

    function checkName($name){
        if( !T50Reflection::hasConstant(__CLASS__, $name) )
            throw new RuntimeException("Invalid option name [{$name}].");
    }
}
