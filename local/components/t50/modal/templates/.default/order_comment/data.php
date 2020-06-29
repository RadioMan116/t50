<?php
use rat\agregator\OrderComment;
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$themes = OrderComment::getEnum("UF_THEME", false);
$arResult["THEMES"] = array();
foreach($themes as $item){
    $arResult["THEMES"][] = array(
    	"val" => $item["id"],
    	"title" => $item["val"],
    );
}