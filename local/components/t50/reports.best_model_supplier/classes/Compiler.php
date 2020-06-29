<?php

namespace ReportsBestModelSupplierComponent;

use Agregator\Product\JSON\ProductMarket;

class Compiler
{
	use TraitData;

	private $usedSuppliers = array();

	private $arResult = array();

	function addRow(array $row){
		$result = $this->compile($row);
		if( $result )
			$this->arResult[] = $result;
	}

	private function compile(array $row){
		$marketData = new ProductMarket($row["UF_DATA_MARKET"]);
		$sale = (int) $row["SALE"];

		$suppliersId = $this->getValidSuppliersId($marketData);
		$compiledSuppliers = $this->compileSuppliers($marketData, $suppliersId);
		if( $compiledSuppliers->purches <= 0 )
			return false;

		unset($row["UF_DATA_MARKET"]);

		$row["CATEGORY"] = $this->initilData["CATEGORIES"][current($row["UF_CATEGORIES"])];
		$row["BRAND"] = $this->initilData["BRANDS"][$row["UF_BRAND"]];
		$row["SHOP"] = $this->initilData["SHOPS"][$row["PRODUCT_PR_UF_SHOP"]];
		$row["FORMULA"] = $this->initilData["FORMULAS"][$row["PRODUCT_PR_UF_FORMULA"]];
		$row["BEST_COMMISSION"] = $sale - $compiledSuppliers->purches;
		$row["PURCHES"] = $compiledSuppliers->purches;

		$suppliers = array();
		foreach($compiledSuppliers->arSupplierPurchases as $supplierId => $purches){
			$supplier = array(
				"purches" => $purches,
				"is_best" => ( $supplierId == $compiledSuppliers->bestSupplierId ),
			);

		    if( $this->input->cond_and_sale ){
				$cond = ( $sale > 0 ? ( $sale - $purches ) / $sale * 100 : 0 );
		    	$supplier["cond"] = round($cond, 2);

		    	$supplier["sale"] = $marketData->setSupplier($supplierId)->getSale();
		    }

		    $suppliers[$supplierId] = $supplier;
		    $this->usedSuppliers[$supplierId] = true;
		}

		$row["SUPPLIERS"] = $suppliers;
		return $row;
	}

	private function compileSuppliers(ProductMarket $marketData, array $suppliersId){
		$obj = new \StdClass;
		$obj->purches = 0;
		$obj->bestSupplierId = 0;
		$obj->arSupplierPurchases = [];

		$forSort = [];
		foreach($suppliersId as $supplierId){
		    $marketData->setSupplier($supplierId);
		    $avail = $marketData->getAvail();
		    $purchase = $marketData->getPurchase();
		    if( $purchase <= 0 )
		    	continue;

		    if( !isset($forSort[$avail]) )
		    	$forSort[$avail] = [];

		    $forSort[$avail][$supplierId] = $purchase;
		}

		$forSort = array_map(function ($items){
			asort($items);
			return $items;
		}, $forSort);

		if( $this->input->only_avail )
			unset($forSort[AVAIL_BY_REQUEST]);

		$obj->arSupplierPurchases = (array) $forSort[AVAIL_IN_STOCK] + (array) $forSort[AVAIL_BY_REQUEST];

		if( !empty($forSort[AVAIL_IN_STOCK]) ){
			$obj->bestSupplierId = key($forSort[AVAIL_IN_STOCK]);
		}
		else
		if( !empty($forSort[AVAIL_BY_REQUEST]) ){
			$obj->bestSupplierId = key($forSort[AVAIL_BY_REQUEST]);
		}

		if( $obj->bestSupplierId > 0 )
			$obj->purches = $obj->arSupplierPurchases[$obj->bestSupplierId];

		return $obj;
	}

	private function getValidSuppliersId(ProductMarket $marketData){
		$suppliersId = $marketData->getSuppliersId( $this->input->spb ? "SPB" : "MSK" );
		if( !empty($this->input->suppliers) )
			$suppliersId = array_intersect($suppliersId, $this->input->suppliers);

		return $suppliersId;
	}

	function getData(){
		return $this->arResult;
	}

	function getUsedSuppliers(){
		$suppliersId = array_keys($this->initilData["SUPPLIERS"]);
		$suppliersId = array_intersect($suppliersId, array_keys($this->usedSuppliers));
		return $suppliersId;
	}
}