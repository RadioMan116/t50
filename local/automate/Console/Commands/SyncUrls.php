<?php

namespace Console\Commands;

use Console\Input;
use Console\CommandHandler;
use Agregator\Sync\Catalog\Utils\UpdateUrls;

class SyncUrls extends CommandHandler
{
	protected $previewDescription = "Load url from site";
	protected $detailDescription = <<<CODE
Prepare standart catalog xml on site; update products url
<warning>Required variable <bold>shopCode</bold></warning>
CODE;

	public function execute(Input $input){
		$shopCode = $input->params["shopCode"];
		if( empty($shopCode) )
			$this->writeAndExit("<error>empty required variable \"shopCode\"</error>");

		$updateUrls = new UpdateUrls($shopCode);
		$success = $updateUrls->update();
		$status = ( $success ? "success" : "FAIL" );
		$tag = ( $success ? "success" : "error" );
		$this->write("<{$tag}>update urls for shop {$shopCode} {$status}</{$tag}>");
	}
}