<?php

namespace Agregator\Sync\Market;
use Agregator\Product\Brand;
use rat\agregator\Product;


class Export
{
	function outCsv($path){
		$data = $this->getData();
		$head = array_keys(current($data));

		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Description: File Transfer');
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=t50_data.csv");
		header("Expires: 0");
		header("Pragma: public");

		$fh = fopen($path, 'w');
		echo "\$fh "; var_dump($fh);
		fputcsv($fh, $head, ";", '"');
		foreach($data as $item)
			fputcsv($fh, $item, ";", '"');
		fclose($fh);
		return true;
	}

	protected function getData(){
		$brands = \T50GlobVars::get("CACHE_BRAND_NAMES");
		$res = Product::clas()::getList([
			"select" => ["ID", "UF_MODEL_PRINT", "UF_BRAND"]
		]);
		$arResult = array();
		while($item = $res->Fetch()){
			$name = $item["UF_MODEL_PRINT"];
			$brand = $brands[$item["UF_BRAND"]];
			$arResult[] = array(
				"product_id" => $item["ID"],
				"category_name" => "",
				"brand_name" => $brand,
				"product_title" => $name,
				"price" => "",
				"purchase_price" => "",
				"available" => 1,
			);
		}
		return $arResult;
	}
}