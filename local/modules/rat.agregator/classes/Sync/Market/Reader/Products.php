<?php

namespace Agregator\Sync\Market\Reader;

class Products extends Reader
{
	function __construct($path){
		parent::__construct($path, "goods", "good");
	}

	function parseNodeToArray($domNode){
        $result = json_decode(json_encode($domNode), 1);

		if( !empty($result["suppliers"]) ){
			$suppliers = $result["suppliers"]["supplier"];

			if( !isset($suppliers[0]) )
				$suppliers = array($suppliers);

			foreach($suppliers as $k => $supplier){
				foreach($supplier as $field => $value){
					if( is_array($value) )
						$supplier[$field] = null;
				}

				$suppliers[$k] = $supplier;
			}

			$result["suppliers"] = $suppliers;
		}

		return $result;
	}
}