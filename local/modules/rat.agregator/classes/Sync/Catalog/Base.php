<?php

namespace Agregator\Sync\Catalog;

abstract class Base
{
	protected $logger;
	protected $shop;
	protected $reporter;

	function __construct(Catalog $syncCatalog){
		$this->shop = $syncCatalog->getShop();
		$this->logger = $syncCatalog->logger;

		if( $this instanceof Validator )
			$this->reporter = new ValidatorReport($syncCatalog);

		if( $this instanceof Updater )
			$this->reporter = new UpdaterReport($syncCatalog);

		$this->initialize();
	}

	protected function initialize(){}
}