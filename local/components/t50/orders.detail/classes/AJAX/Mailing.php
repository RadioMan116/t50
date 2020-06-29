<?php

namespace OrdersDetailComponent\AJAX;

use Agregator\Components\BaseAjaxComponent;
use Agregator\Order\Mailing as MailingClasses;
use Agregator\Common\Mail\MailStructure;
use rat\agregator\OrderHistory;
use T50Date;
use T50GlobVars;

class Mailing extends BaseAjaxComponent
{
	function loadData(){
		$valid = $this->prepare([
				"order_id" => "intval",
				"email_type" => "htmlspecialchars",
			])->validate([
				"order_id" => "positive",
				"email_type" => "in: order_for_client, request_avail, order_for_supplier",
			]);

		if( !$valid )
			$this->validateErrors();

		$mailing = $this->factoryMailing();
		if( !isset($mailing) )
			$this->resultJson(false);

		$result = $mailing->getPreview($this->input->order_id);

		if( $result instanceof  MailStructure )
			$this->resultJson(true, "", $result->getData());

		$this->resultJson(false);
	}

	function send(){
		$valid = $this->prepare([
				"order_id" => "intval",
				"email_type" => "htmlspecialchars",
				"content_type" => "htmlspecialchars",
				"body" => "pass",
				"copy" => "pass",
				"from" => "pass",
				"hidden_copy" => "pass",
				"reply_to" => "pass",
				"subject" => "pass",
				"to" => "pass",
			])->validate([
				"order_id" => "positive",
				"content_type" => "in: text, html",
				"email_type" => "in: order_for_client, request_avail, order_for_supplier",
				"body" => "required",
				"from" => "required",
				"subject" => "required",
				"to" => "required",
			]);

		if( !$valid )
			$this->validateErrors();

		$mailData = new MailStructure;
		foreach($this->input as $code => $value)
		    $mailData->$code = $value;


		$mailing = $this->factoryMailing();
		if( !isset($mailing) )
			$this->resultJson(false);

		$success = $mailing->send($this->input->order_id, $mailData);

		$this->resultJson($success);
	}

	function loadHistory(){
		$valid = $this->prepare(["order_id" => "intval"])->validate(["order_id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$managers = T50GlobVars::get("MANAGERS");
		$arResult = [];

		$res = OrderHistory::clas()::getList(["filter" => [
			"UF_ORDER_ID" => $this->input->order_id,
			"UF_CODE" => "MAILING",
		]]);
		while( $result = $res->Fetch() ){
			$dateTime = T50Date::bxDate($result["UF_DATE_TIME"], "d.m.Y H:i");
			$manager = $managers[$result["UF_MANAGER_ID"]]["NAME"];
			$comment = $result["UF_COMMENT"];
			$arResult[] = "[{$dateTime}] {$manager} > {$comment}";
		}

		$this->resultJson(true, "", $arResult);
	}

	/*

	*/

	private function factoryMailing(){
		switch ($this->input->email_type) {
			case "order_for_client":
				return new MailingClasses\OrderForClient;
			case "request_avail":
				return new MailingClasses\RequestAvailSupplier;
			case "order_for_supplier":
				return new MailingClasses\OrderForSupplier;
		}
	}
}