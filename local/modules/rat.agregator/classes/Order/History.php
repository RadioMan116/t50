<?php

namespace Agregator\Order;

use rat\agregator\OrderHistory;
use T50GlobVars;
use T50Date;

class History
{
	private $data;
	private $newRecords = [];
	private $ignore = false;

	function init(int $orderId, int $id){
		if( $orderId > 0 && $id > 0 ){
			$this->data = [
				"UF_ORDER_ID" => $orderId,
				"UF_ENTITY_ID" => $id,
			];
		}
		return $this;
	}

	function ignore(){
		$this->ignore = true;
		return $this;
	}

	function addChanges($table, $code, array $changes, bool $isManager, $comment = "", $codeFlag = ""){
		if( $this->ignore )
			return;

		global $USER;
		if( empty($this->data) )
			throw new \RuntimeException("History not initialized");

		if( count($changes) != 2 )
			throw new \RuntimeException("required array with old and new values");

		if( !isset(T50GlobVars::get("HL_TABLES")[$table]) )
			throw new \RuntimeException("undefined table '{$table}'");

		foreach($changes as $k => $value)
		    $changes[$k] = $this->prepareValue($value);

		if( empty($changes[0]) && empty($changes[1]) )
			return;

		if( $changes[0] === $changes[1] )
			return;

		$data = $this->data;
		$data["UF_DATE_TIME"] = date("d.m.Y H:i:s");
		$data["UF_ENTITY"] = $table;
		$data["UF_CODE"] = $code;
		$data["UF_CODE_FLAG"] = $codeFlag;
		$data["UF_BEFORE"] = $changes[0];
		$data["UF_AFTER"] = $changes[1];
		$data["UF_MANAGER_ID"] = ( $isManager ? $USER->getId() : 0 );
		$data["UF_COMMENT"] = $comment;

		$this->newRecords[] = $data;
	}

	private function prepareValue($value){
		if( is_array($value) )
			return implode(", ", $value);

		return $value;
	}

	function addChangesWithFlags($ormClass, $beforeIds, $afterIds, bool $isManager, $code = "UF_FLAGS" ){
		if( empty($beforeIds) )
			$beforeIds = [];

		if( empty($afterIds) )
			$afterIds = [];

		$table = $ormClass::getTableName();

		$off = array_diff($beforeIds, $afterIds);
		$on = array_diff($afterIds, $beforeIds);

		$flagsIdCode = $ormClass::getEnum($code);

		foreach($off as $id)
		    $this->addChanges($table, $code, [1, 0], $isManager, "", $flagsIdCode[$id]);

		foreach($on as $id)
		    $this->addChanges($table, $code, [0, 1], $isManager, "", $flagsIdCode[$id]);
	}

	function save(){
		$newRecords = $this->newRecords;
		$this->newRecords = [];
		$success = true;
		foreach($newRecords  as $data){
		    if (!OrderHistory::clas()::add($data)->isSuccess())
		    	$success = false;
		}

		$this->data = null;
		return $success;
	}

	function getComments(int $orderId){
		$managers = T50GlobVars::get("MANAGERS");
		$data = OrderHistory::clas()::getList([
			"order" => ["ID" => "ASC"],
			"filter" => [
				"UF_ORDER_ID" => $orderId,
				"UF_CODE" => ["UF_PRICE_SALE", "UF_PRICE_PURCHASE"],
				"!UF_COMMENT" => false,
			]
		])->fetchAll();

		$arResult = [];
		$codeAlias = ["UF_PRICE_SALE" => "sale", "UF_PRICE_PURCHASE" => "purchase"];
		foreach($data as $item){
			$code = $codeAlias[$item["UF_CODE"]];
			$arResult[$item["UF_ENTITY_ID"]][$code] = [
		    	"before" => $item["UF_BEFORE"],
		    	"after" => $item["UF_AFTER"],
		    	"date" => T50Date::bxdate($item["UF_DATE_TIME"]),
		    	"comment" => $item["UF_COMMENT"],
		    	"manager" => $managers[$item["UF_MANAGER_ID"]]["NAME"],
		    ];
		}

		return $arResult;
	}

	function addSimpleComment($comment, int $orderId, bool $isManager, string $code = "COMMON"){
		global $USER;
		$data = [];
		$data["UF_DATE_TIME"] = date("d.m.Y H:i:s");
		$data["UF_ENTITY"] = "COMMON";
		$data["UF_CODE"] = $code;
		$data["UF_ORDER_ID"] = $orderId;
		$data["UF_MANAGER_ID"] = ( $isManager ? $USER->getId() : 0 );
		$data["UF_COMMENT"] = $comment;

		$this->newRecords[] = $data;
		return $this;
	}

}