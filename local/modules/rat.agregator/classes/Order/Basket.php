<?php

namespace Agregator\Order;

use rat\agregator\Basket as BasketOrm;
use rat\agregator\Delivery as DeliveryOrm;
use ORM\ORMInfo;
use T50GlobVars;
use Bitrix\Main\Entity;
use Agregator\Product\JSON\ProductMarket;
use rat\agregator\Product;
use T50DB;

class Basket extends OrderBase
{
	use Traits\AccountingMonth;
	use Traits\SupplierCommission;

	private $id;
	private $orderId;
	protected $history;

	protected $collectionForUpdate;

	function __construct(){
		$this->history = new History;
		$this->calculator = new BasketCalculator;
		$this->calculator->setHistory($this->history);
	}

	function create(int $orderId, BasketProductInfo $productInfo, int $exchangedId = 0){
		if( !Order::hasAccess($orderId) )
			return false;

		$productInfo = $productInfo->getData();
		if( empty($productInfo) )
			return false;

		$data = [
			"UF_NAME" => $productInfo["NAME"],
			"UF_COMMISSION" => $productInfo["COMMISSION"],
			"UF_START_PRICE_SALE" => $productInfo["SALE"],
			"UF_PRICE_SALE" => $productInfo["SALE"],
			"UF_PRICE_PURCHASE" => $productInfo["PURCHASE"],
			"UF_PRODUCT_ID" => $productInfo["PRODUCT_ID"],
			"UF_PRODUCT_URL" => $productInfo["URL"],
			"UF_CLAIM_EXCHANGE" => $exchangedId,
			"UF_QUANTITY" => $productInfo["QUANTITY"],
			"UF_ORDER_ID" => $orderId,
			"UF_SUPPLIER" => 0,
		];

		T50DB::startTransaction();

		$result = BasketOrm::clas()::add($data);
		if( !$result->isSuccess() )
			return T50DB::rollback();

		$basketId = $result->getId();

		if( !(new Delivery())->init($orderId, $basketId)->create() )
			return T50DB::rollback();

		if( !(new Account())->init($orderId, $basketId)->create() )
			return T50DB::rollback();

		if( !(new Installation())->init($orderId, $basketId)->create() )
			return T50DB::rollback();

		T50DB::commit();
		return $basketId;
	}

	function delete(){
		if( $this->orderId <= 0 || $this->id <= 0 )
			return false;

		$data = $this->getCurrent($this->orderId, $this->id);
		if( empty($data) )
			return false;

		T50DB::startTransaction();

		if( !(new Delivery())->init($this->orderId, $this->id)->delete() )
			return T50DB::rollback();

		if( !(new Account())->init($this->orderId, $this->id)->delete() )
			return T50DB::rollback();

		if( !(new Installation())->init($this->orderId, $this->id)->delete() )
			return T50DB::rollback();

		if( !BasketOrm::clas()::delete($data["ID"])->isSuccess() )
			return T50DB::rollback();

		$this->history->addSimpleComment("Удален товар [" . $data["UF_NAME"] . "]", $this->orderId, true)->save();

		return T50DB::commit();
	}

	function init(int $orderId, int $id){
		$this->orderId = $orderId;
		$this->id = $id;
		$this->history->init($orderId, $id);
		return $this;
	}

	private function update(array $updData, $comment = ""){
		if( isset($this->collectionForUpdate) && count($updData) == 1 && key($updData) == "UF_SUPPLIER" ){
			$collectionForUpdate = $this->collectionForUpdate;
			$this->collectionForUpdate = null;
			T50DB::startTransaction();
			foreach($collectionForUpdate as $installation){
			    if( !$installation->update($updData) ){
			    	return T50DB::rollback();
			    }
			}
			return T50DB::commit();
		}

		if( $this->orderId <= 0 || $this->id <= 0 )
			return false;

		$data = $this->getCurrent($this->orderId, $this->id);
		if( empty($data) )
			return false;

		foreach($updData as $code => $value){
			$oldValue = $data[$code];
			$this->history->addChanges(
				BasketOrm::getTableName(), $code,
				array($oldValue, $value), true,
				$comment
			);
			$data[$code] = $value;
		}

		$this->calculator->setData($data);
		$updData = array_merge($updData, $this->calculator->getData($data));

		T50DB::startTransaction();

		if( $code == "UF_CLAIM" && $value == false && !$this->deleteExchange() )
			return T50DB::rollback();

		$result = BasketOrm::clas()::update($data["ID"], $updData);
		if( $result->isSuccess() && $this->history->save())
			return T50DB::commit();

		return T50DB::rollback();
	}

	private function deleteExchange(){
		$exchange = BasketOrm::clas()::getRow(["filter" => [
			"UF_ORDER_ID" => $this->orderId,
			"UF_CLAIM_EXCHANGE" => $this->id,
		]]);
		if( !isset($exchange) )
			return true;

		$basket = (new Basket())->init($exchange["UF_ORDER_ID"], $exchange["ID"]);
		return $basket->delete();
	}

	function setSupplier(int $supplierId){
		$suppliers = T50GlobVars::get("CACHE_SUPPLIERS");
		if( !isset($suppliers[$supplierId]) )
			return false;

		return $this->update(["UF_SUPPLIER" => $supplierId]);
	}

	function setQuantity(int $quantity){
		if( $quantity < 1 )
			return false;

		return $this->update(["UF_QUANTITY" => $quantity]);
	}

	function setPaymentType(int $payTypeId){
		$payTypeIdCode = BasketOrm::getEnum("UF_PAYMENT_TYPE");
		if( !isset($payTypeIdCode[$payTypeId]) )
			return false;

		return $this->update(["UF_PAYMENT_TYPE" => $payTypeId]);
	}

	function setIsClaim(bool $flag){
		return $this->update(["UF_CLAIM" => ( $flag ? 1 : 0 )]);
	}

	function setManualSale(int $price, $comment){
		return $this->setManual("UF_PRICE_SALE", $price, $comment);
	}

	function setManualPurchase(int $price, $comment){
		return $this->setManual("UF_PRICE_PURCHASE", $price, $comment);
	}

	private function setManual($code, $price, $comment){
		if( empty($comment) )
			return false;

		if( $this->orderId <= 0 || $this->id <= 0 )
			return false;

		$data = $this->getCurrent($this->orderId, $this->id);
		if( empty($data) )
			return false;

		if( $price < 0 || $price == $data[$code] )
			return false;

		switch ($code) {
			case "UF_PRICE_SALE":
				$flagId = BasketOrm::getEnum("UF_FLAGS", false)["manual_sale"]["id"];
			break;
			case "UF_PRICE_PURCHASE":
				$flagId = BasketOrm::getEnum("UF_FLAGS", false)["manual_purchase"]["id"];
			break;
			default:
				return false;
		}

		$flags = $data["UF_FLAGS"] ?? [];
		$flags[] = $flagId;
		$flags = array_unique($flags);

		return $this->update([$code => $price, "UF_FLAGS" => $flags], $comment);
	}

	function getCurrent(int $orderId, int $id){
		return $this->getData($orderId, $id);
	}

	function getList(int $orderId){
		return $this->getData($orderId);
	}

	private function getData(int $orderId, int $id = 0){
		$filter = ["UF_ORDER_ID" => $orderId];
		if( $id > 0 )
			$filter["ID"] = $id;

		$res = BasketOrm::clas()::getList([
			"filter" => $filter,
			"select" => ["*", "PROD.*"],
			"order" => ["ID" => "ASC"],
			"runtime" => [
				new Entity\ReferenceField(
					"PROD", Product::clas(),['=this.UF_PRODUCT_ID' => 'ref.ID']
				)
			],
		]);

		if( $id > 0 )
			return $res->Fetch();

		return $res->fetchAll();
	}

	function setMutilpleUpdateMode(){
		if( $this->orderId <= 0 || $this->id <= 0 )
			return false;

		$notClaimsFilter = ["UF_ORDER_ID" => $this->orderId, "UF_CLAIM" => 0];
		$notClaimsBaskets = BasketOrm::clas()::getList(["filter" => $notClaimsFilter])->fetchAll();

		$this->collectionForUpdate = [];
		foreach($notClaimsBaskets as $item)
		    $this->collectionForUpdate[] = (new self)->init($this->orderId, $item["ID"]);
	}
}
