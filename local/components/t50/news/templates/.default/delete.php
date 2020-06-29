<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Agregator\Manager\JSON\ManagerNews;

foreach($arResult["VARIABLES"] as $code => $val)
	$arParams[$code] = $val;

$managerNews = new ManagerNews();
$el = new CIblockElement;

$newsRedirectAfterDelete = function (bool $successDelete) use($arParams){
	$link = "/news/edit/" . $arParams["CODE"] . "/";
	if( $successDelete )
		$link = "/news/";
	LocalRedirect($link);
	exit();
};

T50DB::startTransaction();
if( !$el->delete($arParams["ID"]) ){
	T50DB::rollback();
	$newsRedirectAfterDelete(false);
}

$removeFromUnread = $managerNews->setReadAndUnreadForManagers(
	$arParams["ID"],
	array_keys(T50GlobVars::get("MANAGERS")), []
);
if( !$removeFromUnread ){
	T50DB::rollback();
	$newsRedirectAfterDelete(false);
}

T50DB::commit();
$newsRedirectAfterDelete(true);