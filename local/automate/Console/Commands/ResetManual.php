<?php

namespace Console\Commands;

use Console\Input;
use Console\CommandHandler;
use Agregator\Product\Tasks\ResetManual as ResetManualHandler;

class ResetManual extends CommandHandler
{
	protected $previewDescription = "Reset manual data";
	protected $detailDescription = <<<CODE
Reset manual data where exist "reset date" and this less than or equal to the current date
Reset data for shops and suplliers: shop sale, shop avail, supplier purchase, supplier avail
CODE;

	public function execute(Input $input){
		$resetManual = new ResetManualHandler();
		$success = $resetManual->run();
		$status = ( $success ? "success" : "FAIL" );
		$tag = ( $success ? "success" : "error" );
		$this->write("<{$tag}>{$status} reset</{$tag}>");
	}
}