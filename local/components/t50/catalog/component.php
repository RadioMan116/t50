<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Agregator\Product\Shop;
use Agregator\Product\Category;

$arDefaultUrlTemplates404 = array(
	"defalut" => "",
	"shop" => "#SHOP_CODE#/",
	"section" => "#SHOP_CODE#/#SECTION_CODE#/",
	"element" => "#SHOP_CODE#/#SECTION_CODE#/#ELEMENT_CODE#.html",
);

$arDefaultVariableAliases404 = array();

$arDefaultVariableAliases = array();

$arComponentVariables = array();


$SEF_FOLDER = "/catalog/";
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

CComponentEngine::InitComponentVariables($componentPage,
											$arComponentVariables,
											$arVariableAliases,
											$arVariables);


// validate variables
$variables = ["SHOP_CODE", "SECTION_CODE", "ELEMENT_CODE"];
foreach($variables as $variable){
	$arVariables[$variable] = preg_replace("#[^a-zA-Z-_0-9]#", "", $arVariables[$variable]);
}

######################

$arResult = array(
	"FOLDER" => $SEF_FOLDER,
	"URL_TEMPLATES" => $arUrlTemplates,
	"VARIABLES" => $arVariables
);

if( !empty($arVariables["SHOP_CODE"]) && $arVariables["SHOP_CODE"] != "all_shops" ){
	$shop = Shop::elements()
		->filter(["=CODE" => $arVariables["SHOP_CODE"]])
		->props("OFFICIAL_NAME", "BRANDS", "CATEGORIES", "CITIES")
		->select("NAME", "PREVIEW_PICTURE", "DETAIL_TEXT")
		->getOneFetch();

	if( !isset($shop) ){
		@define("ERROR_404", "Y");
		CHTTP::SetStatus("404 Not Found");
		return;
	}

	$allCategories = T50ArrayHelper::pluck(T50GlobVars::get("CACHE_CATEGORIES"), "NAME");
	$shop["PROPERTY_CATEGORIES_VALUE"] = T50ArrayHelper::filterByKeys($allCategories, $shop["PROPERTY_CATEGORIES_VALUE"]);

	$shop["PREVIEW_PICTURE"] = CFile::getPath($shop["PREVIEW_PICTURE"]);
	if( empty($shop["PROPERTY_OFFICIAL_NAME_VALUE"]) )
		$shop["PROPERTY_OFFICIAL_NAME_VALUE"] = $shop["NAME"];

	$arParams["SHOP"] = $shop;
	$APPLICATION->SetTitle('Каталог - ' . $shop["NAME"]);
}

if( !empty($arVariables["SECTION_CODE"]) ){
	$category = Category::elements()
		->filter(["=CODE" => $arVariables["SECTION_CODE"]])
		->select("NAME")
		->getOneFetch();

	if( !isset($category) || (isset($shop) && !isset($shop["PROPERTY_CATEGORIES_VALUE"][$category["ID"]])) ){
		@define("ERROR_404", "Y");
		CHTTP::SetStatus("404 Not Found");
		return;
	}

	$arParams["CATEGORY"] = $category;
	$APPLICATION->SetTitle('Каталог - ' . $shop["NAME"] . " - " . $category["NAME"]);
}

$this->IncludeComponentTemplate($componentPage);
?>