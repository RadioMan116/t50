<?php
namespace Agregator\Sync;

class Logger extends \Agregator\CommonLogger
{
	private $className;
	
	function __construct(Sync $syncObject){
		$this->className = strtolower(substr(strrchr(get_class($syncObject), "\\"), 1));
		parent::__construct();
	}
	
	protected function getFileName(){		
		return $this->className;
	}
	
	protected function getFolderName(){
		return "sync/{$this->className}";
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