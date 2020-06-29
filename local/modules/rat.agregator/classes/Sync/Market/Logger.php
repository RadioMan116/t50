<?php
namespace Agregator\Sync\Market;

class Logger extends \Agregator\CommonLogger
{
	
	protected function getFileName(){		
		return "market";
	}
	
	protected function getFolderName(){
		return "sync/market";
	}
	
	protected function rotateCount(){
		return 5;
	}
	
	protected function flushCount(){
		return 10;
	}
	
	function exception($message){
		$this->log($message);
		throw new \RuntimeException($message);
	}
	
}	