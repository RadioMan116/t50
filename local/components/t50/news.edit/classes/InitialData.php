<?php

namespace NewsEditComponent;

use Agregator\IB\Element;
use Agregator\IB\Elements;
use T50GlobVars;
use Bitrix\Main\GroupTable;
use Agregator\Manager\JSON\ManagerNews;
use Agregator\Components\Traits\ComponentDI;

class InitialData
{
	use ComponentDI;

	function getData(){
		if( $this->arParams["IS_NEW"] ){
			$id = $this->getIdForNew();
		} else {
			$id = (int) $this->arParams["ID"];
		}

		if( $id <= 0 )
			return false;

		return $this->_getData($id);
	}

	private function _getData(int $id){
		$data = (new Elements("news"))
			->select(
				"NAME", "CODE", "DETAIL_TEXT", "PREVIEW_TEXT",
				"CREATED_BY", "MODIFIED_BY", "TIMESTAMP_X", "DATE_CREATE"
			)
			->props("TARGET_MAN_GROUPS", "FILES", "FIX_IN_HEADER", "THEME", "BRAND")
			->getOneFetchById($id);

		if( empty($data) )
			return false;

		$data["BRANDS"] = T50GlobVars::get("CACHE_BRAND_NAMES");
		$data["THEMES"] = $this->getThemes();
		$data["GROUPS"] = (new ManagerNews)->getGroups();

		$data["DATE_MODIFY"] = substr($data["TIMESTAMP_X"], 0, 10);
		$data["SHOW_LAST_MODIFY_INFO"] = $this->needShowLastModifyInfo($data);

		$data["DELETE_LINK"] = $this->getDeleteLink($data);

		$files = array_combine($data["PROPERTY_FILES_PROPERTY_VALUE_ID"], $data["PROPERTY_FILES_VALUE"]);
		$data["FILES"] = $this->FilesData->resolveIds($files);

		$managers = T50GlobVars::get("MANAGERS");
		$data["CREATED_BY"] = $managers[$data["CREATED_BY"]];
		$data["MODIFIED_BY"] = $managers[$data["MODIFIED_BY"]];

		return $data;
	}

	private function getDeleteLink(array $data){
		if( $this->arParams["IS_NEW"] )
			return null;

		return "/news/delete/" . $data["CODE"] . "/";
	}

	private function needShowLastModifyInfo(array $data){
		if( $this->arParams["IS_NEW"] )
			return false;

		return ( $data["TIMESTAMP_X"] != $data["DATE_CREATE"] );
	}

	private function getThemes(){
		$themes = T50GlobVars::get("LPROPS_NEWS")["THEME"];
		return array_flip($themes["VALUES"]);
	}

	private function getIdForNew(){
		global $USER;
		$login = $USER->getLogin();
		$draftCode = "draft_for_{$login}";
		$id = (int) (new Elements("news"))
			->filter(["CREATED_BY" => $USER->getId(), "ACTIVE" => "N"])
			->setColumn("ID")
			->getOneFetch();

		if( $id > 0 )
			return $id;

		return (new Element("news"))->create([
			"ACTIVE" => "N",
			"NAME" => "Черновик для пользователя {$login}",
			"CODE" => $draftCode
		]);

	}
}