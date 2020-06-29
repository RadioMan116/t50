<?php
namespace Agregator\Product\Calculator;

use Agregator\Product\JSON\ProductShops;
use Agregator\Product\JSON\ProductMarket;
use Agregator\Product\Calculator\HiddenSuppliers;
use Agregator\Product\Calculator\CalculatorFormula;
use Agregator\Product\Product;
use Agregator\Product\Shop;

class Calculator
{
	const DEFAULT_MIN_COMMISSION = 1000;

	private $hiddenSuppliers;
	private $product;
	private $formulaEngine;

	function __construct(){
		$this->hiddenSuppliers = new HiddenSuppliers;
		$this->formulaEngine = new FormulaEngine;
	}

	function setProduct(Product $product){
		$this->product = $product;
		$this->hiddenSuppliers->setProduct($product);
		$this->formulaEngine->setProduct($product);
	}

	function calculate(){
		$arResult = array();
		foreach($this->product->arShopsId as $shopId){
			$arResult[$shopId] = array();
			$cities = Shop::getShopCities($shopId);
			foreach($cities as $city){
				$result = $this->calculateByShopCity($shopId, $city);
				$arResult[$shopId][$city] = $result;
			}
		}
		return $arResult;
	}

	private function calculateByShopCity($shopId, $city){
		$this->hiddenSuppliers->setShop($shopId)->setCity($city);
		list($avail, $suppliers) = $this->getAvailAndSuppliers($city);
		if( $this->product->discontinued && $avail != AVAIL_IN_STOCK )
			$avail = AVAIL_DISCONTINUED;

		if( empty($suppliers) ){
			$result = new CalculationResult;
			$result->setAvail($avail);
			return $result;
		}

		asort($suppliers);
		$supplier = key($suppliers);
		$purchase = current($suppliers);


		$price = $this->formulaEngine->execute($shopId, $city, $purchase);

		if( ($price - $purchase) < self::DEFAULT_MIN_COMMISSION )
			$price = $purchase + self::DEFAULT_MIN_COMMISSION;

		$result = new CalculationResult;
		$result->setAvail($avail);
		$result->setPrice($price);
		$result->setPurchase($purchase);
		$result->setSupplier($supplier);

		return $result;
	}

	private function getAvailAndSuppliers($city, $ignoreHiddenByPrice = true){
		$market = $this->product->Market;

		$hiddenByPrice = $this->hiddenSuppliers->getHiddenByPrice();
		$hiddenByAvail = $this->hiddenSuppliers->getHiddenByAvail();

		$validSuppliers = array_diff($market->getSuppliersId($city), $hiddenByAvail);
		if( $ignoreHiddenByPrice )
			$validSuppliers = array_diff($validSuppliers, $hiddenByPrice);

		$inStock = $onOrder = array();
		foreach($validSuppliers as $supplierId){
			$market->setSupplier($supplierId);
			$avail = (int) $market->getAvail();
			$purchase = $market->getPurchase();
			if( !isset($purchase) )
				continue;

			if( $avail === AVAIL_IN_STOCK )
				$inStock[$supplierId] = $purchase;

			if( $avail === AVAIL_BY_REQUEST )
				$onOrder[$supplierId] = $purchase;
		}

		if( !empty($inStock) )
			return [AVAIL_IN_STOCK, $inStock];

		if( $ignoreHiddenByPrice && !empty($onOrder) )
			return [AVAIL_BY_REQUEST, $onOrder];

		if( $ignoreHiddenByPrice )
			return $this->getAvailAndSuppliers($city, false);

		return [AVAIL_OUT_OF_STOCK, []];
	}
}
