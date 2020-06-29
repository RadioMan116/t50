<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

require_once __DIR__ . "/global_access.php";

$arFiles = glob(__DIR__ . "/{helpers,events}/*.php", GLOB_BRACE);

foreach($arFiles as $filePath)
	include_once($filePath);

?>