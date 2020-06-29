<?php

namespace Console\Commands;

use Console\Input;
use Console\CommandHandler;
use Agregator\Sync\Catalog\Catalog;

class SyncCatalog extends CommandHandler
{
	protected $previewDescription = "Synchronized unids between t50 and site";
	protected $detailDescription = <<<CODE
Load xml from site; save (or update) in t50; export unids on site
<warning>Required variable <bold>shopCode</bold></warning>
CODE;

	public function execute(Input $input){
		$shopCode = $input->params["shopCode"];
		if( empty($shopCode) )
			$this->writeAndExit("<error>empty required variable \"shopCode\"</error>");

		$catalog = new Catalog($shopCode);

		$synchronized = $catalog->sync();
		$status = ( $synchronized ? "success" : "FAIL" );
		$tag = ( $synchronized ? "success" : "error" );
		$this->write("<{$tag}>sync catalog {$shopCode} {$status}</{$tag}>");
	}
}