<?php

namespace Agregator\Order\Docs;

abstract class Generator
{
	protected $handler;

	function getHandler(){
		return $this->handler;
	}

	abstract function init(string $path);
	abstract function download(string $name);
}
