<?php
namespace Agregator\Product\Calculator;

use Agregator\Product\Product;
use rat\agregator\HiddenSupplier;

class HiddenSuppliers
{
	private $product;
	private $shopId;
	private $city;
	private $data;

	function setProduct(Product $product){
		$this->product = $product;
		$this->loadData();
		return $this;
	}

	function setShop($shopId){
		if( in_array($shopId, $this->product->arShopsId) )
			$this->shopId = $shopId;
		return $this;
	}

	function setCity($city){
		if( in_array($city, ["MSK", "SPB"]) )
			$this->city = $city;
		return $this;
	}

	function getHiddenByPrice(){
		return $this->getHidden("UF_HIDDEN_BY_PRICE");
	}

	function getHiddenByAvail(){
		return $this->getHidden("UF_HIDDEN_BY_AVAIL");
	}

	private function getHidden($code){
		if( empty($this->shopId) || empty($this->city) )
			throw new \RuntimeException("not specified shopId or city");

		$result = array();
		foreach($this->data as $item){
			if( $item["UF_SHOP"] > 0 && $item["UF_SHOP"] != $this->shopId ){
				//echo "ignore UF_SHOP [{$item['UF_SHOP']}] [{$this->shopId}]\n";
				continue;
			}

			if( !empty($item["UF_REGION"]) && $item["UF_REGION"] != $this->city ){
				//echo "ignore UF_REGION [{$item['UF_REGION']}] [{$this->city}]\n";
				continue;
			}

			if( $item["UF_BRAND"] > 0 && $item["UF_BRAND"] != $this->product->brandId ){
				//echo "ignore UF_BRAND [{$item['UF_BRAND']}] [{$this->product->brandId}]\n";
				continue;
			}

			if( $item["UF_CATEGORY"] > 0 && !in_array($item["UF_CATEGORY"], $this->product->arCategoriesId) ){
				// echo "ignore UF_CATEGORY [{$item['UF_CATEGORY']}] [" . implode(", ", $this->product->arCategoriesId) . "]\n";
				continue;
			}

			//echo "valid shop " . $item["UF_SHOP"] . "\n";

			$result = array_merge($result, $item[$code]);
		}
		$result = array_unique($result);
		return $result;
	}

	private function loadData(){
		$filter = array('LOGIC' => 'OR');
		$filter[] = ["UF_SHOP" => $this->product->arShopsId];
		$filter[] = ["UF_SHOP" => 0];
		$filter[] = ["UF_BRAND" => $this->product->brandId];
		$filter[] = ["UF_BRAND" => 0];
		$filter[] = ["UF_CATEGORY" => $this->product->arCategoriesId];
		$filter[] = ["UF_CATEGORY" => 0];

		$select = ["UF_SHOP", "UF_BRAND", "UF_CATEGORY", "UF_REGION", "UF_HIDDEN_BY_PRICE", "UF_HIDDEN_BY_AVAIL"];
		$result = HiddenSupplier::clas()::getList(["filter" => $filter, "select" => $select]);
		$this->data = array();
		while( $row = $result->fetch() ){
			$row["UF_REGION"] = $this->getRegionCode($row["UF_REGION"]);
			$this->data[] = $row;
		}
	}

	private function getRegionCode($id){
		static $regions;
		if( isset($regions) )
			return $regions[$id];

		$regionProps = \T50GlobVars::get("HLPROPS")["t50_hidden_suppliers"]["UF_REGION"];
		$regions = array_map(function ($item){
			return $item["id"];
		}, $regionProps);

		$regions = array_flip($regions);
		return $regions[$id];
	}

}