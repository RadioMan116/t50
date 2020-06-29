<?php

namespace Agregator\Order;

use T50ArrayHelper;
use T50GlobVars;
use Agregator\Components\InputManager;
use rat\agregator\Account as AccountOrm;
use T50DB;

class Account extends BasketCollection
{

	function setOrderAccount($account){ // Заказ
		if( !$this->checkValue($account, "UF_ACCOUNT") )
			return false;

		return $this->update(["UF_ACCOUNT" => $account]);
	}

	function setOrderProductAccount($accounts){ // Заказ товара
		if( !$this->checkValue($accounts, "UF_ACCOUNT_PRODUCT") )
			return false;

		return $this->update(["UF_ACCOUNT_PRODUCT" => $accounts]);
	}

	function setArrivalDate($dates){ // Дата прихода
		if( !$this->checkValue($dates, "UF_DATE_ARRIVAL") )
			return false;

		return $this->update(["UF_DATE_ARRIVAL" => $dates]);
	}

	function setDeliveryAccount($accounts){ // Заказ доставки
		if( !$this->checkValue($accounts, "UF_ACCOUNT_DELIVERY") )
			return false;

		return $this->update(["UF_ACCOUNT_DELIVERY" => $accounts]);
	}

	function setOurOfficialAccount($accounts){ // Счет офиц. наш
		if( !$this->checkValue($accounts, "UF_OFFICIAL_OUR") )
			return false;

		return $this->update(["UF_OFFICIAL_OUR" => $accounts]);
	}

	function setPartnersOfficialAccount($accounts){ // Счет офиц. партнеров
		if( !$this->checkValue($accounts, "UF_OFFICIAL_PARTNERS") )
			return false;

		return $this->update(["UF_OFFICIAL_PARTNERS" => $accounts]);
	}

	function setTransportAccount($accounts){ // Номер ТН ТК
		if( !$this->checkValue($accounts, "UF_ACCOUNT_TN_TK") )
			return false;

		return $this->update(["UF_ACCOUNT_TN_TK" => $accounts]);
	}

	function setFlagInStock($flag){ // На складе
		$flag = (new InputManager())->singlePrepare($flag, "boolean");
		if( !$this->checkValue($flag, "UF_IN_STOCK") )
			return false;

		$flag = ( $flag ? 1 : 0 );
		return $this->update(["UF_IN_STOCK" => $flag]);
	}

	function setFlagInShipment($flag){ // Отгрузка
		$flag = (new InputManager())->singlePrepare($flag, "boolean");
		if( !$this->checkValue($flag, "UF_SHIPMENT") )
			return false;

		$flag = ( (int) $flag ? 1 : 0 );
		return $this->update(["UF_SHIPMENT" => $flag]);
	}

	function removeRow(int $index){
		if( $index < 0 )
			return false;

		$current = $this->getCurrent();
		if( empty($current) )
			return false;

		$multipleFields = ["UF_ACCOUNT_TN_TK", "UF_OFFICIAL_PARTNERS", "UF_OFFICIAL_OUR", "UF_ACCOUNT_DELIVERY", "UF_DATE_ARRIVAL", "UF_ACCOUNT_PRODUCT"];

		$updData = [];

		foreach($multipleFields as $field){
		    $values = $current[$field];
		    if( !is_array($values) )
		    	continue;

		    $values = array_values($values);
		    if( !isset($values[$index]) )
		    	continue;

		    unset($values[$index]);
		    $values = $this->emptyItemsToSplace($values, $field);
		    $updData[$field] = $values;
		}
		if( empty($updData) )
			return true;

		return $this->update($updData);
	}

	function update(array $updData){
		if( isset($this->collectionForUpdate) ){
			$collectionForUpdate = $this->collectionForUpdate;
			$this->collectionForUpdate = null;
			T50DB::startTransaction();
			foreach($collectionForUpdate as $account){
			    if( !$account->update($updData) ){
			    	return T50DB::rollback();
			    }
			}
			return T50DB::commit();
		}

		if( $this->orderId <= 0 || $this->basketId <= 0 )
			return false;

		foreach($updData as $code => $value){
			if( is_array($value) )
				$value = $this->emptyItemsToSplace($value, $code);

		    $updData[$code] = $value;
		}

		return $this->updateWithHistory(
			$this->orderId,
			$this->basketId,
			$updData,
			$this->getCurrent()
		);
	}

	private function emptyItemsToSplace(array $items, $code){
		$isDate = substr_count($code, "DATE");
		$items = array_values($items);
		for($i = count($items) - 1; $i >= 0; $i--) {
			if( $items[$i] === "" || $items[$i] === " " || ($isDate && $items[$i] == "01.01.1970")){
				unset($items[$i]);
			} else {
				break;
			}
		}

		return array_map(function ($item) use($isDate){
			if( $item === "" )
				return ( $isDate ? "01.01.1970" : " " );
			return $item;
		}, $items);
	}

	protected function checkValue($value, $field){
		$errorMessage = function ($messageExpected) use($field, $value){
			return "valid value for field {$field}\n{$messageExpected}\nvalue: " . var_export($value, true);
		};

	    switch ($field) {
			case "UF_ACCOUNT_TN_TK":
			case "UF_OFFICIAL_PARTNERS":
			case "UF_OFFICIAL_OUR":
			case "UF_ACCOUNT_DELIVERY":
	    	case "UF_ACCOUNT_PRODUCT":
				if( !is_array($value) )
					return $this->logError($errorMessage("required array of strings"));
			break;
			case "UF_DATE_ARRIVAL":
				if( !is_array($value) )
					return $this->logError($errorMessage("required array of strings"));

				$notEmptyValues = array_filter(array_map("trim", $value));
				$valueDates = array_map(["T50Date", "check"], $notEmptyValues);
				if( in_array(false, $valueDates) )
					return $this->logError($errorMessage("required array of strings valid dates"));
			break;
	    	case "UF_ACCOUNT":
	    		if( !is_string($value) )
	    			return $this->logError($errorMessage("required string"));
	    	break;
	    	case "UF_SHIPMENT":
	    	case "UF_IN_STOCK":
	    		if( !is_bool($value) )
	    			return $this->logError($errorMessage("required boolean"));
	    	break;
			default:
				return $this->logError("undefined filed {$field}");
	    }

		return true;
	}

}
