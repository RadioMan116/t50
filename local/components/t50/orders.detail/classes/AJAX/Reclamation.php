<?php

namespace OrdersDetailComponent\AJAX;

use Agregator\Components\BaseAjaxComponent;
use Agregator\Order\Claim;
use rat\agregator\Complaint;
use T50GlobVars;
use T50Date;

class Reclamation extends BaseAjaxComponent
{
	function loadAll(){
		$valid = $this->prepare(["order_id" => "intval"])->validate(["order_id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$rawData = Complaint::clas()::getRow(["filter" => ["UF_ORDER_ID" => $this->input->order_id]]);
		$data = $this->convertToJson($rawData);

	    $data["requirements"] = Complaint::getEnumForJson("UF_REQUIREMENT");
	    $data["reasons"] = Complaint::getEnumForJson("UF_REASON");
	    $data["results"] = Complaint::getEnumForJson("UF_RESULT");
	    $data["errors"] = Complaint::getEnumForJson("UF_ERROR_TYPE");

		$claim = new Claim();
		$data["files"] = $claim->init($this->input->order_id)->getFiles($rawData["UF_FILES"]);

		$this->resultJson(true, "", $data);
	}

	function load($result = true){
		$valid = $this->prepare(["order_id" => "intval"])->validate(["order_id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$data = Complaint::clas()::getRow(["filter" => ["UF_ORDER_ID" => $this->input->order_id]]);
		$data = $this->convertToJson($data);
		$this->resultJson($result, "", $data);
	}

	private function convertToJson($data){
		$default = array(
		    "description" => "",
		    "requirement" => 0,
		    "reason" => 0,
		    "result" => 0,
		    "error" => 0,
		    "date_request" => "",
		    "date_finish" => "",
		    "date_start" => "",
		    "manager" => 0,
		);

		if( !is_array($data) || empty($data) )
			return $default;

		return array(
		    "description" => $data["UF_DESCRIPTION"],
		    "requirement" => $data["UF_REQUIREMENT"],
		    "reason" => $data["UF_REASON"],
		    "result" => $data["UF_RESULT"],
		    "error" => $data["UF_ERROR_TYPE"],
		    "date_request" => T50Date::bxdate($data["UF_DATE_REQUEST"]),
		    "date_finish" => T50Date::bxdate($data["UF_DATE_FINISH"]),
		    "date_start" => T50Date::bxdate($data["UF_DATE_START"]),
		    "manager" => $data["UF_MANAGER_ID"],
		);
	}

	function loadFiles($result = true){
		$valid = $this->prepare(["order_id" => "intval"])->validate(["order_id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$claim = new Claim();
		$files = $claim->init($this->input->order_id)->getFiles();

		$this->resultJson($result, "", $files);
	}

	function update(){
		$codes = ["description", "requirement", "reason", "result", "error", "date_request", "date_finish", "date_start", "manager"];

		$valid = $this->prepare([
				"order_id" => "intval",
				"code, value" => "htmlspecialchars",
			])->validate([
				"order_id" => "positive",
				"code" => "in:" . implode(",", $codes),
			]);
		if( !$valid )
			$this->validateErrors();

		$claim = new Claim();
		$claim->init($this->input->order_id);

		switch ($this->input->code) {
			case "description":
				$result = $claim->setDescription($this->input->value);
			break;
			case "requirement":
				$result = $claim->setRequirement((int) $this->input->value);
			break;
			case "reason":
				$result = $claim->setReason((int) $this->input->value);
			break;
			case "result":
				$result = $claim->setResult((int) $this->input->value);
			break;
			case "error":
				$result = $claim->setErrorType((int) $this->input->value);
			break;
			case "date_request":
				$result = $claim->setRequestDate($this->input->value);
			break;
			case "date_finish":
				$result = $claim->setFinishDate($this->input->value);
			break;
			case "date_start":
				$result = $claim->setAcceptDate($this->input->value);
			break;
			case "manager":
				$result = $claim->setResponsibleManager((int) $this->input->value);
			break;
		}

		$this->load($result);
	}

	function saveFile(){
		$valid = $this->prepare([
				"order_id" => "intval",
			])->validate([
				"order_id" => "positive",
				"file" => "File|mimes:xls,xlsx,doc,docx,pdf,sxw,sxc,sxd,sxi,stw,stc,std,sti,ods,jpg,jpeg,png,odt,rtf,rtx,csv,tif,mxl,txt,zip,rar|max:5120", // 5 Mb
			]);
		if( !$valid )
			$this->validateErrors();

		$claim = new Claim();
		$claim->init($this->input->order_id);
		$this->loadFiles($claim->saveFile($this->input->file));
	}

	function deleteFile(){
		$valid = $this->prepare([
				"order_id, file_id" => "intval",
			])->validate([
				"order_id, file_id" => "positive",
			]);
		if( !$valid )
			$this->validateErrors();

		$claim = new Claim();
		$claim->init($this->input->order_id);
		$this->loadFiles($claim->deleteFile($this->input->file_id));
	}
}