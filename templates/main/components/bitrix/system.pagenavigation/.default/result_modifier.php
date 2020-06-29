<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult["ITEMS"] = array();

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");

for($i = $arResult["nStartPage"]; $i <= $arResult["nEndPage"]; $i++){
	$active = ( $i == $arResult["NavPageNomer"] );
	$url = $arResult["sUrlPath"] . "?" . $strNavQueryString . "PAGEN_" . $arResult["NavNum"] . "=" . $i;
	$arResult["ITEMS"][] = array(
		"NUM" => $i,
		"ACTIVE" => $active,
		"URL" => $url,
	);
}