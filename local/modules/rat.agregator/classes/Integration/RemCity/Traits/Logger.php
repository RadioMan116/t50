<?php

namespace Agregator\Integration\RemCity\Traits;

use Agregator\Logger as LoggerClass;

trait Logger
{
    private function log($message){
        static $logger;
        $logger = $logger ?? new LoggerClass("RemCity");

        $class = get_class($this);
        $classWithoutNS = str_replace("Agregator\\Integration\\RemCity\\", "", $class);
        if( is_array($message) )
        	$message = "\n" . implode("\n", $message);
        $logger->log($classWithoutNS . ": " . $message);
    }
}
