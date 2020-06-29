<?php
namespace Agregator\Product;

use Agregator\Product\JSON\ProductMarket;
use Agregator\Product\Calculator\Calculator;
use Agregator\Product\Calculator\CalculationResult;
use Agregator\Logger;
use rat\agregator\Product as ProductOrm;
use rat\agregator\Formula;
use rat\agregator\ProductPrice;
use rat\agregator\ProductComment;
use Bitrix\Main\Entity;
use T50GlobVars;
use T50Debug;
use T50DB;

class Product
{
	private static $Calculator;
	private static $Logger;
	private static $Prices;

	public $ShopsData;
	public $Market;

	private $id;
	private $arShopsId = array();
	private $arCategoriesId = array();
	private $brandId;
	private $discontinued;

	private function __construct(){
		if( isset(self::$Calculator) )
			return;

		self::$Calculator = new Calculator;
		self::$Logger = new Logger("product");
		self::$Prices = new Prices();
	}

	function save(){
		T50DB::startTransaction();

		if( $this->Market->hasErrors() ){
			self::$Logger->log("Error on save product {$this->id} Market error:"
				. implode(", ", $this->Market->getErrors()));

			return T50DB::rollback();
		}

		self::$Calculator->setProduct($this);

		try{
			$result = self::$Calculator->calculate();
		} catch (\Exception $e){
			self::$Logger->log("Error save product {$this->id}:" . $e->getMessage());
			return T50DB::rollback();
		}

		if( $this->Market->isChanged() ){
			$updMarket = ProductOrm::clas()::update($this->id, ["UF_DATA_MARKET" => $this->Market->toJson()]);
			if( !$updMarket->isSuccess() ){
				self::$Logger->log("Error save product {$this->id}:" . implode(PHP_EOL, $updMarket->getErrorMessages()));
				return T50DB::rollback();
			}
		}

		self::$Prices->setProductId($this->id);

		foreach($result as $shopId => $shopData){
			self::$Prices->setShopId($shopId);

			foreach($shopData as $city => $calculationResult){
				self::$Prices->setCity($city);

				self::$Prices->setPriceAuto($calculationResult->getData(CalculationResult::PRICE));
				self::$Prices->setAvailAuto($calculationResult->getData(CalculationResult::AVAIL));
				self::$Prices->setPurchase($calculationResult->getData(CalculationResult::PURCHASE));
				if( !self::$Prices->save() )
					return T50DB::rollback();
			}

		}

		return T50DB::commit();
	}

	function __get($field){
		return $this->$field;
	}

	static function getByFilter(array $filter = array()){
		$cityIdCode = self::getCityIdCode();

		$res = ProductOrm::clas()::getList([
			'select' => array(
				"ID", "UF_SHOPS", "UF_DISCONTINUED", "UF_DATA_MARKET", "UF_CATEGORIES", "UF_BRAND",
				"PR.*"
			),
			"runtime" => [
				new Entity\ReferenceField(
					"PR",
					ProductPrice::clas(),
					['=this.ID' => 'ref.UF_PRODUCT_ID']
				)
			],
			"filter" => $filter
		]);
		$products = array();
		while( $result = $res->Fetch() ){
			$id = $result["ID"];
			if( !isset($products[$id]) ){
				$product = new Product;
				$product->id = $id;
				$product->arCategoriesId = array_filter($result["UF_CATEGORIES"]);
				$product->brandId = (int) $result["UF_BRAND"];
				$product->arShopsId = array_filter($result["UF_SHOPS"]);
				$product->discontinued = (bool) $result["UF_DISCONTINUED"];
				$product->Market = new ProductMarket($result["UF_DATA_MARKET"]);
				$product->ShopsData = new ProductShopsData();
				$products[$id] = $product;
			}

			if( !isset($result["PRODUCT_PR_ID"]) )
				continue;

			$shopsData = $products[$id]->ShopsData;

			$shopsData->setShopId($result["PRODUCT_PR_UF_SHOP"]);
			$shopsData->setCity($cityIdCode[$result["PRODUCT_PR_UF_CITY"]]);
			$shopsData->setData([
				ProductShopsData::RRC => $result["PRODUCT_PR_UF_RRC"],
				ProductShopsData::FORMULA => $result["PRODUCT_PR_UF_FORMULA"],
				ProductShopsData::AVAIL_MANUAL_MODE => $result["PRODUCT_PR_UF_MANUAL_AVAIL"],
				ProductShopsData::PRICE_MANUAL_MODE => $result["PRODUCT_PR_UF_MANUAL_PRICE"],
			]);

			$products[$id]->ShopsData = $shopsData;
		}

		return $products;
	}

	static function getById(int $id){
		$result = self::getByFilter(["ID" => $id]);
		if( empty($result) )
			return  null;

		return $result[$id];
	}

	private static function getCityIdCode(){
		static $cityIdCode;
		if( !isset($cityIdCode) ){
			$cityIdCode = [];
			$propCityData = T50GlobVars::get("HLPROPS")[ProductPrice::getTablename()]["UF_CITY"];
			foreach($propCityData as $code => $item){
			    $cityIdCode[$item["id"]] = $code;
			}
		}
		return $cityIdCode;
	}

}