<?php

namespace Agregator\Order\Traits;

use Agregator\Logger;

trait Log
{
	protected function log($message){
		static $logger;
		$logger = $logger ?? new Logger((new \ReflectionClass($this))->getShortName());
		$logger->log($message);
	}

	protected function logError($message){
		if( $this->orderId )
			$message = "order {$this->orderId}: " . $message;
		$this->log($message);
		return false;
	}
}