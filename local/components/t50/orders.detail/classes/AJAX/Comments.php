<?php

namespace OrdersDetailComponent\AJAX;

use Agregator\Components\BaseAjaxComponent;
use Agregator\Order\OrderComments;
use rat\agregator\OrderComment;
use T50GlobVars;
use T50Date;

class Comments extends BaseAjaxComponent
{
	function load(){
		$valid = $this->prepare([
				"order_id" => "intval",
				"date_from, date_to" => "null, htmlspecialchars",
				"manager, theme" => "null, intval",
				"reclamation" => "null, boolean",
			])->validate([
				"reclamation" => "bool",
				"order_id, manager, theme" => "positive",
				"date_from, date_to" => "date_format:d.m.Y",
			]);

		if( !$valid )
			$this->validateErrors();

		$filter = [];
		foreach(["date_from", "date_to", "manager", "theme"] as $code){
			if( isset($this->input->$code) )
				$filter[$code] = $this->input->$code;
		}

		$comments = new OrderComments();
		if( $this->input->reclamation )
			$comments->setIsForReclamation();

		$result = $comments->getList($this->input->order_id, $filter);
		$items = array_map([$this, "convertToJson"], $result);

		$themes = OrderComment::getEnumForJson("UF_THEME");
		$data = compact("items", "themes");

		$this->resultJson(true, "", $data);
	}

	private function convertToJson($data){
		static $managers;
		$managers = $managers ?? T50GlobVars::get("MANAGERS");

		$remindDate = T50Date::bxdate($data["UF_DATE_REMIND"], "d.m.Y");
		$remindTime = T50Date::bxdate($data["UF_DATE_REMIND"], "H:i");
		$targetManagers = $data["UF_TARGET_MANAGERS"];
		if( empty($targetManagers) )
			$targetManagers = [];

		return [
		    "id" => $data["ID"],
		    "manager" => $managers[$data["UF_MANAGER_ID"]]["NAME"],
		    "date" => T50Date::bxdate($data["UF_DATE_CREATE"], "d.m.Y H:i"),
		    "message" => $data["UF_COMMENT"],
		    "theme" => $data["UF_THEME"],
		    "remind" => isset($remindDate),
		    "remind_date" => $remindDate,
		    "remind_time" => $remindTime,
		    "target_managers" => $targetManagers,
		];
	}

	function create(){
		$valid = $this->prepare([
			"order_id, theme" => "intval",
			"comment" => "htmlspecialchars",
			"remind_date, remind_time" => "null, htmlspecialchars",
			"targetManagers*" => "null, intval",
			"reclamation" => "null, boolean",
		])->validate([
			"order_id" => "positive",
			"theme" => function ($value){
				return ( $value > 0 || $this->input->reclamation );
			},
			"comment" => "required",
			"remind_date" => "date_format:d.m.Y",
			"remind_time" => "date_format:H:i",
			"targetManagers*" => "positive",
			"reclamation" => "bool",
		]);
		if( !$valid )
			$this->validateErrors();

		$comments = new OrderComments();
		if( $this->input->reclamation )
			$comments->setIsForReclamation();
		$comments->init($this->input->order_id);
		$arRemindDateTime = [];
		$targetManagers = [];

		if( isset($this->input->remind_date) ){
			if( !isset($this->input->remind_time) )
				$this->input->remind_time = "00:00";

			$arRemindDateTime = [$this->input->remind_date, $this->input->remind_time];
		}

		if( !empty($this->input->targetManagers) )
			$targetManagers = $this->input->targetManagers;

		$success = $comments->create(
			$this->input->theme, $this->input->comment,
			$arRemindDateTime, $targetManagers
		);

		$data = null;
		if( $this->input->reclamation ){
			$result = $comments->getList($this->input->order_id);
			$items = array_map([$this, "convertToJson"], $result);
			$data = compact("items");
		}


		$this->resultJson($success, "", $data);
	}

	function update(){
		$valid = $this->prepare([
			"order_id, id" => "intval",
			"remind_date, remind_time" => "null, htmlspecialchars",
			"unset" => "null, boolean",
			"reclamation" => "null, boolean",
		])->validate([
			"order_id, id" => "positive",
			"remind_date" => "date_format:d.m.Y",
			"remind_time" => "date_format:H:i",
			"unset" => "bool",
			"reclamation" => "bool",
		]);

		if( !$valid )
			$this->validateErrors();

		$comments = new OrderComments();
		if( $this->input->reclamation )
			$comments->setIsForReclamation();
		$comments->init($this->input->order_id, $this->input->id);

		$result = false;
		if( $this->input->unset === true ){
			$result = $comments->unsetRemind();
		} elseif( isset($this->input->remind_date) ) {
			if( !isset($this->input->remind_time) )
				$this->input->remind_time = "00:00";
			$result = $comments->setRemind($this->input->remind_date, $this->input->remind_time);
		}

		$currentComment = $this->getOneComment($this->input->id, $this->input->order_id);
		$this->resultJson($result, "", $currentComment);
	}

	private function getOneComment(int $id, int $orderId){
		if( $id <= 0 || $orderId <= 0 )
			return;

		$data = OrderComment::clas()::getRow(["filter" => ["ID" => $id, "UF_ORDER_ID" => $orderId]]);
		return $this->convertToJson($data);
	}
}