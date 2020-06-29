<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$page = $this->getComponent()->GetTemplatePage();

foreach($arResult["VARIABLES"] as $code => $val)
	$arParams[$code] = $val;

$componentName = "t50:reports." . $arParams["REPORT"];

$componentPath = getLocalPath("components" . CComponentEngine::MakeComponentPath($componentName));
if( !CComponentUtil::isComponent($componentPath) ){
	@define("ERROR_404", "Y");
	CHTTP::SetStatus("404 Not Found");
	return;
}

$report = $APPLICATION->IncludeComponent($componentName, "", $arParams, $component);

$title = ( property_exists($report, 'reportTitle') ? $report->reportTitle : "Отчет" );

$APPLICATION->setTitle($title);