<?php

namespace Console\Commands;

use Console\Input;
use Console\CommandHandler;
use Agregator\Integration\RemCity\Synchronizer;

class SyncRemcity extends CommandHandler
{
	protected $previewDescription = "Load data from remcity.net";
	protected $detailDescription = <<<CODE
Synchronized data from remcity.net api with iblock remcity
CODE;

	public function execute(Input $input){
		$synchronizer = new Synchronizer();
		$success = $synchronizer->sync();
		$message = ( $success ? "remcity success synchronized" : "remcity synchronization FAILED" );
		$tag = ( $success ? "success" : "error" );
		$this->write("<{$tag}>{$message}</{$tag}>");
	}
}