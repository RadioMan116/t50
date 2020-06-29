<?php
namespace Agregator;

class Logger extends CommonLogger
{
	private $name;
	private $rotateCount;
	private $flushCount;

	function __construct($name){
		$this->name = $name;
		parent::__construct();
	}

	protected function getFileName(){
		return $this->name;
	}

	protected function getFolderName(){
		return $this->name;
	}

	protected function rotateCount(){
		return ( $this->rotateCount ? $this->rotateCount : 5 );
	}

	protected function flushCount(){
		return ( $this->flushCount ? $this->flushCount : 10 );
	}

	function setRotateCount($cnt){
		$this->rotateCount = $cnt;
	}

	function setFlushCount($cnt){
		$this->flushCount = $cnt;
	}
}
