<?php
namespace Agregator\Sync\Sale;

use rat\agregator\ProductPrice;
use rat\agregator\Formula;
use Agregator\Product\ProductsRecalc;
use Agregator\Sync\Sync;
use T50DB;

class UpdateMRC extends Sync
{
	protected $mrcFormulaId;
	protected $ProductPrice;
	protected $metrics = ["setMrc" => 0, "resetMrc" => 0, "processed_count" => 0];

	protected function initializer(){
		$this->detectFormulaMRC();
	}

	function update(){
		if( !$this->mrcFormulaId )
			$this->logger->exception("not defined mrc formula when update shop " . $this->shop["CODE"]);

		$data = $this->loadMrc();
		if( empty($data) ){
			$this->logger->log("empty data from shop " . $this->shop["CODE"]);
			return false;
		}

		$this->ProductPrice = ProductPrice::clas();
		$unidsBlocks = array_chunk(array_keys($data), 50);

		foreach($unidsBlocks as $unids){
			$res = $this->ProductPrice::getList(["filter" => ["UF_PRODUCT_ID" => $unids]]);
			T50DB::startTransaction();
			while( $row = $res->fetch() ){
				if( !$this->_update($data[$row["UF_PRODUCT_ID"]], $row) ){
					$this->saveMetrics("failed on product " . $row["UF_PRODUCT_ID"]);
					return T50DB::rollback();
				}
				$this->metrics["processed_count"] ++;
			}
			if( !ProductsRecalc::recalcByUnids($unids) )
				return T50DB::rollback();
			T50DB::commit();
		}


		$this->saveMetrics();
		return true;
	}

	private function saveMetrics($message = null){
		$data = [
			"shop " . $this->shop["CODE"],
			"metrics: " . var_export($this->metrics, true)
		];
		if( isset($message) )
			$data[] = $message;
		$this->logger->log($data);
	}

	protected function _update($mrcPrice, array $row){
		if( $mrcPrice )
		{
			if( $mrcPrice != $row["UF_RRC"] || $this->mrcFormulaId != $row["UF_FORMULA"] ){
				$updateData["UF_RRC"] = $mrcPrice;
				$updateData["UF_FORMULA"] = $this->mrcFormulaId;
				$this->metrics["setMrc"] ++;
			}
		}
		else
		{
			if( $this->mrcFormulaId == $row["UF_FORMULA"] ){
				$updateData["UF_RRC"] = 0;
				$updateData["UF_FORMULA"] = 0;
				$this->metrics["resetMrc"] ++;
			}
		}

		if( !isset($updateData) )
			return true;

		return $this->ProductPrice::update($row["ID"], $updateData)->isSuccess();
	}

	private function loadMrc(){
		$url = $this->getXmlUrl();
		$xml = simplexml_load_file($url);
		if( !$xml )
			$this->logger->exception("simplexml_load_file({$url}) return false");

		$result = [];
		foreach($xml->item as $item){
			$unid = (int) $item->unid->__toString();
			$isMrc = (bool) $item->is_mrc->__toString();
			$price = (int) $item->price->__toString();
			if( $unid <= 0 || $price <= 0 )
				continue;

		    $result[$unid] = ( $isMrc ? $price : false );
		}

		return $result;
	}

	protected function detectFormulaMRC(){
		$formula = Formula::clas()::getRow(["filter" => ["=UF_TITLE" => "МРЦ"]]);
		if( !empty($formula) )
			$this->mrcFormulaId = $formula["ID"];
	}

	protected function getXmlUrl(){
		$this->logger->log("start load xml from " . $this->shop["CODE"]);

		// generate xml (on site)
		if( !$this->sendActionPost("catalog_export") )
			$this->logger->exception("catalog_export fail " . $this->shop["CODE"] . PHP_EOL . $this->curl->getLastMessage() );

		return $this->getRemoteFilePath("export_catalog.xml");
	}

}