<?php

namespace Console\Commands;

use Console\Input;
use Console\CommandHandler;
use Agregator\Statistics\ProductForOrder;

class CalcProdstats extends CommandHandler
{
	protected $previewDescription = "Calc staistics for products";
	protected $detailDescription = <<<CODE
Calculate count orders for products
Calculate count claims for products
Save result to json
CODE;

	public function execute(Input $input){
		$productForOrder = new ProductForOrder();
		$success = $productForOrder->compile();
		$tag = ( $success ? "success" : "error" );
		$this->write("<{$tag}>{$tag}</{$tag}>");
	}
}