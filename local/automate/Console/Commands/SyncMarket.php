<?php

namespace Console\Commands;

use Console\Input;
use Console\CommandHandler;
use Agregator\Sync\Market\Market;

class SyncMarket extends CommandHandler
{
	protected $previewDescription = "load data from market3";
	protected $detailDescription = <<<CODE
Synchronized prices and avails between t50 and market3
CODE;

	public function execute(Input $input){
		$market = new Market();
		$updated = $market->update();
		$status = ( $updated ? "success" : "FAIL" );
		$tag = ( $updated ? "success" : "error" );
		$this->write("<{$tag}>sync market {$status}</{$tag}>");
	}
}