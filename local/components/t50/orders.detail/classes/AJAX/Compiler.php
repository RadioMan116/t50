<?php

namespace OrdersDetailComponent\AJAX;

use Agregator\Components\BaseAjaxComponent;
use rat\agregator\Basket as BasketOrm;
use rat\agregator\Delivery as DeliveryOrm;
use rat\agregator\Account as AccountOrm;
use rat\agregator\Installation as InstallationOrm;
use rat\agregator\Deduction as DeductionOrm;
use T50ArrayHelper;
use T50GlobVars;
use T50Date;
use DateTime;

class Compiler extends BaseAjaxComponent
{
	function loadAllPrices(){
		$valid = $this->prepare(["order_id" => "intval"])->validate(["order_id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$data = [];
		// $data["ORDER"] = (new Order)->loadByOrderId($this->input->order_id);
		$data["BASKET"] = (new Basket)->loadByOrderId($this->input->order_id);
		$data["DELIVERY"] = (new Delivery)->loadByOrderId($this->input->order_id);
		$data["ACCOUNT"] = (new Account)->loadByOrderId($this->input->order_id);
		$data["INSTALLATION"] = (new Installation)->loadByOrderId($this->input->order_id);
		$data["PROFIT"] = $this->_loadProfit($this->input->order_id);

		$this->resultJson(true, "", $data);
	}

	function loadProfit(){
		$valid = $this->prepare(["order_id" => "intval"])->validate(["order_id" => "positive"]);
		if( !$valid )
			$this->validateErrors();

		$this->resultJson(true, "", $this->_loadProfit($this->input->order_id));
	}

	private function _loadProfit(int $orderId){
		$params = ["filter" => ["UF_ORDER_ID" => $orderId]];
		$data = [
			"basket" => BasketOrm::clas()::getList($params)->fetchAll(),
			"delivery" => DeliveryOrm::clas()::getList($params)->fetchAll(),
			"accounts" => AccountOrm::clas()::getList($params)->fetchAll(),
			"installation" => InstallationOrm::clas()::getList($params)->fetchAll(),
		];

		$basket = $this->compileBasketProfit($data);
		$instal = $this->compileInstallationProfit($data);
		$deductionSum = DeductionOrm::getSum($orderId);
		$fullCommission = $basket["sum"]["commission"] + $instal["sum"]["commission"] + $deductionSum;

		return [
			"basket_items" => $basket["items"],
			"basket_sum" => $basket["sum"],
			"instal_items" => $instal["items"],
			"instal_sum" => $instal["sum"],
			"full_commission" => $fullCommission,
			"delivery_date" => $this->calcDeliveryDate($data["delivery"]),
		];
	}

	private function calcDeliveryDate(array $delivery){
		$dates = T50ArrayHelper::filterMap($delivery, function (&$item){
			if( empty($item["UF_DATE"]) )
				return false;

			$date = new DateTime();
			$date->setTimestamp($item["UF_DATE"]->getTimestamp())->setTime(0, 0, 0);
			$item = $date;
			return true;
		}, true);

		if( empty($dates) )
			return ;

		usort($dates, function ($a, $b){
			return ( $a < $b ? -1 : 1 );
		});

		$today = new DateTime();
		$today->setTime(0, 0, 0);

		foreach($dates as $date){
		    if( $date > $today )
		    	return $date->format("d.m.Y");
		}

		return end($dates)->format("d.m.Y");
	}

	private function compileBasketProfit(array $data){
		$items = [];
		$sum = array_fill_keys(["sale", "purchase", "logistics", "commission", "suppl_commission", "diff"], 0);
		$suppliers = T50GlobVars::get("CACHE_SUPPLIERS");
		$data["accounts"] = T50ArrayHelper::keyBy($data["accounts"], "UF_BASKET_ID");
		$data["delivery"] = T50ArrayHelper::keyBy($data["delivery"], "UF_BASKET_ID");
		foreach($data["basket"] as $basket){
			$account = $data["accounts"][$basket["ID"]];
			$delivery = $data["delivery"][$basket["ID"]];
			$deliveryFlags = DeliveryOrm::getFlags($delivery["UF_FLAGS"]);
			$deliveryFlags = array_map(function ($state){
				return ( $state ? -1: 1 );
			}, $deliveryFlags);
			$logistics = $delivery["UF_COSTS"] * $deliveryFlags["US_COSTS"]
						+ $delivery["UF_MKAD_KM"] * $delivery["UF_MKAD_PRICE"] * $deliveryFlags["US_MKAD"]
						+ $delivery["UF_VIP"] * $deliveryFlags["US_VIP"]
						+ $delivery["UF_LIFT"] * $deliveryFlags["US_LIFT"];

			$sale = $basket["UF_PRICE_SALE"] * $basket["UF_QUANTITY"];
			$purchase = $basket["UF_PRICE_PURCHASE"] * $basket["UF_QUANTITY"];
			$commission = $basket["UF_COMMISSION"] * $basket["UF_QUANTITY"];

		    $item = [
		    	"id" => $basket["ID"],
		    	"title" => $basket["UF_NAME"],
		    	"url" => $basket["UF_PRODUCT_URL"],
		    	"supplier_name" => $suppliers[$basket["UF_SUPPLIER"]]["NAME"],
		    	"account" => $account["UF_ACCOUNT"],
		    	"date" => T50Date::bxdate($delivery["UF_DATE"]),
		    	"sale" => $sale,
		    	"purchase" => $purchase,
		    	"logistics" => $logistics,
		    	"commission" => $commission,
		    	"suppl_commission" => $basket["UF_COM_SUPPLIER"],
		    	"diff" => $basket["UF_COM_SUPPLIER"] - $commission,
		    	"mounth_vp" => $basket["UF_MONTH_ACC_VP"],
		    	"mounth_zp" => $basket["UF_MONTH_ACC_ZP"],
		    	"is_claim" => ($basket["UF_CLAIM"] == 1),
		    ];
		    foreach($sum as $field => $val)
		        $sum[$field] += $item[$field];

		    $items[] = $item;
		}

		return compact("items", "sum");
	}

	private function compileInstallationProfit(array $data){
		$items = [];
		$sum = array_fill_keys(["sale", "purchase", "master", "logistics", "commission", "suppl_commission", "diff" ], 0);
		$providersIdCode = InstallationOrm::getEnum("UF_PROVIDER");
		$data["accounts"] = T50ArrayHelper::keyBy($data["accounts"], "UF_BASKET_ID");
		$data["basket"] = T50ArrayHelper::keyBy($data["basket"], "ID");
		foreach($data["installation"] as $instal){
			$basket = $account = null;
			if( $instal["UF_BASKET_ID"] > 0 ){
				$basket = $data["basket"][$instal["UF_BASKET_ID"]];
				$account = $data["accounts"][$basket["ID"]];
			}

			$flagCostUs = InstallationOrm::getFlags($instal["UF_FLAGS"])["US_COSTS"];

			$master = $instal["UF_MASTER"];
			$logistics = $instal["UF_MKAD_PRICE"] * $instal["UF_MKAD_KM"];


			$sale = $instal["UF_PRICE_SALE"];
			$purchase = $instal["UF_PRICE_PURCHASE"];
			$commission = $instal["UF_COMMISSION"];

		    $item = [
		    	"id" => $instal["ID"],
		    	"title" => $basket["UF_NAME"],
		    	"url" => $basket["UF_PRODUCT_URL"],
		    	"provider" => $providersIdCode[$instal["UF_PROVIDER"]],
		    	"date" => T50Date::bxdate($instal["UF_DATE"]),
		    	"account" => $account["UF_ACCOUNT"],
		    	"sale" => $sale,
		    	"purchase" => $purchase,
		    	"master" => $master,
		    	"logistics" => $logistics,
		    	"commission" => $commission,
		    	"suppl_commission" => $instal["UF_COM_SUPPLIER"],
		    	"diff" => $instal["UF_COM_SUPPLIER"] - $commission,
		    	"mounth_vp" => $instal["UF_MONTH_ACC_VP"],
		    	"mounth_zp" => $instal["UF_MONTH_ACC_ZP"],
		    	"is_claim" => ($basket["UF_CLAIM"] == 1),
		    ];
		    foreach($sum as $field => $val)
		        $sum[$field] += $item[$field];

		    $items[] = $item;
		}

		return compact("items", "sum");
	}
}