<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arDefaultUrlTemplates404 = array(
	"defalut" => "",
	"create" => "create/",
	"claims" => "claims/",
	"detail" => "#ORDER_ID#/",
	"docs" => "#ORDER_ID#/docs/#DOC_TEMPLATE#/",
);

$arDefaultVariableAliases404 = array();

$arDefaultVariableAliases = array();

$arComponentVariables = array();


$SEF_FOLDER = "/orders/";
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

### COMMON CODE ######
\Bitrix\Main\Loader::includeModule('highloadblock');
$arVariables["ORDER_ID"] = (int) $arVariables["ORDER_ID"];
$arVariables["DOC_TEMPLATE"] = htmlspecialchars($arVariables["DOC_TEMPLATE"]);

######################

$arResult = array(
	"FOLDER" => $SEF_FOLDER,
	"URL_TEMPLATES" => $arUrlTemplates,
	"VARIABLES" => $arVariables
);

$this->IncludeComponentTemplate($componentPage);
?>