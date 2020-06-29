<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

AddEventHandler("main", "OnAfterUserUpdate", Array("UserEvents", "OnAfterUserUpdate"));

class UserEvents
{
	static function OnAfterUserUpdate($arFields){
		T50GlobCache::getRedis()->del("USER_" . $arFields["ID"]);
	}
}