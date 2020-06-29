<?php

namespace Console\Commands;

use Console\Input;
use Console\CommandHandler;
use Agregator\Common\ReportXml;
use Agregator\Logger;

class SyncSaleQueue extends CommandHandler
{
	protected $previewDescription = "Single entry point for all shops sync:sale";
	protected $detailDescription = <<<CODE
Export prices and avails from t50 to site
CODE;

	private $xmlPath;
	private $logger;

	private $queueList = array(
		"bosch",
		"siemens",
		"electrolux",
		"faber",
		"asko",
		"blanco-1",
		"blanco-2",
	);

	private $autoLaunchNext = true;


	function __construct(){
		$this->logger = new Logger("sale_queue");
		\T50FileSystem::initDir("/.logs/sync_files", "CommonLogger cannot create dir /.logs/sync_files");
		$this->xmlPath = $_SERVER["DOCUMENT_ROOT"] . "/.logs/sync_files/sync_sale_queue.xml";
	}

	public function execute(Input $input){
		if( !$this->checkFile() )
			$this->generateNewFile();

		$this->getNext();
	}

	private function getNext(){
		$xml = simplexml_load_file($this->xmlPath);
		if( $xml->in_process == "Y" )
			return;

		if( !$this->existsOtherHourInXml($xml) )
			return;

		$hour = date("H");
		$xml->in_process = "Y";
		$xml->asXML($this->xmlPath);

		foreach($xml->shops->item as $item){
			$shopHour = (int) substr($item->time_start, 0, 2);
			if( $shopHour == $hour )
				continue;

			$item->time_start = date("H:i:s");

			$shopCode = escapeshellcmd($item->name);
			$result = shell_exec("php t50cli sync:sale -k --shopCode={$shopCode}");
			$result = ( $result == "ok" );

			$item->time_end = date("H:i:s");
			$item->status = ( $result ? "ok" : "FAIL" );

			break;
		}

		$xml->in_process = "N";
		$xml->asXML($this->xmlPath);

		if( $this->autoLaunchNext ){
			if( $this->existsOtherHourInXml($xml) )
				$this->execute(new Input());
		}
	}

	private function existsOtherHourInXml($xml){
		$hour = date("H");
		$found = $xml->xpath("shops/item[not(starts-with(time_start,'{$hour}:'))]");
		$shop = $found[0];
		return ( $shop != null );
	}

	private function generateNewFile(){
		$codes = array();
		foreach(\T50GlobVars::get("CACHE_SHOPS") as $shop)
			$codes[] = $shop["CODE"];

		$validShops = array_intersect($this->queueList, $codes);
		if( $validShops != $this->queueList ){
			$this->logger->log("check shops\n" .
				var_export($this->queueList, true) . " \n " .
				var_export($codes, true)
			);
		}

		$data = array_map(function($shop){
			return array(
				"name" => $shop,
				"time_start" => "00:00:00",
				"time_end" => "00:00:00",
				"status" => "ok",
			);
		}, $validShops);
		$data = array("in_process" => "N", "shops" => $data);

		(new ReportXml())->setFilePath($this->xmlPath)->setData($data)->export();
	}

	private function checkFile(){
		if( !file_exists($this->xmlPath) )
			return false;

		if( date("d", filemtime($this->xmlPath)) != date("d") )
			return false;

		return true;
	}
}