<?php
namespace Agregator\Sync\Catalog\Utils;

use rat\agregator\Product;
use Agregator\Sync\Sync;
use T50DB;

class UpdateFlags extends Sync
{

	function update(){
		$class = Product::clas();
		$data = $this->loadFlags();
		if( empty($data) )
			return false;

		$dbXmlMap = array(
			"UF_FLAG_BUILD_IN" => "built_in",
			"UF_FLAG_PROF" => "prof",
			"UF_FLAG_PROMO" => "promo",
			"UF_FLAG_NEW" => "new",
		);

		$unids = array_keys($data);
		$res = $class::getList(["filter" => ["ID" => $unids]]);

		$cnt = 0;
		T50DB::startTransaction();
		while( $result = $res->fetch() ){
		    $flags = $data[$result["ID"]];

		    $upd = array();
		    foreach($dbXmlMap as $dbCode => $xmlCode){
		    	if( $result[$dbCode] != $flags[$xmlCode] )
		    		$upd[$dbCode] = $flags[$xmlCode];
		    }

		    if( empty($upd) )
		    	continue;

		    if( !$class::update($result["ID"], $upd)->isSuccess() )
		    	return T50DB::rollback();

		    if( $cnt % 1000 == 0 ){
		    	T50DB::commit();
		    	T50DB::startTransaction();
		    }
		}

		$this->logger->log("update {$cnt} products for shop " . $this->shop["CODE"]);

		return T50DB::commit();
	}

	protected function loadFlags(){
		$urlGenerator = $this->getCustomUrl("/t50/tools/props.php");
		$urlXml = $this->getCustomUrl("/t50/tools/props.xml");
		if( !$this->curl->retBool()->get($urlGenerator) ){
			$this->logger->log("{$urlGenerator} not answer \"ok\"");
			return false;
		}

		$xml = simplexml_load_file($urlXml);
		if( !$xml ){
			$this->logger->log("simplexml_load_file({$url}) return false");
			return false;
		}

		$result = [];
		foreach($xml->item as $item){
			$unid = (int) $item->unid->__toString();

			if( $unid <= 0 )
				continue;

			$result[$unid] = [];
			foreach(["built_in", "prof", "promo", "new"] as $code){
			    $value = (int) $item->$code->__toString();
			    $value = ( $value == 1 ? 1 : 0 );
			    $result[$unid][$code] = $value;
			}
		}

		return $result;
	}
}