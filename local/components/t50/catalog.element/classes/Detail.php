<?php

namespace CatalogElementComponent;

use rat\agregator\Product;
use Agregator\Product\JSON\ProductMarket;
use T50GlobVars;
use Agregator\Components\Traits\ComponentDI;

abstract class Detail
{
	use ComponentDI;

	protected $comments;

	function setComments(array $comments){
		$this->comments = $comments;
	}

	protected abstract function prepare($data);
}