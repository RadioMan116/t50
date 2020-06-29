<?php
namespace Agregator\Product;

use T50GlobVars;
use T50ArrayHelper;
use Agregator\Logger;
use rat\agregator\ProductPrice;
use Agregator\Product\Calculator\ParserFormula;
use Bitrix\Main\Entity;
use rat\agregator\ProductComment;
use T50DB;

class Prices
{
	private $productId;
	private $shopId;
	private $cityId;
	private $cityCode;

	private $enumProps;
	private $data;
	private $formulas;
	private $shops;

	private $logger;

	private static $syncClass = \Agregator\Sync\Sale\Sale::class; // for tests

	function __construct(){
		$this->enumProps = T50GlobVars::get("HLPROPS")[ProductPrice::getTableName()];
		$this->formulas = T50GlobVars::get("FORMULAS");
		$this->shops = T50GlobVars::get("CACHE_SHOPS");
		$this->logger = new Logger("price");
	}

	function setProductId($productId){
		$this->data = array();
		$this->productId = (int) $productId;
		$this->shopId = $this->cityId = $this->cityCode = null;
		return $this;
	}

	function setShopId($shopId){
		$this->data = array();
		$this->shopId = (int) $shopId;
		return $this;
	}

	function setCity($city){
		$this->data = array();
		$this->cityId = (int) $this->enumProps["UF_CITY"][$city]["id"];
		if( $this->cityId > 0 )
			$this->cityCode = $city;
		return $this;
	}

	function setManualPrice(int $price, $comment = "", $date = null){
		T50DB::startTransaction();
		if( !ProductComment::setCommentForShopSale($this->productId, $this->shopId, $this->cityCode, $comment, $date) )
			return T50DB::rollback();

		$switchToAuto = (ProductComment::getLastOperation() == ProductComment::DELETE);
		$this->setPriceManual($price);
		$this->setPriceAutoMode($switchToAuto);
		if( $this->save() ){
			T50DB::commit();
			self::$syncClass::syncOnce($this->productId, $this->shopId);
			return true;
		}

		return T50DB::rollback();
	}

	function setManualAvail(int $avail, $comment = "", $date = null){
		T50DB::startTransaction();
		if( !ProductComment::setCommentForShopAvail($this->productId, $this->shopId, $this->cityCode, $comment, $date) )
			return T50DB::rollback();

		$switchToAuto = (ProductComment::getLastOperation() == ProductComment::DELETE);
		$this->setAvailManual($avail);
		$this->setAvailAutoMode($switchToAuto);
		if( $this->save() ){
			T50DB::commit();
			self::$syncClass::syncOnce($this->productId, $this->shopId);
			return true;
		}

		return T50DB::rollback();
	}

	function setPriceAuto($price){
		$this->data["UF_PRICE_SALE"] = (int) $price;
		return $this;
	}

	function setPriceManual($price){
		$this->data["UF_PRICE_SALE_M"] = (int) $price;
		return $this;
	}

	function setPriceAutoMode($auto = true){
		$this->data["UF_MANUAL_PRICE"] = ( $auto ? 0 : 1 );
		return $this;
	}


	function setAvailAuto($availCode){
		$this->data["UF_AVAIL"] = (int) $availCode;
		return $this;
	}


	function setAvailManual($availCode){
		$this->data["UF_AVAIL_M"] = (int) $availCode;
		return $this;
	}


	function setAvailAutoMode($auto = true){
		$this->data["UF_MANUAL_AVAIL"] = ( $auto ? 0 : 1 );
		return $this;
	}

	function setRrc($price){
		$this->data["UF_RRC"] = (int) $price;
		return $this;
	}

	function setPurchase($price){
		$this->data["UF_PRICE_PURCHASE"] = (int) $price;
		return $this;
	}

	function setFormula($formulaId){
		$this->data["UF_FORMULA"] = (int) $formulaId;
		return $this;
	}

	function save(){
		if( !$this->isValidParams() )
			return false;

		$filter = array(
			'UF_PRODUCT_ID' => $this->productId,
			'UF_SHOP' => $this->shopId,
			'UF_CITY' => $this->cityId,
		);

		$current = ProductPrice::clas()::getRow(['filter' => $filter]);

		if( $current == null ){
			$newData = $filter + $this->data;

			if( !isset($newData["UF_AVAIL"]) )
				$newData["UF_AVAIL"] = 3;

			if( !isset($newData["UF_AVAIL_M"]) )
				$newData["UF_AVAIL_M"] = 3;

			$result = ProductPrice::clas()::add($newData);
			if( $result->isSuccess() )
				return $result->getId();

			$this->logger->log("save/add fail for {$this->productId}:\n" . implode("\n", $result->getErrorMessages()));
			return false;
		}

		$needUpdate = false;
		foreach($this->data as $code => $newVal){
			if( $current[$code] != $newVal ){
				$needUpdate = true;
				break;
			}
		}

		if( $needUpdate ){
			$result = ProductPrice::clas()::update($current["ID"], $this->data);
			if( $result->isSuccess() )
				return $result->getId();

			$this->logger->log("save/update fail for {$this->productId}:\n" . implode("\n", $result->getErrorMessages()));
			return false;
		}

		return $current["ID"];
	}

	private function isValidParams(){
		if( $this->shopId <= 0 || $this->cityId <= 0 || $this->productId <= 0 ){
			$this->logger->log("not set main params shopId or city or productId");
			return false;
		}

		if( !isset($this->shops[$this->shopId]) ){
			$this->logger->log("unknow shopId {$this->shopId} for productid {$this->productId}");
			return false;
		}

		// if( empty($this->data) ){
		// 	$this->logger->log("empty data for productid {$this->productId}");
		// 	return false;
		// }

		foreach($this->data as $code => $val){
		    switch ($code) {
		    	case "UF_FORMULA":
		    		if( !isset($this->formulas[$val]) ){
		    			$this->logger->log("unknow formula [{$val}] for productid {$this->productId}");
		    			return false;
		    		}
		    	break;
		    	case "UF_AVAIL":
		    	case "UF_AVAIL_M":
		    		if( !in_array($val, range(1, 4)) ){
		    			$this->logger->log("not valid avail code [{$val}] for productid {$this->productId}");
		    			return false;
		    		}
		    	break;
		    	case "UF_PRICE_SALE_M":
		    	case "UF_RRC":
		    	case "UF_PRICE_SALE":
		    	case "UF_PRICE_PURCHASE":
		    		if( $val < 0 ){
		    			$this->logger->log("{$code} [{$val}] < 0 for productid {$this->productId}");
		    			return false;
		    		}
		    	break;
		    }
		}

		return true;
	}
}