<?php

namespace OrdersClaimsComponent;

use T50GlobVars;
use T50ArrayHelper;

class InitialData
{
	function getData(){
		static $data;
		if( isset($data) )
			return $data;

		$data = array(
			"SHOPS" =>  $this->getDataShops(),
			"SUPPLIERS" =>  $this->getDataSuppliers(),
			"MANAGERS" =>  $this->getDataManagers(),
			"STATUS" =>  [
				"Y" => "Открыта",
				"N" => "Закрыта",
			],
		);

		return $data;
	}

	private function getDataShops(){
		return T50ArrayHelper::pluck(T50GlobVars::get("CACHE_SHOPS"), "NAME");
	}

	private function getDataSuppliers(){
		return T50ArrayHelper::pluck(T50GlobVars::get("CACHE_SUPPLIERS", "MSK"), "NAME");
	}

	private function getDataManagers(){
		return T50ArrayHelper::pluck(T50GlobVars::get("MANAGERS", "sales_managers"), "NAME");
	}
}