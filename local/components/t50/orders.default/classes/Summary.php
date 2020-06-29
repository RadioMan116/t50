<?php

namespace OrdersDefaultComponent;

class Summary
{
	function calculate(array $arResult){
		$fields = array_fill_keys(
			["ORDERS", "PRODUCTS", "SALE", "PURCHASE", "COMMISSION", "SUPPL_COMMISSION"], 0);

		$sumByStatus = array();
		foreach($arResult["INITIAL_DATA"]["STATUS"]["items"] as $status){
		    $sumByStatus[$status["ID"]] = $fields;
		    $sumByStatus[$status["ID"]]["STATUS"] = $status["NAME"];
		}

		foreach($arResult["ITEMS"] as $orderId => $order){
			$statusId = $order["UF_STATUS"];
			if( $statusId == 0 )
				return [];

			$sumByStatus[$statusId]["ORDERS"] ++;
    	    foreach($order["BASKET"] as $item){
    	    	$sumByStatus[$statusId]["PRODUCTS"] += $item["UF_QUANTITY"];
    	    	$sumByStatus[$statusId]["SALE"] += $item["UF_PRICE_SALE"];
    	    	$sumByStatus[$statusId]["PURCHASE"] += $item["UF_PRICE_PURCHASE"];
    	    	$sumByStatus[$statusId]["COMMISSION"] += $item["UF_COMMISSION"];
    	    	$sumByStatus[$statusId]["SUPPL_COMMISSION"] += $item["UF_COM_SUPPLIER"];
    	    }
    	}

    	$total = $fields;
    	foreach($sumByStatus as $items){
    		foreach($items as $field => $value){
    		    $total[$field] += $value;
    		}
    	}


    	return ["SUM_BY_STATUS" => $sumByStatus, "SUM_TOTAL" => $total];
	}

}