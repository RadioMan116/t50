<?php

namespace NewsEditComponent;

use Agregator\IB\Element;
use Agregator\IB\Elements;
use T50GlobVars;
use Bitrix\Main\GroupTable;
use Agregator\Manager\JSON\ManagerNews;
use T50DB;
use T50Text;

class Storage
{
	function save(int $id, \StdClass $input){
		T50DB::startTransaction();

		$managerNews = new ManagerNews();
		if( !$managerNews->addUnreadForManagerGroups($id, $input->group) )
			return T50DB::rollback();

		$element = new Element("news");

		$code = $this->arParams["CODE"];
		if( $this->arParams["IS_NEW"] )
			$code = strtolower(T50Text::translit($input->title, "_"));

		$fields = [
			"NAME" => $input->title,
			"ACTIVE" => "Y",
			"CODE" => $code,
			"DETAIL_TEXT" => $input->text,
			"PREVIEW_TEXT" => $input->comment,
		];
		if( $this->arParams["IS_NEW"] )
			$fields["DATE_CREATE"] = date("d.m.Y");

		$props = [
			"BRAND" => $input->brand,
			"THEME" => $input->theme,
			"TARGET_MAN_GROUPS" => $input->group,
			"FIX_IN_HEADER" => $this->prepareFixInHeader($input->fix_in_header),
		];

		$success = $element->update($id, $fields, $props);

		if( $success ){
			T50DB::commit();
			return $code;
		}

		return T50DB::rollback();
	}

	private function prepareFixInHeader($value){
		if( $value !== true )
			return false;

		$enumValues = T50GlobVars::get("LPROPS_NEWS")["FIX_IN_HEADER"]["VALUES"];
		return $enumValues["Y"];
	}
}