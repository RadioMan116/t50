<?php

namespace Agregator\Components\Traits;


trait ComponentDI
{
    private $DIConteiner = [];

    protected function singleton($className){
        static $conteiner = [];
        if( isset($conteiner[$className]) )
            return $conteiner[$className];

        $validClassName = explode("\\", static::class)[0] . "\\" . str_replace("_", "\\", $className);
        $conteiner[$className] = new $validClassName($this->arParams);
        $conteiner[$className]->arParams = $this->arParams;
        return $conteiner[$className];
    }

    function __get($field){
        return $this->singleton($field);
    }

    protected function registerAutoload(){
        spl_autoload_register([$this, "autoload"]);
    }

    function autoload($className){
        if( substr($className, 0, strlen(static::class)) !== static::class )
            return;

        $componentClassesDir = $this->getClassesDir();

        if( !isset($componentClassesDir) )
            return;

        $classNameParts = explode("\\", $className);
        if( array_shift($classNameParts) != static::class )
            return;

        $classPath = $componentClassesDir . implode("/", $classNameParts) . ".php";
        require $classPath;
    }

    private function getClassesDir(){
        static $path = array();
        $name = $this->getName();
        if( !isset($name) )
            return null;

        if( isset($path[$name]) )
            return $path[$name];

        if( $this instanceOf \CBitrixComponent ){
            $path[$name] = $_SERVER["DOCUMENT_ROOT"] . $this->getPath() . "/classes/";
        } else {
            $path[$name] = $this->getPath() . "/classes/";
        }

        return $path[$name];
    }
}
