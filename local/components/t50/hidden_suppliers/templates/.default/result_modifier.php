<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$chunkSize = ceil(count($arResult["SUPPLIERS"]) / 3);
$arResult["SUPPLIERS_CHUNKS"] =  array_chunk($arResult["SUPPLIERS"], ( $chunkSize < 1 ? 1 : $chunkSize ), true);

