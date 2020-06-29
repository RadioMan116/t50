<?php

namespace Agregator\Statistics;

use Bitrix\Main\Entity;
use Bitrix\Main\DB\MysqliResult;
use ORM\ORMInfo;
use rat\agregator\Basket;
use rat\agregator\Order;
use rat\agregator\Product;
use Agregator\Logger;
use T50Reflection;
use T50DB;

class ProductForOrder
{
	const CNT_ORDERS = "CNT_ORDERS";
	const CNT_CLAIMS = "CNT_CLAIMS";

	function compile(){
		$ordersForProduct = $this->loadCountOrdersForProduct();
		if( !$this->saveData(self::CNT_ORDERS, $ordersForProduct) )
			return false;

		$claimsForProduct = $this->loadCountClaimsForProduct();
		if( !$this->saveData(self::CNT_CLAIMS, $claimsForProduct) )
			return false;

		return true;
	}

	protected function loadCountOrdersForProduct(){
		$res = Basket::clas()::getList([
			"select" => ["UF_PRODUCT_ID", "CNT"],
			"filter" => ["ORD.UF_TEST" => 0],
			"runtime" => [
				new Entity\ExpressionField("CNT", "count(distinct UF_ORDER_ID)"),
				new Entity\ReferenceField("ORD", Order::clas(), ['=this.UF_ORDER_ID' => 'ref.ID']),
			],
			"group" => ["UF_PRODUCT_ID"]
		]);

		return $this->convertSqlResultToArray($res);
	}

	protected function loadCountClaimsForProduct(){
		$res = Basket::clas()::getList([
			"select" => ["UF_PRODUCT_ID", "CNT"],
			"filter" => ["UF_CLAIM" => 1, "ORD.UF_TEST" => 0],
			"runtime" => [
				new Entity\ExpressionField("CNT", "count(UF_PRODUCT_ID)"),
				new Entity\ReferenceField("ORD", Order::clas(), ['=this.UF_ORDER_ID' => 'ref.ID']),
			],
			"group" => ["UF_PRODUCT_ID"]
		]);

		return $this->convertSqlResultToArray($res);
	}

	protected function convertSqlResultToArray(MysqliResult $res){
		$data = [];
		while( $result = $res->Fetch() )
			$data[$result["UF_PRODUCT_ID"]] = $result["CNT"];

		return $data;
	}

	protected function saveData($type, array $arUnidCount){
		if( !in_array($type, T50Reflection::getConstants(__CLASS__)) ){
			$this->log("invalid type {$type}");
			throw new \InvalidArgumentException("invalid type {$type}");
		}

		if( empty($arUnidCount) ){
			$this->log("empty data for save " . $type);
			return false;
		}

		$productClass = Product::clas();
		$res = $productClass::getList([
			"filter" => ["ID" => array_keys($arUnidCount)],
			"select" => ["ID", "UF_STATISTICS"],
		]);

		$cntUpdated = 0;
		T50DB::startTransaction();
		while( $result = $res->Fetch() ){
			$currentData = json_decode($result["UF_STATISTICS"], true);
			if( empty($currentData) )
				$currentData = [];

			$unid = $result["ID"];
			$count = $arUnidCount[$unid];
			if( $currentData[$type] == $count )
				continue;

			$currentData[$type] = $count;
			$json = json_encode($currentData);

			if( $productClass::update($unid, ["UF_STATISTICS" => $json])->isSuccess() ){
				$cntUpdated ++;
			} else {
				self::log("failed when update product {$unid} with json \"{$json}\"");
				return T50DB::rollback();
			}

			if( $cntUpdated % 20 == 0 ){
				T50DB::commit();
				T50DB::startTransaction();
			}
		}

		self::log("{$type} updated in {$cntUpdated} products");

		return T50DB::commit();
	}

	protected function log($message){
		static $logger;
		$logger = $logger ?? new Logger("Statistics");
		$logger->log($message);
	}
}