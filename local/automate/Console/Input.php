<?php

namespace Console;

class Input
{
	private $class;
	private $command;
	private $params = array();
	private $options = array();

	function __set($prop, $val){
		if( in_array($prop, $this->getVaildVars()) )
			$this->$prop = $val;
	}

	function __get($prop){
		if( in_array($prop, $this->getVaildVars()) )
			return $this->$prop;
	}

	private function getVaildVars(){
		return array("class", "command", "params", "options");
	}
}