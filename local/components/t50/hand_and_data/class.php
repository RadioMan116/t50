<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Agregator\Manager\Manager;

class HandAndDataCompoment extends \CBitrixComponent
{
	function executeComponent(){
		$this->arResult["IS_EDITABLE"] = $this->isEditable();

		$this->prepareValue("VALUE");
		$this->prepareValue("AUTO_VALUE");
		$this->prepareCommentInfo();
		$this->prepareDataForModal();

		$this->arResult["TOOLTIP_ID"] = T50Html::uniqStr("tooltip" . ($this->arParams["IS_AJAX"] ? "_ajax" : ""));
		$this->IncludeComponentTemplate();
	}

	private function prepareDataForModal(){
		$autoValue = $this->arResult["AUTO_VALUE"];
		$ajax_id = implode("_", T50ArrayHelper::filterByKeys($this->arParams, ["KEY", "BIND_ID", "PRODUCT_ID"]));
		$this->arResult["DATA_ATTRS"] = T50Html::dataAttrs([
			"id" => $this->arParams["PRODUCT_ID"],
			"modal_key" => $this->arParams["KEY"],
			"bind_id" => $this->arParams["BIND_ID"],
			"city" => $this->arParams["CITY"],
			"ajax_id" => $ajax_id,
			"value" => $this->arParams["AUTO_VALUE"],
		]);
		$this->arResult["AJAX_ID"] = $ajax_id;
	}

	private function prepareCommentInfo(){
		$commentInfo = $this->arParams["COMMENT"][$this->arParams["KEY"]] ?? [];

		foreach($commentInfo as $code => $value)
		    $this->arResult[$code] = $value;

		if( isset($this->arResult["DATE_CREATE"]) )
			$this->arResult["DATE"] = $this->arResult["DATE_CREATE"];

		if( isset($this->arResult["DATE_RESET"]) )
			$this->arResult["DATE"] .= " - " . $this->arResult["DATE_RESET"];
	}

	private function prepareValue($code){
		$value = $this->arParams[$code];
		if( in_array($this->arParams["KEY"], ["avail_supplier", "avail_shop"]) ){
			$this->arResult["TYPE_AVAIL"] = true;
			$this->arResult[$code] = $this->getAvailTextAndClass($value);
		} else {
			$this->arResult[$code] = T50HTML::fnum($value);
		}
	}

	private function isEditable(){
		static $isEditable;
		$isEditable = $isEditable ?? Manager::canChangeProductCard();
		return $isEditable;
	}

	private function getAvailTextAndClass($availValue){
		static $data = array(
			AVAIL_IN_STOCK => ["В наличии", "status_style_in-stock"],
			AVAIL_BY_REQUEST => ["Под заказ", "status_style_under-order"],
			AVAIL_OUT_OF_STOCK => ["Нет в наличии", "status_style_ended"],
			AVAIL_DISCONTINUED => ["Снят с производства", "status_style_ended"],
		);

		$result = new StdClass;
		$result->text = $result->class = "";

		if( !isset($data[$availValue]) )
			return $result;

		list($result->text, $result->class) = $data[$availValue];
		$result->val = $availValue;
		return $result;
	}
}
