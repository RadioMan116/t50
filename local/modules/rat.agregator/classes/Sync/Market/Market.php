<?php

namespace Agregator\Sync\Market;
use Agregator\Product\Supplier;


class Market
{
	private $localXmlPath;
	public $logger;

	function __construct(){
		$this->logger = new Logger();
	}

	function update(){
		$filePath = $this->load();

		if( !$filePath )
			return false;
		$this->localXmlPath = $filePath;

		if( !$this->updateSuppliers() )
			return false;

		if( !$this->updateProducts() )
			return false;

		return true;
	}

	private function load(){
		$loader = new Loader($this);
		return $loader->load();
	}

	private function updateSuppliers(){
		$marketSuppliers = [];
		$iterator = new Reader\Suppliers($this->localXmlPath);
		foreach($iterator as $item)
		    $marketSuppliers[] = $item;

		$updater = new SuppliersUpdater();
		$updater->setLogger($this->logger)->setData($marketSuppliers);
		return $updater->update();
	}

	private function updateProducts(){
		$marketProducts = [];
		$iterator = new Reader\Products($this->localXmlPath);
		foreach($iterator as $item)
		    $marketProducts[$item["uid"]] = $item;

		$updater = new ProductsUpdater();
		$updater->setLogger($this->logger)->setData($marketProducts);
		return $updater->update();
	}
}