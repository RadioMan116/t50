<?php

namespace Agregator\Integration\RemCity\Structures;

class Service extends Structure
{
    static $requiredFields = [
        "model_type_id", "service_price_id", "price"
    ];

    static $optionalFields = [
        "is_free", "comment",
    ];

    protected function prepareIsFree($value){
        return ( $value ? 1 : null );
    }

    protected function check($name, $value): bool{
        if( in_array($name, self::$requiredFields) && $value <= 0 )
            return false;

        return true;
    }

}