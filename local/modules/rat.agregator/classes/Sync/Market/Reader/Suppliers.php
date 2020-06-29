<?php

namespace Agregator\Sync\Market\Reader;

class Suppliers extends Reader
{
	function __construct($path){
		parent::__construct($path, "supplierslist", "supplier");
	}

	function parseNodeToArray($domNode){
        $result = json_decode(json_encode($domNode), 1);
		return $result;
	}
}