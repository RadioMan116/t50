<?php

namespace OrdersDetailComponent\AJAX;

use Agregator\Components\BaseAjaxComponent;
use Agregator\Order\Docs as OrderDocs;
use T50GlobVars;

class Docs extends BaseAjaxComponent
{
	private $typeRule = "in:company_card, our_prepayment_invoice, partners_prepayment_invoice, proxy_shipment_tk,contract, purchase_order, proxy_receipt_goods";

	function load(){
		$valid = $this->prepare([
				"order_id" => "intval",
				"type" => "null, htmlspecialchars",
			])->validate([
				"order_id" => "positive",
				"type" => $typeRule,
			]);
		if( !$valid )
			$this->validateErrors();

		$docs = new OrderDocs();

		if( isset($this->input->type) ){
			$data = $docs->loadByType($this->input->order_id, $this->input->type);
			$data = array_map([$this, "prepareForJson"], $data);
		} else {
			$data = $docs->loadAll($this->input->order_id);
			foreach ($data as $code => $item)
				$data[$code] = array_map([$this, "prepareForJson"], $item);
		}

		$this->resultJson(true, "", $data);
	}

	private function prepareForJson($row){
		$result = $row["FILE_DATA"];
		$result["can_delete"] = $row["CAN_DELETE"];
		return $result;
	}

	function upload(){
		$valid = $this->prepare([
				"order_id" => "intval",
				"type" => "htmlspecialchars",
			])->validate([
				"order_id" => "positive",
				"type" => $this->typeRule,
				"file" => "File|mimes:xls,xlsx,doc,docx,pdf,sxw,sxc,sxd,sxi,stw,stc,std,sti,ods,jpg,jpeg,png,odt,rtf,rtx,csv,tif,mxl,txt,zip,rar|max:5120", // 5 Mb
			]);
		if( !$valid )
			$this->validateErrors();

		$docs = new OrderDocs();
		$success = $docs->save($this->input->order_id, $this->input->type, $this->input->file);
		if( !$success )
			$this->resultJson(false, "cannot save file");

		$this->load();
	}

	function delete(){
		$valid = $this->prepare([
				"file_id, order_id" => "intval",
				"type" => "htmlspecialchars",
			])->validate([
				"file_id, order_id" => "positive",
				"type" => $this->typeRule,
			]);
		if( !$valid )
			$this->validateErrors();

		$docs = new OrderDocs();
		$success = $docs->delete($this->input->file_id, $this->input->order_id, $this->input->type);
		if( !$success )
			$this->resultJson(false, "cannot delete file");

		$this->load();
	}
}