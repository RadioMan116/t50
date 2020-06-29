<?php
namespace Agregator\Sync\Catalog;

use rat\agregator\Product;
use Agregator\Product\ProductShopsAttachment;
use Agregator\Product\Prices;
use Agregator\Product\Shop;
use Agregator\Product\Brand;

class Updater extends Base
{
	private $productClass;
	private $Prices;
	private $mapBrandFormula = [];

	protected function initialize(){
		$this->productClass = Product::clas();
		$this->Prices = new Prices;
		$this->mapBrandFormula = Brand::getMapBrandFormula();
	}

	function update($data){
		$products = Product::getListIndexed([
			"filter" => ["UF_SHOPS" => $this->shop["ID"]],
			"select" => ["ID", "UF_BRAND", "UF_MODEL", "UF_SHOPS", "UF_REMOTE_IDS", "UF_REMOTE_URLS", "UF_CATEGORIES"],
		], "UF_MODEL");


		foreach($data as $item){
			if( $item["id"] <= 0 ){
				$item["error"] = "not valid remote id";
				$this->reporter->add(UpdaterReport::LOG_FAIL, $item);
				continue;
			}

			$t50Product = $products[$item["t50_model"]];
			$new = ( empty($t50Product) || $t50Product["UF_BRAND"] != $item["brand"] );

			if( $new ){
				$this->createProduct($item);
			} else {
				$this->updateProduct($t50Product, $item);
			}
		}

		$this->reporter->makeReport();

		if( $this->reporter->hasErrors() )
			return false;

		return true;
	}

	private function updateProduct($t50Product, $xmlItem){
		$updated = ProductShopsAttachment::attach($this->shop["ID"], $xmlItem, $t50Product);

		if( !$updated ){
			$xmlItem["error"] = "cannot add shop " . $this->shop["ID"] . " for product " . $t50Product["ID"];
			$this->reporter->add(UpdaterReport::LOG_FAIL, $xmlItem);
			return;
		}

		$this->addPrice($t50Product["ID"], $this->shop["ID"], $t50Product["UF_BRAND"]);

		$xmlItem["attach_shop"] = $this->shop["ID"] . " / " . $this->shop["CODE"];
		$this->reporter->add(UpdaterReport::LOG_UPDATE, $xmlItem);
	}

	private function createProduct($xmlItem){
		$name = trim($xmlItem["name"]);
		$codeParts = array($this->shop["ID"], $xmlItem["category"], $xmlItem["t50_model"], $xmlItem["brand"], $this->generateCode($name));
		$code = implode("_", $codeParts);

		$arFields = array(
			"UF_TITLE" => $name,
			"UF_CODE" => $code,
			"UF_SHOPS" => [$this->shop["ID"]],
		    "UF_CATEGORIES" => [$xmlItem["category"]],
		    "UF_BRAND" => $xmlItem["brand"],
		    "UF_MODEL" => $xmlItem["t50_model"],
		    "UF_MODEL_PRINT" => $xmlItem["model"],
		    "UF_REMOTE_IDS" => json_encode([$this->shop["ID"] => $xmlItem["id"]]),
		    "UF_REMOTE_URLS" => json_encode([$this->shop["ID"] => $xmlItem["url"]]),
		);

		$result = $this->productClass::add($arFields);

		if( $result->isSuccess() ){
			$xmlItem["t50_id"] = $result->getId();
			$this->reporter->add(UpdaterReport::LOG_NEW, $xmlItem);
			$this->addPrice($xmlItem["t50_id"], $this->shop["ID"], $xmlItem["brand"]);
		} else {
			$xmlItem["error"] = $error;
			$this->reporter->add(UpdaterReport::LOG_FAIL, $xmlItem);
		}
	}

	private function addPrice($unid, $shopId, $brandId){
		$formulaId = (int) $this->mapBrandFormula[$brandId];
		foreach(Shop::getShopCities($shopId) as $cite){
			$this->Prices->setProductId($unid)->setShopId($shopId)->setCity($cite);
			if( $formulaId > 0 )
				$this->Prices->setFormula($formulaId);
			$this->Prices->save();
		}
	}

	protected function generateCode($name){
		$code = \T50Text::translit($name);
		$code = strtolower($code);
		$code = preg_replace("#[^a-z0-9-_]#", "_", $code);
		return $code;
	}

}