<?php

namespace Agregator\Sync;

use Agregator\IB\Elements;
use Agregator\Product\Shop;
use T50GlobVars;

abstract class Sync
{
	protected $shop;
	public $logger;
	public $curl;

	const SYNC_URL = "/t50/entry.php";
	const REMOTE_FILES_DIR = "/t50/store";

	function __construct($shopCode){
		$this->logger = new Logger($this);
		$this->curl = new Curl();
		$this->setShop($shopCode);
		$this->initializer();
	}

	protected function initializer(){}

	private function setShop($shopCode){
		if( is_numeric($shopCode) )
			$shopCode = T50GlobVars::get("CACHE_SHOPS")[$shopCode]["CODE"];

		$shop = Shop::elements()
			->select("CODE", "NAME")->props("HTTP_HOST", "CATEGORIES", "SYNCHRONIZED", "BRANDS", "CITIES")
			->filter(array("CODE" => $shopCode))->getOneFetch();

		if( empty($shop) )
			$this->logger->exception("not found shop by code {$shopCode}");

		if( empty($shop["PROPERTY_BRANDS_VALUE"]) )
			$this->logger->exception("not found brands in shop {$shopCode}");

		$shop["SYNC_URL"] = $shop["PROPERTY_HTTP_HOST_VALUE"] . self::SYNC_URL;
		if( ENV !== "PRODUCTION" )
			$shop["SYNC_URL"] = "";


		$shop["REMOTE_FILES_DIR"] = $shop["PROPERTY_HTTP_HOST_VALUE"] . self::REMOTE_FILES_DIR;
		$this->shop = $shop;
	}

	function getShop(){
		return $this->shop;
	}

	protected function getRemoteFilePath($fileName){
		return $this->shop["REMOTE_FILES_DIR"] . "/" . $fileName;
	}

	protected function getCustomUrl($path){
		return $shop["PROPERTY_HTTP_HOST_VALUE"] . $path;
	}

	protected function sendActionPost($action, array $post = array()){
		$post["T50_SYNC_ACTION"] = $action;
		$post["SHOP_CODE"] = $this->shop["CODE"];
		return $this->curl->retBool()->setPost($post)->get($this->shop["SYNC_URL"]);
	}

	protected function sendFilePost($fileName, $filePath, $post = array()){
		$post["T50_SYNC_ACTION"] = "upload_file";
		$post["file_new_name"] = $fileName;

		$hash = hash_file("sha256", $filePath);
		$hash .= hash("whirlpool", substr($hash, 0, 7));
		$post["file_checksum"] = $hash;
		if( \T50Debug::isWin() ){
			$post["file"] = "@" . $filePath;
		} else {
			$post["file"] = new \CURLFile($filePath);
		}
		return $this->curl->retBool()->setPost($post)->get($this->shop["SYNC_URL"]);
	}
}
?>