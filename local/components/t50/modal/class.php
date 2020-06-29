<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class ModalComponent extends \CBitrixComponent
{

	function executeComponent(){
		$templates = $_SERVER["DOCUMENT_ROOT"] . $this->getPath() . "/templates/";
		$subTemplateFolder = "/" . $this->arParams["modal"];
		$this->arResult["TEMPLATE_PATH"] =  $subTemplateFolder . "/template.php";
		$this->arResult["DATA_PATH"] = $subTemplateFolder . "/data.php";
		$this->arResult["JS_PATH"] =  $subTemplateFolder . "/script.js";
		$this->arResult["CSS_PATH"] =  $subTemplateFolder . "/style.css";

		$this->IncludeComponentTemplate();
	}
}
