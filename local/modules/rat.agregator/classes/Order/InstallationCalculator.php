<?php

namespace Agregator\Order;

use rat\agregator\Installation as InstallationOrm;
use Agregator\Product\JSON\ProductMarket;
use Agregator\Order\InstallationPrices;

class InstallationCalculator
{
	private $data;
	private $flags;

	protected $instPrices;
	protected $service;

	function __construct(){
		$this->instPrices = new InstallationPrices;
	}

	function setData(array $data){
		$this->data = $data;
		$this->flags = InstallationOrm::getFlags($data["UF_FLAGS"]);

		if( $data["UF_PROVIDER"] > 0 && $data["UF_SERVICE_ID"] > 0 ){
			$providerCode = InstallationOrm::getEnum("UF_PROVIDER")[$data["UF_PROVIDER"]];
			$this->instPrices->setProvider($providerCode);
			$this->service = $this->instPrices->getServiceById($data["UF_SERVICE_ID"]);
		}
	}

	function getData(){
		if( !isset($this->data) )
			throw new \RuntimeException("InstallationCalculator not initialized");


		$data = [];
		$data["UF_PRICE_PURCHASE"] = $this->getPurchase();
		$data["UF_PRICE_SALE"] = $this->getSale();
		$this->data = array_merge($this->data, $data);

		$data["UF_COMMISSION"] = $this->calcCommission();

		return $data;
	}

	private function getPurchase(){
		$purchase = $this->data["UF_PRICE_PURCHASE"];
		if( $this->flags["manual_purchase"] )
			return $purchase;

		return $this->service["PROPERTY_PURCHASE_VALUE"] ?? $purchase;
	}

	private function getSale(){
		$sale = $this->data["UF_PRICE_SALE"];
		if( $this->flags["manual_sale"] )
			return $sale;

		return $this->service["PROPERTY_SALE_VALUE"] ?? $sale;
	}

	private function calcCommission(){
		$visitMaster = $this->data["UF_MASTER"] + $this->data["UF_MKAD_PRICE"] * $this->data["UF_MKAD_KM"];
		$commission = $visitMaster * ( $this->flags["US_VISIT_MASTER"] ? -1 : 1 );

		if( $this->flags["US_COSTS"] ){
			$commission -= $this->data["UF_PRICE_PURCHASE"];
		} else {
			$commission += ($this->data["UF_PRICE_SALE"] - $this->data["UF_PRICE_PURCHASE"]);
		}
		return $commission;
	}
}
