<?php

namespace Agregator\Sync\Market;
use Agregator\Product\Supplier;
use T50Date;
use T50GlobVars;

class SuppliersUpdater extends Updater
{
	protected $logger;
	protected $data = array();

	function update(){
		$supplier = new Supplier();
		$t50Suppliers = Supplier::elements()->props("MARKET_ID")->setIndex("PROPERTY_MARKET_ID_VALUE")->getListFetch();

		$allAdded = true;
		$countAdded = 0;
		foreach($this->data as $item){
			$updatedDate = T50Date::convertDate($item['upd_date'], "Y-m-d H:i", "d.m.Y H:i");

			if( isset($t50Suppliers[$item["id"]]) ){
				$this->updateDate($t50Suppliers[$item["id"]]["ID"], $updatedDate);
				continue;
			}

			$name = htmlspecialchars($item["name"], ENT_COMPAT | ENT_HTML401, 'cp1251');
			$code = strtolower($item["code"]);
			$city = strtoupper($item['city']);

			$newData = array(
				Supplier::F_NAME => $name,
				Supplier::F_CODE => $code,
				Supplier::F_CITY => $city,
				Supplier::F_MARKET_ID => $item["id"]
			);

			if( $updatedDate )
				$newData[Supplier::F_UPD_DATE] = $updatedDate;

			if( !$supplier->create($newData, $errors) ){
				$allAdded = false;
				if( is_array($errors) )
					$errors = implode(PHP_EOL, $errors);

				$this->logger->log("updateSuppliers cannot add supplier " . $item["id"] . PHP_EOL . $errors);
			} else {
				$countAdded ++;
			}
		}
		$this->logger->log("updateSuppliers count created {$countAdded} suppliers");
		$this->logger->log("updateSuppliers " . ( $allAdded ? "success" : "FAIL" ));

		if( $countAdded > 0 ){
			T50GlobVars::get("CACHE_SUPPLIERS", null, true); // reset cache
		}

		return $allAdded;
	}

	private function updateDate(int $id, $date){
		$supplier = new Supplier();
		if( $date )
			$supplier->setId($id)->update([Supplier::F_UPD_DATE => $date]);
	}
}