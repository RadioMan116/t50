<?php

namespace Console\Commands;

use Console\Input;
use Console\CommandHandler;
use Agregator\Sync\Sale\Sale;

class SyncSale extends CommandHandler
{
	protected $previewDescription = "Export prices and avails from t50 to site";
	protected $detailDescription = <<<CODE
<warning>Required variable <bold>shopCode</bold></warning>
Optional variable <bold>city</bold>, once value (e.g. 'MSK') or array (e.g. 'MSK~SPB')
Option -k for queue (return 'ok' or nothing)
CODE;

	public function execute(Input $input){
		$shopCode = $input->params["shopCode"];
		if( empty($shopCode) )
			$this->writeAndExit("<error>empty required variable \"shopCode\"</error>");

		$syncSale = new Sale($shopCode);
		$shop = $syncSale->getShop();

		$city = $this->getCityParam($input, $shop);

		if( $shop["PROPERTY_SYNCHRONIZED_VALUE"] != "Y" )
			$this->writeAndExit("<error>disable sync sale for shop {$shopCode}</error>");

		$synchronized = $syncSale->export($city);
		if( $input->options["k"] )
			die(( $synchronized ? "ok" : "" ));

		$tag = ( $synchronized ? "success" : "error" );
		$this->write("<{$tag}>sync sale {$shopCode} {$tag}</{$tag}>");
	}

	private function getCityParam(Input $input, $shop){
		$city = $input->params["city"];
		if( $city == null )
			$city = array_values($shop["PROPERTY_CITIES_VALUE"]);

		return $city;
	}
}