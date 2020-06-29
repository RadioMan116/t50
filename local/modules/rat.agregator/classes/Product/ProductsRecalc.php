<?php
namespace Agregator\Product;

use rat\agregator\Formula;
use rat\agregator\Product as ProductOrm;
use rat\agregator\ProductPrice;
use Agregator\Logger;
use T50GlobVars;
use T50Debug;
use T50DB;

class ProductsRecalc
{
	static function changeFormula(int $formulaId, array $pricesId){
		if( $formulaId <= 0 || empty($pricesId) || !\T50ArrayHelper::isInt($pricesId) )
			return false;

		if( Formula::clas()::getRowById($formulaId) == null )
			return false;

		$priceOrm = ProductPrice::clas();
		$unids = [];

		T50DB::startTransaction();

		$res = $priceOrm::getList(["filter" => ["ID" => $pricesId]]);
		while( $result = $res->Fetch() ){
			$unid = $result["UF_PRODUCT_ID"];
			if( isset($unids[$unid]) )
				continue;

			if( $result["UF_FORMULA"] == $formulaId )
				continue;

			if( !$priceOrm::update($result["ID"], ["UF_FORMULA" => $formulaId])->isSuccess() )
				return T50DB::rollback();

			$unids[$unid] = true;
		}

		return T50DB::commit() ;
	}

	static function recalcByUnids(array $unids){
		T50DB::startTransaction();
		$products = Product::getByFilter(["ID" => $unids]);
		foreach($products as $productObject){
		    if( !$productObject->save() ){
		    	return T50DB::rollback();
		    }
		}
		return T50DB::commit();
	}

	static function recalcByShop(int $shopId){
		if( $shopId <= 0 )
			return false;

		$logger = new Logger("product");

		T50Debug::time(1);

		$allIds = ProductOrm::clas()::getList(['select' => ["ID"], 'filter' => ["UF_SHOPS" => $shopId]])->fetchAll();
		$countAll = count($allIds);
		if( $countAll == 0 ){
			$logger->log("recalcByShop({$shopId}) not found products");
			return false;
		}

		$idsBlocks = array_chunk($allIds, 50);
		$countUpdated = 0;
		foreach($idsBlocks as $ids){
			$ids = array_column($ids, "ID");
			$products = Product::getByFilter(["ID" => $ids]);
			T50DB::startTransaction();
		    foreach($products as $product){
		    	if( !$product->save() ){
		    		$errorMsg = "FAILED Product::recalcByShop({$shopId}) updated {$countUpdated}/{$countAll}.";
		    		$errorMsg .= "\nFailed on product {$product->id}";
		    		T50Debug::alert($errorMsg);
		    		return T50DB::rollback();
		    	}
		    	$countUpdated ++;
		    }
		    T50DB::commit();
		}

		$logger->log("recalcByShop({$shopId}) updated {$countUpdated}/{$countAll}; " . T50Debug::time(0));

		return true;
	}

	static function deleteFormula(int $formulaId){
		if( $formulaId <= 0 )
			return false;

		$priceOrm = ProductPrice::clas();
		$unids = [];

		T50DB::startTransaction();

		$res = $priceOrm::getList(["filter" => ["UF_FORMULA" => $formulaId]]);
		while( $result = $res->Fetch() ){
			$unids[] = $result["UF_PRODUCT_ID"];
			if( !$priceOrm::update($result["ID"], ["UF_FORMULA" => false])->isSuccess() )
				return T50DB::rollback();
		}

		$unids = array_unique($unids);
		if( empty($unids) )
			return T50DB::rollback();

		if( !self::recalcByUnids($unids) )
			return T50DB::rollback();

		return T50DB::commit();
	}
}