<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

AddEventHandler("main", "OnProlog", "loadBootConfig");

function loadBootConfig(){	
	require_once dirname(__FILE__) . "/../boot_config.php";
}
?>