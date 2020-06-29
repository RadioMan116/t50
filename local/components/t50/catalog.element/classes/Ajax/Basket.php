<?php

namespace CatalogElementComponent\Ajax;

use Agregator\Components\BaseAjaxComponent;

use Agregator\Order\Order;
use rat\agregator\Order as OrderOrm;
use Agregator\Order\BasketProductInfo;
use Agregator\Order\Basket as OrderBasket;
use Agregator\Components\Traits\ComponentData;
use T50GlobVars;
use T50DB;

class Basket extends BaseAjaxComponent
{
	function add(){
		$valid = $this->prepare([
                "city" => "htmlspecialchars",
                "shop_id, product_price_id" => "intval",
                "order_id" => "null, intval",
            ])->validate([
                "city" => "in: MSK, SPB",
                "order_id, shop_id, product_price_id" => "positive",
            ]);

        if( !$valid )
        	$this->validateErrors();

        $orderId = $this->create();
        $success = ( $orderId > 0 );
        $this->resultJson($success, "", ["orderId" => $orderId]);
	}

	private function create(){
		$input = $this->input;
		$order = new Order();
		$basket = new OrderBasket();
		$productInfo = new BasketProductInfo();
		$productInfo->setPriceId($input->product_price_id);

		$city = ( $input->city != "MSK" ? "St. Petersburg" : "Moscow" );

		$shopCode = T50GlobVars::get("CACHE_SHOPS")[$input->shop_id]["CODE"];

		T50DB::startTransaction();

		if( isset($input->order_id) ){
			$order = OrderOrm::clas()::getRowById($input->order_id);
			$orderId = (int) $order["ID"];
		} else {
			$orderId = $order->create($shopCode, $city);
		}

		if( $orderId <= 0 )
			return T50DB::rollback();

		$basketId = $basket->create($orderId, $productInfo);
		if( $basketId <= 0 )
			return T50DB::rollback();

		T50DB::commit();
		return $orderId;
	}
}