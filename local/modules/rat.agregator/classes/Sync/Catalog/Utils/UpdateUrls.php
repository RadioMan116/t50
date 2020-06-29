<?php
namespace Agregator\Sync\Catalog\Utils;

use rat\agregator\Product;
use Agregator\Product\ProductShopsAttachment;
use Agregator\Product\Prices;
use Agregator\Product\Shop;
use Agregator\Sync\Sync;
use T50DB;

class UpdateUrls extends Sync
{
	function update(){
		$data = $this->loadArUnidUrl();
		if( empty($data) )
			return false;

		$unids = array_keys($data);
		$res = Product::clas()::getList(["filter" => ["ID" => $unids]]);
		T50DB::startTransaction();
		while( $result = $res->fetch() ){
		    $newUrl = $data[$result["ID"]];
		    if( !ProductShopsAttachment::updateUrl($this->shop["ID"], $newUrl, $result) )
		    	return T50DB::rollback();
		}

		return T50DB::commit();
	}

	private function loadArUnidUrl(){
		$url = $this->getXmlUrl();
		$xml = simplexml_load_file($url);
		if( !$xml )
			$this->logger->exception("simplexml_load_file({$url}) return false");

		$result = [];
		foreach($xml->item as $item){
			$unid = (int) $item->unid->__toString();
			$url = $item->url->__toString();
			if( $unid <= 0 || empty($url) )
				continue;

		    $result[$unid] = $url;
		}

		return $result;
	}

	protected function getXmlUrl(){
		$this->logger->log("start load xml from " . $this->shop["CODE"]);

		// generate xml (on site)
		if( !$this->sendActionPost("catalog_export") )
			$this->logger->exception("catalog_export fail " . $this->shop["CODE"] . PHP_EOL . $this->curl->getLastMessage() );

		return $this->getRemoteFilePath("export_catalog.xml");
	}

}