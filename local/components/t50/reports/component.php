<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
\Bitrix\Main\Loader::includeModule('iblock');

$arDefaultUrlTemplates404 = array(
	"root" => "index.php",
	"report" => "#REPORT#/",
);

$SEF_FOLDER = "/reports/";

$arDefaultVariableAliases404 = array();
$arComponentVariables = array();

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

CComponentEngine::InitComponentVariables(
	$componentPage, $arComponentVariables, $arVariableAliases, $arVariables);


$arResult = array(
	"FOLDER" => $SEF_FOLDER,
	"URL_TEMPLATES" => $arUrlTemplates,
	"VARIABLES" => $arVariables
);

$this->IncludeComponentTemplate($componentPage);