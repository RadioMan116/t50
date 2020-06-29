<?php

namespace OrdersDetailComponent\AJAX;

use Agregator\Components\BaseAjaxComponent;
use Agregator\Order\Deduction as DeductionOrder;
use rat\agregator\Deduction as DeductionOrm;
use T50GlobVars;
use T50Date;

class Deduction extends BaseAjaxComponent
{
	function create(){
		$valid = $this->prepare(["order_id" => "intval"])->validate(["order_id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$deduction = new DeductionOrder;
		$deduction->init($this->input->order_id);
		$id = $deduction->create();
		$this->load(($id > 0));
	}

	function load($result = true){
		$valid = $this->prepare([
				"order_id" => "intval",
			])->validate([
				"order_id" => "positive",
			]);
		if( !$valid )
			$this->validateErrors();

		$data = DeductionOrm::clas()::getList([
			"filter" => ["UF_ORDER_ID" => $this->input->order_id]
		])->fetchall();
		$data = $this->prepareForJson($data);
		$this->resultJson($result, "", $data);
	}

	private function prepareForJson(array $data){
		$arResult = array();
		foreach($data as $item){
		    $arResult[] = array(
			    "id" => $item["ID"],
			    "date" => T50Date::bxdate($item["UF_DATE"]),
			    "manager" => $item["UF_MANAGER_ID"],
			    "deduction" => $item["UF_AMOUNT"],
			    "type" => $item["UF_TYPE"],
			    "comment" => $item["UF_COMMENT"],
		    );
		}
		return $arResult;
	}

	function update(){
		$valid = $this->prepare([
				"order_id, id" => "intval",
				"code, value" => "htmlspecialchars",
			])->validate([
				"order_id, id" => "positive",
				"code" => "in:deduction,type,comment",
			]);
		if( !$valid )
			$this->validateErrors();

		$deduction = new DeductionOrder;
		$deduction->init($this->input->order_id, $this->input->id);

		switch ($this->input->code) {
			case "deduction":
				$result = $deduction->setAmount((int) $this->input->value);
			break;
			case "comment":
				$result = $deduction->setComment($this->input->value);
			break;
			case "type":
				$result = $deduction->setType((int) $this->input->value);
			break;
		}

		$this->load($result);
	}

	function delete(){
		$valid = $this->prepare(["order_id, id" => "intval"])->validate(["order_id, id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$deduction = new DeductionOrder;
		$deduction->init($this->input->order_id, $this->input->id);
		$result = $deduction->delete();
		$this->load($result);
	}

	function loadTypes(){
		$data = DeductionOrm::getEnum("UF_TYPE", false);
		$types = [];
		foreach($data as $code => $item){
		    $item = ["val" => $item["id"], "title" => $item["val"]];
		    $types[] = $item;
		}

		$this->resultJson(true, "", $types);
	}
}