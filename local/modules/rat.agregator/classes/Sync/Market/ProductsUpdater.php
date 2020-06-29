<?php

namespace Agregator\Sync\Market;
use Agregator\Product\Supplier;
use Agregator\Product\Product as ProductModel;
use rat\agregator\Product as ProductOrm;
use T50DB;
use T50Date;

class ProductsUpdater extends Updater
{
	private $counter = 0;

	protected function getSuppliers(){
		return Supplier::elements()->props("MARKET_ID")->setIndex("PROPERTY_MARKET_ID_VALUE")->getListFetch();
	}

	function update(){
		$allUpdated = true;
		for($i = 0, $limit = 50; ; $i++){
		    $ids = array_column(ProductOrm::clas()::getList([
		        "order" => ["ID" => "ASC"],
		        "select" => ["ID"],
		        "limit" => $limit,
		        "offset" => $i * $limit,
		    ])->fetchAll(), "ID");
		    if( empty($ids) )
		        break;

		    T50DB::startTransaction();
		    if( $this->_update($ids) ){
		    	T50DB::commit();
		    } else {
		    	T50DB::rollback();
		    	$this->logger->log("not update market for ids:\n" . implode(",", $ids));
		    	$allUpdated = false;
		    }
		}

		$this->logger->log("update {$this->counter} products");
		return $allUpdated;
	}

	protected function _update(array $ids){
		$products = ProductModel::getByFilter(["ID" => $ids]);
		$suppliers = $this->getSuppliers();

		$allUpdated = true;
		foreach($products as $product){
			$marketProduct = $this->data[$product->id];
			$product->Market->clearAllAutoAvail();
			foreach($marketProduct["suppliers"] as $marketSupplier){
				if( !isset($suppliers[$marketSupplier["id"]]) )
					continue;

				$t50SupplierId = $suppliers[$marketSupplier["id"]]["ID"];
				$product->Market->setSupplier($t50SupplierId);


				$purchase = (int) $marketSupplier["purchase"];
				$sale = (int) $marketSupplier["sale"];
				$avail = ( $marketSupplier["avail"] === "1" ? AVAIL_IN_STOCK : AVAIL_BY_REQUEST );
				$dateSupply = T50Date::convertDate($marketSupplier["date_supply"], "Y-m-d", "d.m.Y");
				if( $dateSupply )
					$product->Market->setDateSupply($dateSupply);

				$product->Market->setPurchaseAuto(( $purchase > 0 ? $purchase : null ));
				$product->Market->setSale(( $sale > 0 ? $sale : null ));
				$product->Market->setAvailAuto($avail);
			}

			if( $product->save() ){
				$this->counter ++;
			} else {
				$allUpdated = false;
			}
		}
		return $allUpdated;
	}
}