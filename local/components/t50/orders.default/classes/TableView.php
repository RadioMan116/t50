<?php

namespace OrdersDefaultComponent;

use Agregator\Components\BaseAjaxComponent;
use Agregator\Manager\Option;
use T50Config;
use Agregator\Components\Traits\ComponentData;

class TableView extends BaseAjaxComponent
{
	use ComponentData;

	private $option;

	function __construct(){
		parent::__construct();
		$this->option = new Option;
	}

	function update(){
		$valid = $this->prepare([
				"reset" => "null, boolean",
				"columns*" => "null, htmlspecialchars",
			])->validate([
				"reset" => "bool",
			]);

		if( !$valid )
			return;

		$map = T50Config::get("orders_list_columns");
		$defaultColumns = array_keys($map);

		if( $this->input->reset ){
			$this->option->set(Option::ORDERS_LIST_COLUMNS, $defaultColumns);
			return;
		}

		$newColumns = array_filter($this->input->columns);
		$newColumns = array_values(array_unique($newColumns));
		if( !isset($newColumns) || empty($newColumns) )
			return;

		$diff = array_diff($newColumns, $defaultColumns);
		if( !empty($diff) )
			return;

		$this->option->set(Option::ORDERS_LIST_COLUMNS, $newColumns);
	}

	function getData(){
		$MAP = T50Config::get("orders_list_columns");
		$defaultColumns = array_keys($MAP);
		$columns = $this->option->get(Option::ORDERS_LIST_COLUMNS, $defaultColumns);
		$COLUMNS = array_intersect($columns, $defaultColumns);
		return compact("COLUMNS", "MAP");
	}
}
