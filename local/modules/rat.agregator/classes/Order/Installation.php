<?php

namespace Agregator\Order;

use Agregator\IB\Elements;
use Agregator\Integration\RemCity\Order as RemCityOrder;
use T50ArrayHelper;
use T50GlobVars;
use T50DB;

use rat\agregator\Installation as InstallationOrm;

class Installation extends BasketCollection
{
	use Traits\Flags;
	use Traits\AccountingMonth;
	use Traits\SupplierCommission;

	protected $history;
	protected $calculator;

	function __construct(){
		$this->history = new History;
		$this->installationPrices = new InstallationPrices;
		$this->calculator = new InstallationCalculator;
	}

	function getDefaultData(){
		return [
			"UF_MKAD_PRICE" => 30,
			"UF_TYPE" => InstallationOrm::getEnum("UF_TYPE", false)["installation"]["id"],
		];
	}

	function setService(string $provider, int $seriveId){
		if( !$this->canChangeForProvider($provider) )
			return false;

		$arCodeInfo = InstallationOrm::getEnum("UF_PROVIDER", false);
		$providerId = $arCodeInfo[$provider]["id"];
		if( !isset($providerId) )
			return $this->logError("not valid provider {$provider}");

		$service = $this->installationPrices->setProvider($provider)->getServiceById($seriveId);
		if( !isset($service) )
			return $this->logError("not found service id {$seriveId} for provider {$provider}");

		return $this->update(["UF_SERVICE_ID" => $seriveId, "UF_PROVIDER" => $providerId]);
	}

	function setDate($date){
		if( empty($date) )
			return $this->update(["UF_DATE" => ""]);

		if( !\T50Date::check($date) )
			return $this->logError("not valid date {$date}");

		return $this->update(["UF_DATE" => $date]);
	}

	function setMasterPrice(int $price){
		if( $price < 0 )
			return false;
		return $this->update(["UF_MASTER" => $price]);
	}

	function setMKADDistantion(int $dist){
		if( $dist < 0 )
			return false;
		return $this->update(["UF_MKAD_KM" => $dist]);
	}

	function setMKADDPrice(int $price){
		if( $price < 0 )
			return false;
		return $this->update(["UF_MKAD_PRICE" => $price]);
	}

	function setComment($comment){
		return $this->update(["UF_COMMENT" => $comment]);
	}

	function setSale(int $price){
		return $this->setPrice($price, "UF_PRICE_SALE", "manual_sale");
	}

	function setPurchase(int $price){
		return $this->setPrice($price, "UF_PRICE_PURCHASE", "manual_purchase");
	}

	private function setPrice(int $price, $code, $manualFlagCode){
		if( $price < 0 )
			return false;

		$flag = InstallationOrm::getEnum("UF_FLAGS", false)[$manualFlagCode]["id"];
		return $this->update([$code => $price], ["manual_flag" => $flag]);
	}

	function setType(string $code){
		$arCodeInfo = InstallationOrm::getEnum("UF_TYPE", false);
		if( !isset($arCodeInfo[$code]) )
			return $this->logError("not valid type {$code}");

    	return $this->update(["UF_TYPE" => $arCodeInfo[$code]["id"]]);
	}

	function update(array $updData, $options = []){
		if( isset($this->collectionForUpdate) ){
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

		$data = $this->getCurrent();
		if( empty($data) )
			return false;

		if( !$this->canChangeForProvider($data["UF_PROVIDER"]) )
			return false;

		$history = new History;
		$history->init($data["UF_ORDER_ID"], $data["ID"]);
		foreach($updData as $code => $value){
			$oldValue = $data[$code];
			if( $code == "UF_FLAGS" ){
				if( is_bool(current($value)) )
					$value = $this->getNewFlags(InstallationOrm::class, $oldValue, $value);
				$history->addChangesWithFlags(InstallationOrm::class, $oldValue, $value, true);
			} else {
				$history->addChanges(InstallationOrm::getTableName(), $code, array($oldValue, $value), true);
			}
			$updData[$code] = $data[$code] = $value;
		}

		if( $options["manual_flag"] > 0 ){
			$flags = $data["UF_FLAGS"] ?? [];
			$flags[] = $options["manual_flag"];
			$updData["UF_FLAGS"] = $data["UF_FLAGS"] = array_unique($flags);
		}

		$this->calculator->setData(array_merge($data, $updData));
		$updData = array_merge($updData, $this->calculator->getData());

		T50DB::startTransaction();
		$result = InstallationOrm::clas()::update($data["ID"], $updData);
		if( $result->isSuccess() && $history->save())
			return T50DB::commit();

		return T50DB::rollback();
	}

	function customCreate($productName, string $serviceProvider, int $serviceId, int $orderId){
		if( $orderId <= 0 || empty($productName) )
			return false;

		if( !$this->canChangeForProvider($serviceProvider, $orderId) )
			return false;

		$installationPrices = new InstallationPrices;
		$service = $installationPrices->setProvider($serviceProvider)->getServiceById($serviceId);
		$providerId = InstallationOrm::getEnum("UF_PROVIDER", false)[$serviceProvider]["id"];

		if( !isset($providerId) )
			return false;

		if( !isset($service) )
			return false;

		if( empty($productName) )
			return false;

		$data = [
			"UF_ORDER_ID" => $orderId,
			"UF_PRODUCT_NAME" => $productName,
			"UF_SERVICE_ID" => $serviceId,
			"UF_PROVIDER" => $providerId,
		];
		$this->calculator->setData($data);
		$data = array_merge($data, $this->getDefaultData(), $this->calculator->getData());

		return $this->getOrmClass()::add($data)->isSuccess();
	}

	function customDelete(){
		$data = $this->getCurrent();
		if( empty($data) )
			return false;

		if( !$this->canChangeForProvider($data["UF_PROVIDER"]) )
			return false;

		T50DB::startTransaction();
		$history = (new History)->init($this->orderId, $this->selfId);
		$logMessage = "Удалена заявка на установку [id " . $this->selfId . "]";
		if( $this->delete() && $history->addSimpleComment($logMessage, $this->orderId, true)->save() )
			return T50DB::commit();

		return T50DB::rollback();
	}

	private function canChangeForProvider($providerIdOrStr, int $orderId = null){
		$orderId = $orderId ?? $this->orderId;
		$provider = $providerIdOrStr;
		if( is_numeric($providerIdOrStr) )
			$provider = InstallationOrm::getEnum("UF_PROVIDER")[$providerIdOrStr];

		if( $provider == "remcity" && RemCityOrder::isSent($orderId) ){
			$this->log("blocked changes for remcity (is sent)");
			return false;
		}

		return true;
	}

	static function getProviders(){
		$arCodeInfo = InstallationOrm::getEnum("UF_PROVIDER", false);
		return T50ArrayHelper::toIndexed($arCodeInfo, ["val", "val" => "title"]);
	}
}
