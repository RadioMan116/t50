<?php
namespace Agregator\Sync\Catalog;
use Agregator\IB\Elements;
use rat\agregator\Product;
use Agregator\Product\ProductsRecalc;
use Agregator\Product\ProductShopsAttachment;
use Agregator\Product\Shop;

class Catalog extends \Agregator\Sync\Sync
{

	protected $validator;
	protected $updater;

	function __construct($shopCode){
		parent::__construct($shopCode);
		$this->validator = new Validator($this);
		$this->updater = new Updater($this);
	}

	public function sync(){
		if( $this->shop["PROPERTY_SYNCHRONIZED_VALUE"] !== "Y" )
			return false;

		$this->logger->log("\n\n\nstart sync for shop " . $this->shop["CODE"], false);

		if( $this->import() )
			return $this->export();

		return false;
	}

	protected function export(){
		$this->logger->log("start export " . $this->shop["CODE"]);

		// compile data (JSON)
		$filter = ["UF_SHOPS" => $this->shop["ID"]];
		$select = ["UF_SHOPS", "ID", "UF_REMOTE_IDS", "UF_REMOTE_URLS"];
		$res = Product::clas()::getList(compact("filter", "select"));

		$data = array();
		while( $item = $res->Fetch() ){
			$arShopIdRemoteId = ProductShopsAttachment::getRemoteIds($item);
			$id = $arShopIdRemoteId[$this->shop["ID"]];
			if( $id > 0 )
				$data[$id] = $item["ID"];
		}

		$json = json_encode($data);

		// create file import_catalog.json ($json) and send to site
		$tmpFilePath = \T50FileSystem::createTmpFile($tmpLink, $json);
		if( !$this->sendFilePost("import_catalog.json", $tmpFilePath) )
			$this->logger->exception($this->shop["CODE"] . " refused import_catalog.json");

		// trigger for site to start import (from import_catalog.json)
		if( !$this->sendActionPost("catalog_import") )
			$this->logger->exception("catalog_import fail " . $this->shop["CODE"] . PHP_EOL . $this->curl->getLastMessage() );

		$this->logger->log("shop " . $this->shop["CODE"] . " success import");

		return true;
	}

	protected function import(){
		$this->logger->log("start import " . $this->shop["CODE"]);

		// load xml
		$localXmlFile = $this->loadXml();
		if( !$localXmlFile )
			$this->logger->exception("cannot load or save catalog xml from shop " . $this->shop["CODE"]);

		// validate
		$valid = $this->validator->validate($localXmlFile);
		$this->logger->log("Validation " . $this->shop["CODE"] . " " . ( $valid ? "ok" : "FAIL" ));
		@unlink($localXmlFile);

		//$shop = new Shop($this->shop);
		//$shop->setIsSync($valid);

		// update
		$updated = false;
		if( $valid ){
			$data = $this->validator->getData();
			$updated = $this->updater->update($data);
			$this->logger->log("Update " . $this->shop["CODE"] . " " . ( $updated ? "ok" : "FAIL" ));
			if( $updated ){
				$successRecalc = ProductsRecalc::recalcByShop(intval($this->shop["CODE"]));
				$this->logger->log("Recalc " . $this->shop["CODE"] . " " . ( $successRecalc ? "ok" : "FAIL" ));
			}
		}
		return $updated;
	}

	private function loadXml(){
		$this->logger->log("start load xml from " . $this->shop["CODE"]);

		// tmp dir for load xml
		$tmpDir = \T50FileSystem::initDir("/.logs/TEMP", "CommonLogger cannot create dir TEMP");

		// generate xml (on site)
		if( !$this->sendActionPost("catalog_export") )
			$this->logger->exception("catalog_export fail " . $this->shop["CODE"] . PHP_EOL . $this->curl->getLastMessage() );

		// load xml from site
		$remoteXmlFile = $this->getRemoteFilePath("export_catalog.xml");
		$localTmpFile = tempnam($tmpDir, "CATALOG_SYNC_" . $this->shop["CODE"] . "_");
		return $this->curl->setFile($localTmpFile)->get($remoteXmlFile);
	}
}