<?php

namespace Agregator\Order;

use T50ArrayHelper;
use T50DB;
use T50GlobVars;
use rat\agregator\Deduction as DeductionOrm;
use Agregator\Manager\Manager;

class Deduction
{
	use Traits\Log;

	private $orderId;
	private $id;

	function init(int $orderId, int $id = 0){
		if( $orderId > 0 )
			$this->orderId = $orderId;

		if( $id > 0 )
			$this->id = $id;

		return $this;
	}

	function setAmount(int $amount){
		if( $amount <= 0 )
			return false;

		return $this->update("UF_AMOUNT", $amount);
	}

	function setComment(string $comment){
		if( empty($comment) )
			return false;

		return $this->update("UF_COMMENT", $comment);
	}

	function setType(int $typeId){
		$types = DeductionOrm::getEnum("UF_TYPE");
		if( !isset($types[$typeId]) )
			return false;

		return $this->update("UF_TYPE", $typeId);
	}

	function create(){
		if( !$this->checkParams() )
			return false;

		$updData = [
			"UF_DATE" => date("d.m.Y"),
			"UF_ORDER_ID" => $this->orderId,
			"UF_MANAGER_ID" => $GLOBALS["USER"]->getId(),
		];

		$result = DeductionOrm::clas()::add($updData);
		if( $result->isSuccess() )
			return $result->GetId();

		return false;
	}

	private function update($code, $value){
		if( !$this->checkParams(true) )
			return false;

		$updData = [
			"UF_MANAGER_ID" => $GLOBALS["USER"]->getId(),
			$code => $value
		];
		return DeductionOrm::clas()::update($this->id, $updData)->isSuccess();
	}

	function delete(){
		if( !$this->checkParams(true) )
			return false;

		$data = DeductionOrm::clas()::getRowById($this->id);
		if( $data["UF_ORDER_ID"] != $this->orderId )
			return false;

		return DeductionOrm::clas()::delete($this->id)->isSuccess();
	}

	private function checkParams($checkId = false){
		if( !Manager::canWorkWithDeduction() )
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