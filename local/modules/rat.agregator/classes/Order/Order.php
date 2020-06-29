<?php

namespace Agregator\Order;

use T50ArrayHelper;
use T50DB;
use T50Date;
use T50GlobVars;
use rat\agregator\Client as OrmClient;
use rat\agregator\Order as OrderOrm;
use rat\agregator\OrderProperty;
use Agregator\Manager\Manager;

class Order extends OrderBase
{
	use Traits\Flags;

	private $data;
	private $history;
	private $clientData;

	function __construct(){
		$this->history = new History;
	}

	function setClientData(array $data){
		$this->clientData = $data;
	}

	function create($shopCode, $city = "Moscow", int $remoteId = 0){
		$cityId = OrderOrm::getEnum("UF_CITY", false)[$city]["id"];
		if( $cityId <= 0 )
			return $this->logError("invalid city '{$city}'");

		if( is_numeric($shopCode) ){
			$shop = T50GlobVars::get("CACHE_SHOPS")[$shopCode];
			$shopCode = $shop["CODE"];
		} else {
			$shop = T50ArrayHelper::find(T50GlobVars::get("CACHE_SHOPS"), function ($item) use ($shopCode){
				return $item["CODE"] == $shopCode;
			});
		}
		if( $shop["ID"] <= 0 )
			return $this->logError("not found shop '{$shopCode}'");

		if( !Manager::hasAccessToShop($shop["ID"]) )
			return false;

		if( $remoteId < 0 )
			return $this->logError("invalid remote id {$remoteId}");

		$sourceId = OrderOrm::getEnum("UF_SOURCE", false)[( $remoteId > 0 ? "shop" : "phone" )]["id"];

		if( $remoteId > 0 && ($existsId = $this->getExistsByRemoteId($shop["ID"], $remoteId)) > 0 )
			return $this->logError("remoteId {$remoteId} already exist for shop {$shopCode}");

		$data = [
			"UF_SHOP" => $shop["ID"],
			"UF_CITY" => $cityId,
			"UF_REMOTE_ORDER" => $remoteId,
			"UF_MANAGER_ID" => $GLOBALS["USER"]->getId(),
			"UF_DATE_CREATE" => date("d.m.Y H:i:s"),
			"UF_STATUS" => OrderOrm::getEnum("UF_STATUS", false)["new"]["id"],
			"UF_SOURCE" => $sourceId,
		];

		$result = OrderOrm::clas()::add($data);

		if( !$result->isSuccess() )
		 	return $this->logError("cannot create order");

		$orderId = $result->getId();

		if( !$this->attachClient($orderId, (int) $cityId) )
			$this->logError("cannot attach client");

		return $orderId;
	}

	private function getExistsByRemoteId(int $shopId, int $remoteId){
		$existOrder = OrderOrm::clas()::getRow(["filter" => ["UF_SHOP" => $shopId, "UF_REMOTE_ORDER" => $remoteId]]);
		return (int) $existOrder["ID"];
	}

	private function attachClient(int $orderId, int $cityId){
		T50DB::startTransaction();
		$clientData = ( isset($this->clientData) ? $this->clientData : [] );
		if( empty($clientData["UF_CITY"]) && $cityId > 0 ){
			$clientData["UF_CITY"] = OrderOrm::getEnum("UF_CITY", true, true)[$cityId];
		}
		$result = OrmClient::clas()::add($clientData);
		if( !$result->isSuccess() )
			return T50DB::rollback();

		$clientId = $result->getId();
		OrderOrm::clas()::update($orderId, ["UF_CLIENT" => $clientId]);

		return T50DB::commit();
	}

	function init(int $orderId){
		$this->data = OrderOrm::clas()::getRowById($orderId);
		$this->history->init($orderId, $orderId);
		return $this;
	}

	private function update(array $updData){
		if( empty($this->data) )
			return $this->logError("order not initialized");

		return $this->updateWithHistory(
			$this->data["ID"], $this->data["ID"],
			$updData,
			$this->data
		);
	}

	function setStatus(int $statusId){
		$arrStatusIdCode = OrderOrm::getEnum("UF_STATUS");
		if( !isset($arrStatusIdCode[$statusId]) )
			return $this->logError("invalid status id {$statusId}");

		return $this->update(["UF_STATUS" => $statusId]);
	}

	function setIsTest(bool $isTest){
		if( !Manager::canSwitchTestOrder() )
			return false;
		return $this->update(["UF_TEST" => $isTest]);
	}

	function setSource(int $sourceId){
		$sourcesIdCode = OrderOrm::getEnum("UF_SOURCE");
		if( !isset($sourcesIdCode[$sourceId]) )
			return $this->logError("invalid source id {$sourceId}");

		return $this->update(["UF_SOURCE" => $sourceId]);
	}

	function setCity(int $cityId){
		$citiesIdTitle = OrderOrm::getEnum("UF_CITY", true, true);
		if( !isset($citiesIdTitle[$cityId]) )
			return $this->logError("invalid city id {$cityId}");

		T50DB::startTransaction();
		$clientId = (int) $this->data["UF_CLIENT"];
		if( !OrmClient::clas()::update($clientId, ["UF_CITY" => $citiesIdTitle[$cityId]])->isSuccess() ){
			T50DB::rollback();
			return $this->logError("invalid city id {$cityId}");
		}

		if( !$this->update(["UF_CITY" => $cityId]) )
			return T50DB::rollback();

		return T50DB::commit();
	}

	static function shareCommission(int $orderId, int $managerId){
		if( $orderId <= 0 )
			return false;

		if( $managerId == 0 )
			return OrderProperty::delete($orderId, "SHARE_COM_MANAGER");

		$managers = T50GlobVars::get("MANAGERS", "sales_managers");
		if( !isset($managers[$managerId]) )
			return false;

		$result = OrderProperty::set($orderId, "SHARE_COM_MANAGER", $managerId);
		return (bool) $result;
	}

	static function take(int $orderId){
		$order = OrderOrm::clas()::getRowById($orderId);
		if( empty($order) )
			return false;

		$statusIdCode = OrderOrm::getEnum("UF_STATUS");
		if( $statusIdCode[$order["UF_STATUS"]] != "new" )
			return false;

		if( $order["UF_MANAGER_ID"] > 0 )
			return false;

		if( !Manager::hasAccessToShop((int) $order["UF_SHOP"]) )
			return false;

		$userId = $GLOBALS["USER"]->getId();
		return OrderOrm::clas()::update($orderId, ["UF_MANAGER_ID" => $userId])->isSuccess();
	}

	static function setDateInvoice(int $orderId, $date){
		if( !T50Date::check($date, "d.m.Y") || $orderId < 0 )
			return false;

		$result = OrderProperty::set($orderId, "DATE_INVOICE", $date);
		return (bool) $result;
	}

	static function hasAccess(int $orderId){
		$order = OrderOrm::clas()::getRowById($orderId);
		$shopId = (int) $order["UF_SHOP"];
		if( $shopId <= 0 )
			return true;
		return Manager::hasAccessToShop($shopId);
	}
}