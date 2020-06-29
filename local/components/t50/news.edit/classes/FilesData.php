<?php

namespace NewsEditComponent;

use Agregator\IB\Element;
use Agregator\IB\Elements;
use T50GlobVars;
use CIBlockElement;
use CFile;

class FilesData
{
	private $updater;

	function __construct($arParams){
		$this->arParams = $arParams;
		$this->updater = new Element("news");
	}

	function loadByArticleId(int $articleId, $resolve = true){
		$data = (new Elements("news"))
			->props("FILES")
			->getOneFetchById($articleId);

		$files = array_combine($data["PROPERTY_FILES_PROPERTY_VALUE_ID"], $data["PROPERTY_FILES_VALUE"]);
		return ( $resolve ? $this->resolveIds($files) : $files ) ;
	}

	function saveToArticle(int $articleId, array $fileUploadData){
		return CIBlockElement::SetPropertyValueCode($articleId, "FILES", $fileUploadData);
	}

	function deleteFromArticle(int $articleId, int $filePropId){
		return CIBlockElement::SetPropertyValueCode($articleId, "FILES", [$filePropId => ["del" => "Y"]]);
	}

	function resolveIds(array $files){
		$arResult = array();
		foreach($files as $propId => $fileId){
			$fileInfo = CFile::GetFileArray($fileId);
			$arResult[] = array(
				"ID" => $fileInfo["ID"],
				"PROP_ID" => $propId,
				"TITLE" => $fileInfo["ORIGINAL_NAME"],
				"PATH" => $fileInfo["SRC"],
			);
		}
		return $arResult;
	}
}