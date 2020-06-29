<?php

namespace Agregator\Order;
use rat\agregator\ProductPrice;
use rat\agregator\Basket as BasketOrm;
use rat\agregator\Product;
use Bitrix\Main\Entity;
use ORM\ORMInfo;
use T50GlobVars;

abstract class BasketCollection extends OrderBase
{
	protected $orderId;
	protected $basketId;
	protected $selfId;

	protected $collectionForUpdate;

	function getDefaultData(){return [];}

	function loadListByOrderId(int $orderId){
		$filter = ["UF_ORDER_ID" => $orderId];
		$order = ["ID" => "ASC"];
		return $this->getOrmClass()::getList(compact("filter", "order"))->FetchAll();
	}

	function create(){
		if( $this->orderId <= 0 || $this->basketId <= 0 )
			return false;

		$data = ["UF_ORDER_ID" => $this->orderId, "UF_BASKET_ID" => $this->basketId];
		foreach($this->getDefaultData() as $field => $value)
		    $data[$field] = $value;

		$result = $this->getOrmClass()::add($data)->isSuccess();
		return $result;
	}

	function delete(){
		$data = $this->getCurrent();
		if( $data === false )
			return false;

		if( !isset($data) )
			return true;

		if( $data["ID"] <= 0 )
			return false;

		return $this->getOrmClass()::delete($data["ID"])->isSuccess();
	}

	function getCurrent(){
		if( $this->orderId <= 0 )
			return false;

		$filter = ["UF_ORDER_ID" => $this->orderId];

		if( $this->basketId > 0 ){
			$filter["UF_BASKET_ID"] = $this->basketId;
		}elseif( $this->selfId > 0 ){
			$filter["ID"] = $this->selfId;
		} else {
			return false;
		}

		return $this->getOrmClass()::getRow(compact("filter"));
	}

	function init(int $orderId, int $basketId, int $selfId = 0){
		if( $orderId <= 0 || ($basketId <= 0 && $selfId <= 0) )
			return $this;

		$this->orderId = $orderId;
		$this->basketId = $basketId;
		$this->selfId = $selfId;
		return $this;
	}

	function setMutilpleUpdateMode(){
		$orderId = (int) $this->getCurrent()["UF_ORDER_ID"];
		if( $orderId <= 0 )
			return false;

		$claimsFilter = ["UF_ORDER_ID" => $orderId, "UF_CLAIM" => 1];
		$claimsBaskets = BasketOrm::clas()::getList(["filter" => $claimsFilter])->fetchAll();
		$claimsId = array_column($claimsBaskets, "ID");

		$currentFilter = ["UF_ORDER_ID" => $orderId, "!UF_BASKET_ID" => $claimsId];
		$currentData = $this->getOrmClass()::getList(["filter" => $currentFilter])->fetchAll();
		$this->collectionForUpdate = [];
		foreach($currentData as $item)
		    $this->collectionForUpdate[] = (new static)->init($orderId, (int) $item["UF_BASKET_ID"], (int) $item["ID"]);
	}

}




