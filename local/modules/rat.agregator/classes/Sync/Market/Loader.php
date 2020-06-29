<?php

namespace Agregator\Sync\Market;

use Agregator\Sync\Curl;

class Loader
{
	protected $Market;
	protected $marketXmlGeneratorLink = "http://dev.market3.t50.su/cron/export_for_t50/";
	protected $marketXmlGeneratorCommand = "php54 /var/www/clients/client1/web3/web/cron/export_for_t50/index.php";

	function __construct(Market $Market){
		$this->Market = $Market;
	}

	protected function getXmlDist(){
		$dir = \T50FileSystem::initDir("/.logs/sync_files", "Market Loader cannot create dir");
		$filePath = $dir . "/market3.xml";
		return $filePath;
	}

	function load(){
		$filePath = $this->getXmlDist();
		@unlink($filePath);
		if( file_exists($filePath) )
			$this->Market->logger->exception("old xml not removed");

		if( !$this->generate() )
			$this->Market->logger->exception("failed generate xml in market");

		if( !file_exists($filePath) )
			$this->Market->logger->exception("xml not found in {$filePath}");

		$size = filesize($filePath);

		if( $size < 1000 )
			$this->Market->logger->exception("market xml too small (size {$size} bytes)");

		$this->Market->logger->log("loaded market xml (size " . \T50Text::formatBytes($size) . ")");

		return $filePath;
	}

	private function generate(){
		// use http link
		// return (new Curl)->retBool()->get($this->marketXmlGeneratorLink);

		// use shell command
		$result = trim(shell_exec($this->marketXmlGeneratorCommand));
		return ( $result == "ok" );
	}
}
?>