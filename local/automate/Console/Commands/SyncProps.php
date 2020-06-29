<?php

namespace Console\Commands;

use Console\Input;
use Console\CommandHandler;
use Agregator\Sync\Catalog\Utils\UpdateFlags;

class SyncProps extends CommandHandler
{
	protected $previewDescription = "Load flags from site";
	protected $detailDescription = <<<CODE
Generate props.xml in site; update products flags
<warning>Required variable <bold>shopCode</bold></warning>
CODE;

	public function execute(Input $input){
		$shopCode = $input->params["shopCode"];
		if( empty($shopCode) )
			$this->writeAndExit("<error>empty required variable \"shopCode\"</error>");

		$updateFlags = new UpdateFlags($shopCode);
		$success = $updateFlags->update();
		$status = ( $success ? "success" : "FAIL" );
		$tag = ( $success ? "success" : "error" );
		$this->write("<{$tag}>update flags for shop {$shopCode} {$status}</{$tag}>");
	}
}