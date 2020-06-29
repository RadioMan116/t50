<?php

namespace OrdersDetailComponent;

use Agregator\Manager\Option;
use T50Config;

class OrderView
{
	private $option;

	function __construct(){
		$this->option = new Option;
	}

	function update(){
		$config = T50Config::get("order_detail");
		if( isset($_POST["reset"]) ){
			$this->option->set(Option::ORDER_TABS, $config["viewDefault"]);
			return;
		}


		$data = $this->option->get(Option::ORDER_TABS, $config["viewDefault"]);
		$blocks = $data["BLOCKS"];
		$blocks = $newBlocks = array_combine(array_column($blocks, "code"), $blocks);

		$names = $config["tabNames"];
		$type = ( in_array($_POST["type"], ["ALL", "TABS"]) ? $_POST["type"] : $data["TYPE"] );
		$type = htmlspecialchars($type);

		$_POST["blocks"]["basket"] = "basket";
		if( count(array_unique($_POST["blocks"])) != count($names) )
			return;

		foreach($_POST["blocks"] as $code => $newCode){
		    if( !isset($names[$code]) )
		    	continue;

		    if( $code != $newCode )
		    	$newBlocks[$code] = $blocks[$newCode];

		    $newBlocks[$code]["notab"] = ( $_POST["ignore_tabs"][$code] == "Y" );
		}

		$newData = array("BLOCKS" => array_values($newBlocks), "TYPE" => $type);
		$this->option->set(Option::ORDER_TABS, $newData);
	}

	function getData(){
		$config = T50Config::get("order_detail");
		$data = $this->option->get(Option::ORDER_TABS, $config["viewDefault"]);
		$data["BLOCKS"] = array_chunk($data["BLOCKS"], 4);
		$data["NAMES"] = $config["tabNames"];
		return $data;
	}

	function blocksGrouped(){
		$config = T50Config::get("order_detail");
		$data = $this->option->get(Option::ORDER_TABS, $config["viewDefault"]);
		$blocks = $data["BLOCKS"];
		if( $data["TYPE"] == "ALL" )
			return ["NOTABS" => array_column($blocks, "title", "code")];

		$intabs = array_filter($blocks, function ($item){return !$item["notab"];});
		$notabs = array_filter($blocks, function ($item){return $item["notab"];});
		return [
			"INTABS" => array_column($intabs, "title", "code"),
			"NOTABS" => array_column($notabs, "title", "code"),
		];
	}
}
