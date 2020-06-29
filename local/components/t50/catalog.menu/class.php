<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//use Bitrix\Main\Text\HtmlFilter;
use Agregator\Components\BaseComponent;
use Agregator\Components\Traits\ComponentDI;

class CatalogMenuComponent extends BaseComponent
{
	use ComponentDI;

	private function prepareAlphabet($code){
		$alphabet = [];
		foreach($this->arResult[$code] as $k => $item){
			$letter = mb_substr($item["NAME"], 0, 1, 'utf8');
			$this->arResult[$code][$k]["LETTER"] = $letter;
		    $alphabet[] = $letter;
		}
		$this->arResult["{$code}_ALPHABET"] = array_unique($alphabet);
	}

	function executeComponent(){
        $this->arResult["SECTIONS"] = $this->Sections->getData();
        $this->arResult["SHOPS"] = $this->Shops->getData();
        $this->prepareAlphabet("SECTIONS");
        $this->prepareAlphabet("SHOPS");
		$this->IncludeComponentTemplate();
	}
}
