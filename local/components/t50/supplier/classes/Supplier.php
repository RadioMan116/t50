<?php

namespace HiddenSuppliersCompoment;

use Agregator\IB\Elements;
use Agregator\IB\Element;


class Supplier
{
	function load(int $supplierId){
		$elements = new Elements("suppliers");
		$select = array("NAME", "DETAIL_TEXT");
		$data = $elements->filter(["ID" => $supplierId])->select($select)->props("SITE")->getOneFetch();
		if( !empty($data["PROPERTY_SITE_VALUE"]) )
			$data += $this->prepareSite($data["PROPERTY_SITE_VALUE"]);

		return $data;
	}

	private function prepareSite($url){
		$parseUrl = parse_url($url);
		return ["SITE_URL" => $url, "DOMEN" => $parseUrl["host"]];
	}

	function update(\StdClass $input){
		$element = new Element("suppliers");

		$updData = [
			"MODIFIED_BY" => $GLOBALS["USER"]->GetID(),
      		"DETAIL_TEXT_TYPE" =>"html",
     	 	"DETAIL_TEXT" => html_entity_decode($input->text),
		];
		$result = $element->update($input->supplier_id, $updData, [], $errors);

		return $result;
	}
}