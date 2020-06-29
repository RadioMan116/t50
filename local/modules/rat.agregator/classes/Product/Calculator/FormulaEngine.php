<?php
namespace Agregator\Product\Calculator;

use Agregator\Product\JSON\ProductShops;
use Agregator\Product\JSON\ProductMarket;
use Agregator\Product\JSON\ProductJSON;
use Agregator\Product\Calculator\HiddenSuppliers;
use Agregator\Product\Calculator\CalculatorFormula;
use Agregator\Product\Product;
use Agregator\Product\ProductShopsData;

class FormulaEngine
{
	private $formulaLoader;

	private $product;
	private $purchase;

	function __construct(){
		$this->formulaLoader = new FormulaLoader;
	}

	function setProduct(Product $product){
		$this->product = $product;
	}

	function execute($shopId, $city, $purchase){
		if( $purchase <= 0 )
			return;

		$this->purchase = $purchase;
		$this->product->ShopsData->setCity($city)->setShopId($shopId);
		$formula = $this->getFormula($this->product->ShopsData->getData(ProductShopsData::FORMULA));
		return $this->calcPrice($formula);
	}

	private function calcPrice($formula){
		if( !isset($formula) )
			return;

		if( is_array($formula) ){
			foreach($formula as $item){
				if( $this->purchase < $item->purchaseRange[0] )
					continue;

				if( $item->purchaseRange[1] > 0 && $this->purchase > $item->purchaseRange[1] )
					continue;

				return $this->calcPrice($item->data);
			}
			return ;
		}

		if( $formula->mode == FormulaLoader::MODE_RRC )
			$price = $this->calcPriceRrc($formula);

		if( $formula->mode == FormulaLoader::MODE_FREE )
			$price = $this->calcPriceFree($formula);

		$rrc = $this->product->ShopsData->getData(ProductShopsData::RRC);
		if( $formula->checkRrc && $price < $rrc )
			$price = $rrc;

		return $price;
	}

	private function calcPriceFree($formula){
		$price = $this->purchase;
		if( $formula->perc > 0 )
			$price += round($this->purchase * $formula->perc / 100);

		if( $formula->minCom > 0 && ($price - $this->purchase) < $formula->minCom )
			$price = $this->purchase + $formula->minCom;

		if( $formula->maxCom > 0 && ($price - $this->purchase) > $formula->maxCom )
			$price = $this->purchase + $formula->maxCom;

		return $price;
	}

	private function calcPriceRrc($formula){
		$price = $this->product->ShopsData->getData(ProductShopsData::RRC);
		if( $formula->useSupplSale && !empty($formula->suppliersSale) ){
			$minSuppliersSale = $this->getMinSuppliersSale($formula->suppliersSale);
			if( $minSuppliersSale > 0 )
				$price = $minSuppliersSale;
		}

		return $price;
	}

	private function getFormula($id){
		static $cacheFormulas = [];
		if( !isset($cacheFormulas[$id]) )
			$cacheFormulas[$id] = $this->formulaLoader->getById($id);

		return $cacheFormulas[$id];
	}

	private function getMinSuppliersSale($suppliersId){
		$Market = $this->product->Market;
		foreach($suppliersId as $supplierId){
			$Market->setSupplier($supplierId);
			$sale = $Market->getSale();
			if( $minSale === null || ( $sale > 0 && $minSale > $sale ) )
				$minSale = $sale;
		}

		return $minSale;
	}
}
