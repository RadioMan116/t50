<?php

namespace Agregator\Order;

use T50ArrayHelper;
use T50GlobVars;
use rat\agregator\OrderComment;
use T50Date;
use Agregator\Manager\Manager;

class OrderComments
{

	private $orderId;
	private $id;
	private $forReclamation;

	function create(int $themeId, $comment, array $arRemindDateTime = [], array $targetManagers = []){
		if( !($this->orderId > 0) || !$this->hasAccess() )
			return false;

		if( empty($comment) )
			return false;

		$themes = OrderComment::getEnum("UF_THEME");
		if( !$this->forReclamation && !isset($themes[$themeId]) )
			return false;

		$managersId = array_keys(T50GlobVars::get("MANAGERS"));
		$targetManagers = array_intersect($targetManagers, $managersId);

		if( !empty($arRemindDateTime) ){
			$remindDateTime = $this->makeRemindDateTime($arRemindDateTime);
			if( !$remindDateTime )
				return false;
		}

		$newData = [
			"UF_THEME" => ( $this->forReclamation ? 0 : $themeId ),
			"UF_DATE_CREATE" => date("d.m.Y H:i:s"),
			"UF_MANAGER_ID" => $GLOBALS["USER"]->getId(),
			"UF_COMMENT" => $comment,
			"UF_ORDER_ID" => $this->orderId,
			"UF_CLAIM_COMMENT" => ( $this->forReclamation ? 1 : 0 ),
		];

		if( isset($remindDateTime) )
			$newData["UF_DATE_REMIND"] = $remindDateTime;

		if( !empty($targetManagers) )
			$newData["UF_TARGET_MANAGERS"] = $targetManagers;

		$result = OrderComment::clas()::add($newData);
		if( !$result->isSuccess() )
			return false;

		$this->id = $result->getId();

		return true;
	}

	function init(int $orderId, int $id = 0){
		if( $orderId > 0 )
			$this->orderId = $orderId;

		if( $id > 0 )
			$this->id = $id;

		return $this;
	}

	function setIsForReclamation(){
		$this->forReclamation = true;
		return $this;
	}

	function unsetRemind(){
		return $this->update(["UF_DATE_REMIND" => null]);
	}

	function setRemind($date, $time){
		$remindDateTime = $this->makeRemindDateTime([$date, $time]);
		if( !$remindDateTime )
			return false;

		return $this->update(["UF_DATE_REMIND" => $remindDateTime]);
	}

	function getList(int $orderId, array $searchFilter = array()){
		if( $orderId <= 0 )
			return [];

		$filter = ["UF_ORDER_ID" => $orderId, "UF_CLAIM_COMMENT" => ( $this->forReclamation ? 1 : 0 )];

		if( T50Date::check($searchFilter["date_from"], "d.m.Y") )
			$filter[">=UF_DATE_CREATE"] = $searchFilter["date_from"];

		if( T50Date::check($searchFilter["date_to"], "d.m.Y") )
			$filter["<=UF_DATE_CREATE"] = $searchFilter["date_to"] . "23:59:59";

		$managerId = (int) $searchFilter["manager"];
		if( $managerId > 0 )
			$filter["UF_MANAGER_ID"] = $managerId;

		$themeId = (int) $searchFilter["theme"];
		if( isset(OrderComment::getEnum("UF_THEME")[$themeId]) )
			$filter["UF_THEME"] = $themeId;

		return OrderComment::clas()::getList(compact("filter"))->fetchAll();
	}

	private function makeRemindDateTime(array $arRemindDateTime){
		list($date, $time) = $arRemindDateTime;
		if( !T50Date::check($date) )
			return false;

		if( !T50Date::check($time, "H:i") )
			return false;

		return "{$date} {$time}:00";
	}

	private function update(array $updData){
		if( !($this->orderId > 0 && $this->id > 0) || !$this->hasAccess() )
			return false;

		$current = OrderComment::clas()::getRowById($this->id);
		if( $current["UF_ORDER_ID"] != $this->orderId )
			return false;

		$result = OrderComment::clas()::update($this->id, $updData);
		return $result->isSuccess();
	}

	private function hasAccess(){
		if( $this->forReclamation && !Manager::canWorkWithClaim() )
			return false;

		return true;
	}
}