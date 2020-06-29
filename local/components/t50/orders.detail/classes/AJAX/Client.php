<?php

namespace OrdersDetailComponent\AJAX;

use Agregator\Order\Mailing;
use Agregator\Components\BaseAjaxComponent;
use rat\agregator\Client as ClientOrm;
use Agregator\Order\Client as OrderClient;
use rat\agregator\Order as OrderOrm;
use T50GlobVars;

class Client extends BaseAjaxComponent
{
	function load(){
		$valid = $this->prepare(["order_id" => "intval"])->validate(["order_id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$this->loadByOrderId($this->input->order_id);
	}

	private function loadByOrderId(int $orderId){
		$clientId = (int) OrderOrm::clas()::getRowById($orderId)["UF_CLIENT"];
		$this->loadById($clientId);
	}

	private function loadById(int $id){
		$client = new OrderClient;
		$result = $client->load($id);
		if( $result == false )
			$this->resultJson(false, "not found client for order {$orderId}");

		$this->resultJson(true, "", $result);
	}

	function update(){
		$valid = $this->prepare([
				"order_id, id" => "intval",
				"value, code" => "htmlspecialchars",
			])->validate([
				"order_id, id" => "positive",
				"code" => "in:fio,fio_dop,phone,phone_dop,email,is_entity,requisites,city,street,house_number,porch,floor,apartment,intercom,elevator",
			]);
		if( !$valid )
			$this->validateErrors();

		$client = new OrderClient;
		$success = $client->update($this->input->id, $this->input->code, $this->input->value, $this->input->order_id);

		if( $success ){
			$this->loadById($this->input->id);
		} else {
			$this->resultJson(false, "cannot update");
		}

	}
}