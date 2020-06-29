<?php
namespace Agregator\Sync\Order;

use Agregator\Order\Order as OrderModel;
use Agregator\Order\Basket as BasketModel;
use rat\agregator\Product as ProductOrm;
use Agregator\Order\BasketProductInfo;
use Agregator\Sync\Sync;
use rat\agregator\ProductPrice;
use rat\agregator\Client;
use rat\agregator\Order as OrderOrm;
use T50ArrayHelper;

class Order extends Sync
{
	private $data;
	private $cityInfo;
	private $remoteId;

	function setData(array $data){
		// T50ArrayHelper::isEmpty($data["ORDER"]) ||
		// T50ArrayHelper::isEmpty($data["BASKET"]) ||
		if( is_array($data["ORDER"]) && !empty($data["ORDER"])
			&& is_array($data["BASKET"]) && !empty($data["BASKET"])
			){
			$this->data = $data;
			$this->detectCity();
		}
	}

	function setRemoteOrderId($remoteId){
		if( $remoteId > 0 )
			$this->remoteId = (int) $remoteId;
	}

	function import(){
		if( !isset($this->data) || !isset($this->remoteId) )
			return $this->stop("data not initialized");

		$orderData = $this->data["ORDER"];

		$order = new OrderModel;
		$order->setClientData($this->buildClientData($orderData));
		$orderId = (int) $order->create($this->shop["CODE"], $this->cityInfo["ORDER_CITY"], $this->remoteId);
		if( $orderId <= 0 )
			return $this->stop("Shop " . $this->shop["CODE"] . " cannot create order");

		$this->createBasket($orderId);
		return true;
	}

	private function buildClientData(array $orderData){
		$elevatorCodeInfo = Client::getEnum("UF_ELEVATOR", false);
		$elevator = 0;
		if( $orderData['ELEVATOR'] == "BIG" )
			$elevator = $elevatorCodeInfo["freight"]["id"];
		return [
			"UF_FIO" => $orderData['NAME'],
			"UF_PHONE" => $orderData['PHONE'],
			"UF_EMAIL" => $orderData['EMAIL'],
			"UF_STREET" => $orderData['ADDRESS'],
			"UF_ELEVATOR" => $elevator,
			"UF_FLOOR" => $orderData['FLOOR'],
			"UF_CITY" => $orderData['CITY'],
		];
	}

	private function createBasket(int $orderId){
		if( !isset($this->data) )
			return $this->stop("data not initialized");

		if( $orderId <= 0 )
			return $this->stop("Shop " . $this->shop["CODE"] . " cannot create order");

		$withUnids = $withoutUnids = $unids = array();
		$basketsId = array();
		foreach($this->data["BASKET"] as $item){
			$unid = (int) $item["unid"];
			if( $unid > 0 ){
				$unids[] = $unid;
				$withUnids[] = $item;
			} else {
				$withoutUnids[] = $item;
			}
		}

		$existsProducts = ProductOrm::clas()::getList(["filter" => ["ID" => $unids]])->fetchAll();
		$existsUnids = array_column($existsProducts, "ID");

		$basketNotFoundUnids = [];

		foreach($withUnids as $item){
			if( !in_array($item["unid"], $existsUnids) ){
				$basketNotFoundUnids[] = $item;
				continue;
			}

			$productInfo = new BasketProductInfo;
			$productInfo->setProductId($item["unid"]);
			$productInfo->setShop($this->shop["CODE"]);
			$productInfo->setCity($this->cityInfo["PRICE_CITY"]);
			$productInfo->setSale($item["price"]);
			$productInfo->setQuantity($item["quantity"]);

			$basket = new BasketModel();
			$baksetId = $basket->create($orderId, $productInfo);
			if( $baksetId > 0 ){
				$basketsId[] = $baksetId;
			} else {
				$message = "cannot add propduct for order {$orderId}:\n";
				$message .= var_export($productInfo->getData(), true);
				$this->logger->log($message);
			}
		}

		if( !empty($withoutUnids) ){
			$message = array("Shop " . $this->shop["CODE"] . " cannot put in basket items without unid");
			$message[] = var_export($withoutUnids, true);
			$this->logger->log($message);
		}

		if( !empty($basketNotFoundUnids) ){
			$message = array();
			$message[] = "Shop " . $this->shop["CODE"] . " cannot put in basket items with unknown unid";
			$message[] = var_export($basketNotFoundUnids, true);
			$this->logger->log($message);
		}

		return $basketsId;
	}

	private function detectCity(){
		$this->cityInfo = [
			"ORDER_CITY" => "Moscow",
			"PRICE_CITY" => "MSK",
		];

		$cities = OrderOrm::getEnum("UF_CITY", false);
		foreach($cities as $cityCode => $info){
		    if( strcasecmp($this->data["ORDER"]["CITY"], $info["val"]) == 0 ){
		    	$this->cityInfo["ORDER_CITY"] = $cityCode;
		    	break;
		    }
		}

		if( $this->cityInfo["ORDER_CITY"] == "St. Petersburg" )
			$this->cityInfo["PRICE_CITY"] = "SPB";
	}

	private function stop($message){
		$this->logger->log($message);
		return false;
	}
}