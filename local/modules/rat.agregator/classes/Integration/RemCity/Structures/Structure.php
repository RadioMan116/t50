<?php

namespace Agregator\Integration\RemCity\Structures;
use T50Text;

abstract class Structure
{
    protected $data = [];

    function __set($name, $value){
        $validNames = array_merge(static::$requiredFields, static::$optionalFields);
        if( !in_array($name, $validNames) )
            return;

        $prepareMethod = "prepare" . T50Text::camelCase($name);
        if( method_exists($this, $prepareMethod) )
            $value = $this->$prepareMethod($value);

        if( !$this->check($name, $value) )
            return;

        $this->data[$name] = $value;
    }

    abstract protected function check($name, $value): bool;

    function isValid(){
        foreach(static::$requiredFields as $field){
            if( empty($this->data[$field]) ){
                return false;
            }
        }
        return true;
    }

    function __get($name){
        return $this->data[$name];
    }

    function getData(){
        return $this->data;
    }
}