<?php

namespace Agregator\Order;

use T50ArrayHelper;
use T50DB;

use rat\agregator\Delivery as DeliveryOrm;

class Delivery extends BasketCollection
{
	use Traits\Flags;

	function getDefaultData(){
		return [
			"UF_MKAD_PRICE" => 30,
		];
	}

	function setCondition(int $condId){
		$condIdCode = DeliveryOrm::getEnum("UF_CONDITIONS");
		if( !isset($condIdCode[$condId]) )
			return $this->logError("not valid condition id {$condId}");

		if( $condIdCode[$condId] == "pickup" || $condIdCode[$condId] == "delivery_tk" )
			return $this->resetPricesByCondition($condId);

		return $this->update(["UF_CONDITIONS" => $condId]);
	}

	function setDate($date){
		if( !\T50Date::check($date) )
			return $this->logError("not valid date {$date}");

		return $this->update(["UF_DATE" => $date]);
	}

	function setTime($time){
		return $this->update(["UF_TIME" => $time]);
	}

	function setPrice(int $price){
		if( $price < 0 )
			return false;
		return $this->update(["UF_COSTS" => $price]);
	}

	function setMKADDistantion(int $dist){
		if( $dist < 0 )
			return false;
		return $this->update(["UF_MKAD_KM" => $dist]);
	}

	function setMKADPrice(int $price){
		if( $price < 0 )
			return false;
		return $this->update(["UF_MKAD_PRICE" => $price]);
	}

	function setVipPrice(int $price){
		if( $price < 0 )
			return false;
		return $this->update(["UF_VIP" => $price]);
	}

	function setLiftPrice(int $price){
		if( $price < 0 )
			return false;
		return $this->update(["UF_LIFT" => $price]);
	}

	function setPickupAddress($address){
		return $this->update(["UF_PICKUP_ADDRESS" => $address]);
	}

	private function resetPricesByCondition($conditionId){
		$updData = [
			"UF_CONDITIONS" => $conditionId,
			"UF_LIFT" => 0,
			"UF_COSTS" => 0,
			"UF_VIP" => 0,
			"UF_MKAD_KM" => 0
		];
		return $this->update($updData, ["UF_LIFT","UF_COSTS","UF_VIP","UF_MKAD_KM"]);
	}

	function update(array $updData, array $systemChanges = []){
		if( isset($this->collectionForUpdate) ){
			$collectionForUpdate = $this->collectionForUpdate;
			$this->collectionForUpdate = null;
			T50DB::startTransaction();
			foreach($collectionForUpdate as $delivery){
			    if( !$delivery->update($updData, $systemChanges) ){
			    	return T50DB::rollback();
			    }
			}
			return T50DB::commit();
		}

		if( $this->orderId <= 0 || $this->basketId <= 0 )
			return false;

		return $this->updateWithHistory($this->orderId, $this->basketId, $updData, $this->getCurrent(), $systemChanges);
	}

}
