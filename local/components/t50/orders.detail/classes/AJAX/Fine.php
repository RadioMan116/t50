<?php

namespace OrdersDetailComponent\AJAX;

use Agregator\Components\BaseAjaxComponent;
use Agregator\Order\Penalty;
use rat\agregator\Fine as FineOrm;
use T50GlobVars;
use T50Date;

class Fine extends BaseAjaxComponent
{
	function create(){
		$valid = $this->prepare(["order_id" => "intval"])->validate(["order_id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$penalty = new Penalty;
		$penalty->init($this->input->order_id);
		$id = $penalty->create();
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

		$data = FineOrm::clas()::getList(["filter" => ["UF_ORDER_ID" => $this->input->order_id]])->fetchall();
		$data = $this->prepareForJson($data);
		$this->resultJson($result, "", $data);
	}

	private function prepareForJson(array $data){
		$arResult = array();
		foreach($data as $item){
		    $arResult[] = array(
			    "id" => $item["ID"],
			    "date" => T50Date::bxdate($item["UF_DATE"]),
			    "manager_responsible" => $item["UF_RESPONSIBLE_ID"],
			    "manager_initiator" => $item["UF_INITIATOR_ID"],
			    "fine" => $item["UF_AMOUNT"],
			    "reason" => $item["UF_REASON"],
			    "month" => $item["UF_MONTH_PAY"],
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
				"code" => "in:manager_responsible,fine,reason,month",
			]);
		if( !$valid )
			$this->validateErrors();

		$penalty = new Penalty;
		$penalty->init($this->input->order_id, $this->input->id);

		switch ($this->input->code) {
			case "manager_responsible":
				$result = $penalty->setManager((int) $this->input->value);
			break;
			case "fine":
				$result = $penalty->setAmount((int) $this->input->value);
			break;
			case "reason":
				$result = $penalty->setReason($this->input->value);
			break;
			case "month":
				$result = $penalty->setMonth((int) $this->input->value);
			break;
		}

		$this->load($result);
	}

	function delete(){
		$valid = $this->prepare(["order_id, id" => "intval"])->validate(["order_id, id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$penalty = new Penalty;
		$penalty->init($this->input->order_id, $this->input->id);
		$result = $penalty->delete();
		$this->load($result);
	}
}