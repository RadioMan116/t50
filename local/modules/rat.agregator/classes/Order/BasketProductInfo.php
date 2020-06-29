<?php

namespace Agregator\Order;
use rat\agregator\ProductPrice;
use rat\agregator\Product;
use Bitrix\Main\Entity;
use ORM\ORMInfo;
use T50GlobVars;

class BasketProductInfo
{
	private $priceId;
	private $productId;
	private $shopId;
	private $cityId;
	private $sale;
	private $quantity = 1;

	function setPriceId(int $priceId){
		if( $priceId > 0 )
			$this->priceId = $priceId;

		return $this;
	}

	function setProductId(int $productId){
		if( $productId > 0 )
			$this->productId = $productId;

		return $this;
	}

	function setShop($shopCode){
		$shopsCodeId = array_column(T50GlobVars::get("CACHE_SHOPS"), "ID", "CODE");
		if( isset($shopsCodeId[$shopCode]) )
			$this->shopId = $shopsCodeId[$shopCode];

		return $this;
	}

	function setCity($cityCode){
		$cityProp = ProductPrice::getEnum("UF_CITY", false);
		if( isset($cityProp[$cityCode]) )
			$this->cityId = $cityProp[$cityCode]["id"];

		return $this;
	}

	function setSale(int $sale){
		if( $sale >= 0 )
			$this->sale = $sale;

		return $this;
	}

	function setQuantity(int $quantity){
		if( $quantity > 0 )
			$this->quantity = $quantity;

		return $this;
	}

	function getData(){
		if( $this->priceId > 0 )
			return $this->getDataByPriceId($this->priceId);

		if( $this->productId <= 0 || $this->shopId <= 0 || $this->cityId <= 0 || !isset($this->sale) )
			return;

		$priceId = $this->getProductPriceId();
		if( $priceId <= 0 )
			return;

		$data = $this->getDataByPriceId($priceId);
		if( empty($data) )
			return;

		return $data;
	}

	private function getProductPriceId(){
		$data = ProductPrice::clas()::getRow(["filter" => [
			"UF_PRODUCT_ID" => $this->productId,
			"UF_SHOP" => $this->shopId,
			"UF_CITY" => $this->cityId,
		]]);
		return $data["ID"];
	}

	private function getDataByPriceId(int $productPriceId){
		// ORMInfo::sqlTracker("start");
		$data = ProductPrice::clas()::getRow([
			'select' => array(
				"ID", "UF_PRICE_SALE_M", "UF_MANUAL_PRICE", "UF_PRICE_PURCHASE", "UF_PRICE_SALE",
				"UF_PRODUCT_ID", "UF_SHOP",
				"PROD.UF_TITLE", "PROD.ID", "PROD.UF_CODE", "PROD.UF_CATEGORIES"
			),
			"runtime" => [
				new Entity\ReferenceField(
					"PROD",
					Product::clas(),
					['=this.UF_PRODUCT_ID' => 'ref.ID']
				)
			],
			"filter" => ["ID" => $productPriceId]
		]);
		// ORMInfo::sqlTracker("show");

		if( empty($data) )
			return;

		$sale = ( $data["UF_MANUAL_PRICE"] ? $data["UF_PRICE_SALE_M"] : $data["UF_PRICE_SALE"] );
		if( isset($this->sale) )
			$sale = $this->sale;

		$url = Product::buildUrl(
			$data["UF_SHOP"],
			$data["PRODUCT_PRICE_PROD_UF_CATEGORIES"][0],
			$data["PRODUCT_PRICE_PROD_UF_CODE"]
		);

		return [
			"PRODUCT_ID" => $data["PRODUCT_PRICE_PROD_ID"],
			"NAME" => $data["PRODUCT_PRICE_PROD_UF_TITLE"],
			"SALE" => $sale,
			"URL" => $url,
			"PURCHASE" => $data["UF_PRICE_PURCHASE"],
			"COMMISSION" => ( $sale - $data["UF_PRICE_PURCHASE"] ),
			"QUANTITY" => $this->quantity,
		];
	}

}




