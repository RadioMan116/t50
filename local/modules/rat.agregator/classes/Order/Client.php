<?php

namespace Agregator\Order;

use T50ArrayHelper;
use T50DB;
use T50GlobVars;
use rat\agregator\Client as ClientOrm;
use rat\agregator\Order as OrderOrm;
use Bitrix\Main\Entity\ReferenceField;

class Client
{
	use Traits\Log;

	private $data;
	private $history;

	private $jsonMap = [
		"id" => "ID",
		"fio" => "UF_FIO",
		"fio_dop" => "UF_FIO2",
		"phone" => "UF_PHONE",
		"phone_dop" => "UF_PHONE2",
		"email" => "UF_EMAIL",
		"is_entity" => "UF_IS_ENTITY",
		"requisites" => "UF_REQUISITES",
		"city" => "UF_CITY",
		"street" => "UF_STREET",
		"house_number" => "UF_HOUSE_NUMBER",
		"porch" => "UF_PORCH",
		"floor" => "UF_FLOOR",
		"apartment" => "UF_APARTMENT",
		"intercom" => "UF_INTERCOM",
		"elevator" => "UF_ELEVATOR",
		"send_email" => "UF_SEND_EMAIL",
	];

	function __construct(){
		$this->history = new History;
	}

	function update(int $id, $rawCode, $value, int $orderId){
		if( $orderId <= 0 )
			return $this->logError("not set orderId");

		$data = ClientOrm::clas()::getRowById($id);
		if( empty($data) )
			return $this->logError("not found client with id {$id}");

		$code = $this->detectCode($rawCode);
		if( empty($code) )
			return $this->logError("invalid code '{$rawCode}'");

		$value = $this->prepareValue($code, $value);

		if( !$this->checkValue($code, $value) )
			return false;

		$this->history->init($orderId, $orderId);
		$this->history->addChanges(ClientOrm::getTableName(), $code, array($data[$code], $value), true);

		T50DB::startTransaction();
		$success = ClientOrm::clas()::update($id, [$code => $value])->isSuccess();
		if( $success && $this->history->save())
			return T50DB::commit();

		return T50DB::rollback();
	}

	static function setIsEmailSent(int $orderId){
		$data = OrderOrm::clas()::getRow([
			"select" => ["CL.ID", "CL.UF_SEND_EMAIL"],
			"filter" => ["ID" => $orderId],
			"runtime" => [new ReferenceField("CL", ClientOrm::clas(), ['=this.UF_CLIENT' => 'ref.ID'])]
		]);
		if( empty($data) )
			return false;

		if( $data["ORDER_CL_UF_SEND_EMAIL"] )
			return true;

		return ClientOrm::clas()::update($data["ORDER_CL_ID"], ["UF_SEND_EMAIL" => 1])->isSuccess();
	}

	private function prepareValue($rawCode, $value, $forDb = true){
		$code = $this->detectCode($rawCode);
		if( in_array($code, ["UF_IS_ENTITY", "UF_SEND_EMAIL"]) )
			return (int) $value;

		if( in_array($code, ["UF_PHONE", "UF_PHONE2"]) ){
			if( preg_match("#^\d{10}$#", $value) ){
				return "+7" . $value;
			}
		}

		if( $code == "UF_ELEVATOR" ){
			if( $forDb )
				$value = ClientOrm::getEnum("UF_ELEVATOR", false)[$value]["id"];
			else
				$value = ClientOrm::getEnum("UF_ELEVATOR")[$value];
		}


		return $value;
	}

	private function checkValue($code, $value){
		if( $code == "UF_EMAIL" && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL) )
			return false;

		return true;
	}

	private function detectCode($code){
		if( isset($this->jsonMap[$code]) )
			return $this->jsonMap[$code];

		if( in_array($code, $this->jsonMap) )
			return $code;
	}

	function load(int $id){
		$data = ClientOrm::clas()::getRowById($id);
		if( empty($data) )
			return false;

		return $this->prepare($data);
	}

	private function prepare(array $data){
		$map = array_flip($this->jsonMap);
		$arResult = [];
		foreach($data as $code => $value){
			$code = $map[$code];
		    $arResult[$code] = $this->prepareValue($code, $value, false);
		}

		return $arResult;
	}

	private function logError($message){
		$this->log($message);
		return false;
	}

}