<?php

namespace ReportsBestModelSupplierComponent;

use Bitrix\Main\Text\Encoding;

class TotalCsv
{
	private $arResult;

	function setArResult(array $arResult){
		$this->arResult = $arResult;
		return $this;
	}

	function export(){
		$data = $this->calc();
		$dataForCsv = $this->prepareForCsv($data);
		$this->outCsv($dataForCsv);
	}

	private function outCsv(array $data){
		$GLOBALS["APPLICATION"]->RestartBuffer();
		header("Content-disposition: attachment; filename=total.csv");
		header("Content-Type: text/csv");

		$fp = fopen('php://output', 'w');
		foreach($data as $fields)
    		fputcsv($fp, $fields, ";");
		fclose($fp);

		exit();
	}

	private function prepareForCsv(array $data){
		$result = array();

		$header = [""];
		foreach($this->arResult["USED_SUPPLIERS"] as $supplierId)
		    $header[] = $this->arResult["SUPPLIERS"][$supplierId];
		$result[] = $header;

		$total = ["Ассортимент"];
		foreach($this->arResult["USED_SUPPLIERS"] as $supplierId)
		    $total[] = $data[$supplierId]["TOTAL"];
		$result[] = $total;

		$best = ["Лучших цен"];
		foreach($this->arResult["USED_SUPPLIERS"] as $supplierId)
		    $best[] = $data[$supplierId]["BEST"];
		$result[] = $best;

		$result = Encoding::convertEncoding($result, "utf-8", "cp1251");
		return $result;
	}

	private function calc(){
		$data = array_fill_keys($this->arResult["USED_SUPPLIERS"], array(
			"TOTAL" => 0,
			"BEST" => 0,
		));
		foreach($this->arResult["ITEMS"] as $item){
		    foreach($item["SUPPLIERS"] as $supplierId => $supplier){
		        $data[$supplierId]["TOTAL"] ++;
		        if( $supplier["is_best"] )
		        	$data[$supplierId]["BEST"] ++;
		    }
		}

		return $data;
	}
}