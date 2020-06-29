<?php
namespace Agregator\Product\Tasks;

use Agregator\Logger;
use rat\agregator\ProductComment;
use Agregator\Product\Prices;
use Agregator\Product\Product;
use T50DB;
use T50ArrayHelper as AH;
use T50Date;
use T50Debug;

class ResetManual
{
	private $logger;

	function __construct(){
		$this->logger = new Logger("ResetManual");
	}

	function run(){
		if( !$this->resetShopsData() ){
			T50Debug::alert("ResetManual cannot reset shops data");
			return false;
		}

		if( !$this->resetSuppliersData() ){
			T50Debug::alert("ResetManual cannot reset suppliers data");
			return false;
		}

		return true;
	}

	protected function resetShopsData(){
		$prices = new Prices;
		$commentClass = ProductComment::clas();
		$data = $this->loadManualDataForShops();

		T50DB::startTransaction();

		foreach($data as $unid => $unidData){
		    $prices->setProductId($unid);

		    foreach($unidData["avail_shop"] as $item){
		    	$savePrices = $prices->setShopId($item["UF_SHOP_ID"])->setCity($item["CITY"])
		    		->setAvailAutoMode(true)
		    		->save();

		    	if( !$savePrices ){
		    		$this->logger->log("cannot reset avail for unid {$unid}", var_export($item, true));
		    		return T50DB::rollback();
		    	}

		    	if( !$commentClass::delete($item["COMMENT_ID"])->isSuccess() ){
		    		$this->logger->log("cannot delete comment avail unid {$unid}", var_export($item, true));
		    		return T50DB::rollback();
		    	}
		    }

		    foreach($unidData["sale"] as $item){
		    	$savePrices = $prices->setShopId($item["UF_SHOP_ID"])->setCity($item["CITY"])
		    		->setPriceAutoMode(true)
		    		->save();

		    	if( !$savePrices ){
		    		$this->logger->log("cannot reset sale for unid {$unid}", var_export($item, true));
		    		return T50DB::rollback();
		    	}

		    	if( !$commentClass::delete($item["COMMENT_ID"])->isSuccess() ){
		    		$this->logger->log("cannot delete comment sale unid {$unid}", var_export($item, true));
		    		return T50DB::rollback();
		    	}
		    }
		}

		return T50DB::commit();
	}

	protected function resetSuppliersData(){
		$commentClass = ProductComment::clas();
		$data = $this->loadManualSuppliersData();

		T50DB::startTransaction();

		foreach($data as $unid => $unidData){
			$product = Product::getById($unid);

		    foreach($unidData["avail_supplier"] as $item){
				$product->Market->setSupplier($item["UF_SUPPLIER_ID"]);
				$product->Market->setAvailMode(true);
				if( !$product->save() ){
					$this->logger->log("avail cannot save product unid {$unid}", var_export($item, true));
		    		return T50DB::rollback();
				}

		    	if( !$commentClass::delete($item["COMMENT_ID"])->isSuccess() ){
		    		$this->logger->log("cannot delete comment avail unid {$unid}", var_export($item, true));
		    		return T50DB::rollback();
		    	}
		    }

		    foreach($unidData["purchase"] as $item){
		    	$product->Market->setSupplier($item["UF_SUPPLIER_ID"]);
				$product->Market->setPurchaseMode(true);
				if( !$product->save() ){
					$this->logger->log("avail cannot save product unid {$unid}", var_export($item, true));
		    		return T50DB::rollback();
				}

		    	if( !$commentClass::delete($item["COMMENT_ID"])->isSuccess() ){
		    		$this->logger->log("cannot delete comment purchase unid {$unid}", var_export($item, true));
		    		return T50DB::rollback();
		    	}
		    }
		}

		return T50DB::commit();
	}

	protected function loadManualSuppliersData(){
		$keys = ProductComment::getEnum("UF_KEY", false);
		$data = $this->loadManualData([$keys["avail_supplier"]['id'], $keys["purchase"]['id']], ["UF_SUPPLIER_ID"]);
		return $data;
	}

	private function loadManualDataForShops(){
		$keys = ProductComment::getEnum("UF_KEY", false);
		$data = $this->loadManualData([$keys["avail_shop"]['id'], $keys["sale"]['id']], ["UF_SHOP_ID", "CITY"]);
		return $data;
	}

	private function loadManualData(array $keys, array $fields){
		if( empty($keys) )
			throw new \RuntimeException("empty keys");

		$keysIdCode = ProductComment::getEnum("UF_KEY");
		$citiesIdCode = ProductComment::getEnum("UF_CITY");
		$filter = array("<=UF_DATE_RESET" => date("d.m.Y"), "UF_KEY" => $keys);
		$res = ProductComment::clas()::getList(compact("filter"));
		$arResult = [];
		while( $row = $res->Fetch() ){
			$unid = $row["UF_PRODUCT_ID"];
			$key = $keysIdCode[$row["UF_KEY"]];
			if( $unid <= 0 )
				continue;

			if( !empty($row["UF_CITY"]) )
				$row["CITY"] = $citiesIdCode[$row["UF_CITY"]];

			if( !isset($arResult[$unid][$key]) )
				$arResult[$unid][$key] = [];

			$data = AH::filterByKeys($row, $fields);
			$data["COMMENT_ID"] = $row["ID"];

			$arResult[$unid][$key][] = $data;
		}

		return $arResult;
	}

}