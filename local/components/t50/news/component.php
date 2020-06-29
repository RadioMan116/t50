<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
\Bitrix\Main\Loader::includeModule('iblock');
use Agregator\IB\Elements;
use Agregator\Manager\Manager;

$arDefaultUrlTemplates404 = array(
	"defalut" => "",
	"add" => "add/",
	"edit" => "edit/#CODE#/",
	"delete" => "delete/#CODE#/",
);

$arDefaultVariableAliases404 = array();

$arDefaultVariableAliases = array();

$arComponentVariables = array();


$SEF_FOLDER = "/news/";
$arUrlTemplates = array();

$arVariables = array();

$arUrlTemplates =
        CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, array());
$arVariableAliases =
        CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, array());


$componentPage = CComponentEngine::ParseComponentPath(
	$SEF_FOLDER,
	$arUrlTemplates,
	$arVariables
);

if( !$componentPage ){
	$componentPage = "default";
	if( $SEF_FOLDER != $APPLICATION->GetCurPage(false) ){
		@define("ERROR_404", "Y");
		CHTTP::SetStatus("404 Not Found");
		return;
	}
}

CComponentEngine::InitComponentVariables(
	$componentPage, $arComponentVariables, $arVariableAliases, $arVariables);


### COMMON ######

$code = $arVariables["CODE"];
if( !empty($code) ){

	if( !Manager::canWorkWithNews() ){
		CHTTP::SetStatus("403 Forbidden");
		include $_SERVER["DOCUMENT_ROOT"] . "/.error_pages/access_denied.php";
		return;
	}

	$news = new Elements("news");
	$id = (int) $news->filter(["=CODE" => $code])->setColumn("ID")->getOneFetch();
	if( $id <= 0 ){
		@define("ERROR_404", "Y");
		CHTTP::SetStatus("404 Not Found");
		return;
	}
	$arVariables["ID"] = $id;
}


######################

$arResult = array(
	"FOLDER" => $SEF_FOLDER,
	"URL_TEMPLATES" => $arUrlTemplates,
	"VARIABLES" => $arVariables
);

$this->IncludeComponentTemplate($componentPage);
?>