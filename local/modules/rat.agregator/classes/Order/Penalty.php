<?php

namespace Agregator\Order;

use T50ArrayHelper;
use T50DB;
use T50GlobVars;
use rat\agregator\Fine;
use Agregator\Manager\Manager;

class Penalty
{
	use Traits\Log;

	private $orderId;
	private $id;
	private $data = [];

	function init(int $orderId, int $id = 0){
		if( $orderId > 0 )
			$this->orderId = $orderId;

		if( $id > 0 )
			$this->id = $id;

		return $this;
	}

	function setManager(int $managerId){
		if( !isset(T50GlobVars::get("MANAGERS")[$managerId]) )
			return false;

		return $this->update("UF_RESPONSIBLE_ID", $managerId);
	}

	function setAmount(int $amount){
		if( $amount <= 0 )
			return false;

		return $this->update("UF_AMOUNT", $amount);
	}

	function setReason(string $reason){
		if( empty($reason) )
			return false;

		return $this->update("UF_REASON", $reason);
	}

	function setMonth(int $month){
		if( !in_array($month, range(1, 12)) )
			return false;

		return $this->update("UF_MONTH_PAY", $month);
	}

	function create(){
		if( !$this->checkParams() )
			return false;

		$updData = [
			"UF_DATE" => date("d.m.Y"),
			"UF_ORDER_ID" => $this->orderId,
			"UF_INITIATOR_ID" => $GLOBALS["USER"]->getId(),
		];

		$result = Fine::clas()::add($updData);
		if( $result->isSuccess() )
			return $result->GetId();

		return false;
	}

	private function update($code, $value){
		if( !$this->checkParams(true) )
			return false;

		$updData = [
			"UF_DATE" => date("d.m.Y"),
			"UF_INITIATOR_ID" => $GLOBALS["USER"]->getId(),
		];
		$updData[$code] = $value;
		return Fine::clas()::update($this->id, $updData)->isSuccess();
	}

	function delete(){
		if( !$this->checkParams(true) )
			return false;

		$data = Fine::clas()::getRowById($this->id);
		if( $data["UF_ORDER_ID"] != $this->orderId )
			return false;


		$id = $this->id;

		return Fine::clas()::delete($id)->isSuccess();
	}

	private function checkParams($checkId = false){
		if( !Manager::canWorkWithPenalty() )
			return $this->logError("user has not access");

		if( $this->orderId <= 0 )
			return $this->logError("empty order id");

		if( $checkId && $this->id <= 0 )
			return $this->logError("empty id");

		return true;
	}

	private function logError($message){
		$message = "USER [" . $GLOBALS["USER"]->getId() . "]; order [{$this->orderId}]; error:\n" . $message;
		$this->log($message);
		return false;
	}
}