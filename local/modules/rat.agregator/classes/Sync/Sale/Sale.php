<?php
namespace Agregator\Sync\Sale;

use Agregator\Common\ReportXml;
use T50ArrayHelper;
use T50FileSystem;
use rat\agregator\ProductPrice;

class Sale extends \Agregator\Sync\Sync
{
	function export(array $ids = array()){
		$this->logger->log("\n\n\n", false);

		if( !T50ArrayHelper::isInt($ids) )
			$ids = [];

		$manualUpdate = !empty($ids);
		$this->logger->log("start " . ( $manualUpdate ? "manual" : "auto" ) . " sync for shop " . $this->shop["CODE"]);

		$exportData = $this->load($ids);

		$tmpFilePath = T50FileSystem::createTmpFile();

		(new ReportXml())
			->setData($exportData)
			->setFormat(true)
			->setAttribute("shop", $this->shop["CODE"])
			->setFilePath($tmpFilePath)
			->export();

		// export
		$remoteFileName = ( $manualUpdate ? "import_sale_manual.xml" : "import_sale.xml" );
		$action = ( $manualUpdate ? "import_sale_manual" : "import_sale" );

		if( !$this->sendFilePost($remoteFileName, $tmpFilePath) )
			$this->logger->exception($this->shop["CODE"] . " refused {$remoteFileName}");

		// tell site to start import (from $remoteFileName)
		if( !$this->sendActionPost($action) )
			$this->logger->exception("sale export (action {$action}) fail " . $this->shop["CODE"] . PHP_EOL . $this->curl->getLastMessage() );


		$this->logger->log("shop " . $this->shop["CODE"] . " success export sale (action {$action})");

		return true;
	}

	protected function load(array $ids = []){
		$filter = ["UF_SHOP" => $this->shop["ID"]];
		if( !empty($ids) )
			$filter["UF_PRODUCT_ID"] = $ids;

		$select = array(
	        "UF_PRODUCT_ID",
	        "UF_CITY",
	        "UF_PRICE_PURCHASE",
	        "UF_PRICE_SALE",
	        "UF_PRICE_SALE_M",
	        "UF_MANUAL_PRICE",
	        "UF_AVAIL",
	        "UF_AVAIL_M",
	        "UF_MANUAL_AVAIL",
	    );

		$cities = ProductPrice::getEnum("UF_CITY");

		$products = array();

		$res = ProductPrice::clas()::getList(compact("select", "filter"));
		while( $result = $res->Fetch() ){
			$city = $cities[$result["UF_CITY"]];
			if( empty($city) )
				continue;

			$unid = $result["UF_PRODUCT_ID"];
			if( !isset($products[$unid]) ){
				$products[$unid] = array(
					"unid" => $unid,
					"prices" => array(),
					"commissions" => array(),
					"avails" => array(),
				);
			}

			$price = (int) $this->getPrice($result);
			$commission = $price - $result["UF_PRICE_PURCHASE"];
			if( $price > 0 ){
				$products[$unid]["prices"][$city] = $price;
				$products[$unid]["commissions"][$city] = $commission;
			}


			$avail = $this->getAvail($result);
			if( in_array($avail, [1,2,3,4]) )
				$products[$unid]["avails"][$city] = $avail;
		}
		$products = array_values($products);

		return $products;
	}

	private function getPrice($item){
		if( $item["UF_MANUAL_PRICE"] )
			return $item["UF_PRICE_SALE_M"];

		return $item["UF_PRICE_SALE"];
	}

	private function getAvail($item){
		if( $item["UF_MANUAL_AVAIL"] )
			return $item["UF_AVAIL_M"];

		return $item["UF_AVAIL"];
	}

	static function syncOnce(int $unid, $shops){
		if( $unid <= 0 || empty($shops) )
			return false;

		if( !is_array($shops) )
			$shops = array($shops);

		$result = true;
		foreach($shops as $shop){
			try {
		    	$obj = new self($shop);
		    	if( !$obj->export([$unid]) )
		    		$result = false;
		    } catch (\Exception $e) {
		    	$result = false;
		    }
		}

		return $result;
	}
}