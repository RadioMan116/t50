<?php
namespace Agregator\Sync\Catalog;

use Agregator\Product\Brand;
use rat\agregator\Product;
use T50GlobVars;

class Validator extends Base
{
	private $data = array();

	function getData(){
		$data = array_filter($this->data, function($item){
			return !( $item["unid"] > 0 );
		});
		return $data;
	}

	function validate($localXml){
		if( empty($this->shop) )
			$this->logger->exception("empty shop for validate");

		$this->loadData($localXml);

		$brands = array_fill_keys($this->shop["PROPERTY_BRANDS_VALUE"], 1);
		$categories = array_fill_keys($this->shop["PROPERTY_CATEGORIES_VALUE"], 1);

		$products = Product::getListIndexed([
			"filter" => ["UF_BRAND" => $this->shop["PROPERTY_BRANDS_VALUE"]],
			"select" => ["ID", "UF_MODEL"]
		], "ID");

		$models = $unids = [];

		foreach($this->data as $item){
			$models[] = $item["t50_model"];

			if( !isset($categories[$item["category"]]) )
				$this->reporter->add(ValidatorReport::ERR_SECTION, $item);

			if( !isset($brands[$item["brand"]]) )
				$this->reporter->add(ValidatorReport::ERR_BRAND, $item);

			if( $item["unid"] > 0 ){
				if( $products[$item["unid"]]["UF_MODEL"] != $item["t50_model"] )
					$this->reporter->add(ValidatorReport::ERR_CHANGE_MODEL, $item);

				$unids[] = $item["unid"];
			}
		}

		$doubleModels = $this->searchDoubles($models);
		$doubleUnids = $this->searchDoubles($unids);

		foreach($this->data as $item){
			if( $doubleModels[$item["t50_model"]] )
				$this->reporter->add(ValidatorReport::ERR_MOD_DOUBLES, $item);

			if( $doubleUnids[intval($item["unid"])] )
				$this->reporter->add(ValidatorReport::ERR_UNID_DOUBLES, $item);
		}

		$this->reporter->makeReport();

		if( $this->reporter->hasErrors() )
			return false;

		return true;
	}

	private function searchDoubles($values){
		$valueDoubles = array();
		foreach(array_count_values($values) as $value => $cnt){
			if( $cnt > 1 )
				$valueDoubles[$value] = true;
		}
		return $valueDoubles;
	}

	private function validateModel($model){
		$model = trim($model);
		$model = mb_strtolower($model, "utf-8");
		$letters = preg_replace("#[^0-9a-zа-яё]#u", "", $model);

		if( empty($letters) )
			return ["", ValidatorReport::ERR_EMPT_MODEL];

		$hasCyrillic = preg_match("#[а-яё]#u", $letters);
		$hasLatin = preg_match("#[a-z]#", $letters);

		if( $hasCyrillic ){
			if( $hasLatin )
				return [$model, ValidatorReport::ERR_MIX_MODEL];

			$model = \T50Text::translit($letters);
		} else {
			$model = $letters;
		}

		return [$model];
	}

	private function loadData($localXml){
		$shopCode = $this->shop["CODE"];

		if( !file_exists($localXml) )
			$this->logger->exception("tmp localXml {$localXml} not found for shop {$shopCode}");

		$simplexml = simplexml_load_file($localXml);
		if( !$simplexml )
			$this->logger->exception("cannot read {$localXml} for shop {$shopCode}");

		$this->data = array();

		$hasCyrillic = preg_match("#[а-яё]#u", $letters);
		$hasLatin = preg_match("#[a-z]#", $letters);

		foreach($simplexml->item as $item){
			$item = json_decode(json_encode($item), 1);
			$item["unid"] = (int) $item["unid"];

			list($model, $error) = $this->validateModel($item["model"]);
			if( isset($error) )
				$this->reporter->add($error, $item);
			$item["t50_model"] = $model;

			$this->data[] = $item;
		}
	}
}