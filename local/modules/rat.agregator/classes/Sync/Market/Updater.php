<?php

namespace Agregator\Sync\Market;
use Agregator\Product\Supplier;


abstract class Updater
{
	protected $logger;
	protected $data = array();

	abstract function update();

	function setLogger(Logger $logger){
		$this->logger = $logger;
		return $this;
	}

	function setData(array $data){
		$this->data = $data;
		return $this;
	}
}