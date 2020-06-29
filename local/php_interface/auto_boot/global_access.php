<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

AddEventHandler("main", "OnBeforeProlog", ["GlobalAccess", "check"]);

class GlobalAccess
{
	static function check(){
		global $APPLICATION;
		$curUrl = $APPLICATION->getCurPage(false);
		$curFullUrl = $APPLICATION->GetCurPageParam();
		$authUrl = "/auth/";
		if( $curUrl != $authUrl ){
			if( self::needAuth($curFullUrl) ){
				define("NEED_AUTH", true);
			}
		}
		self::checkCsrf($curUrl);
	}

	static function needAuth($curUrl){
		if( php_sapi_name() === "cli" )
			return false;

		if( self::isRemoteRequest($curUrl) )
			return false;

		global $USER;
		if( $USER->isAdmin() )
			return false;

		$groups = T50ArrayHelper::remItem($USER->GetUserGroupArray(), 2);
		if( !empty($groups) )
			return false;

		if( $USER->isAuthorized() ){
			include $_SERVER["DOCUMENT_ROOT"].'/.error_pages/access_denied.php';
			die();
		}

		if( substr($curUrl, 0, 14) == "/bitrix/admin/" )
			LocalRedirect("/auth/?backurl=" . urlencode($curUrl));

		return true;
	}

	static function checkCsrf($curUrl){
		if( empty($_POST) )
			return;

		if( self::isRemoteRequest($curUrl) )
			return;

		$sessid = $_REQUEST["sessid"];
		if( empty($sessid) )
			$sessid = $_SERVER["HTTP_X_CSRF_TOKEN"];

		if( $sessid != bitrix_sessid() ){
			CHTTP::SetStatus("403 Forbidden");
			die();
		}
	}

	private static function isRemoteRequest($curUrl){
		return ( TRUSTED_REMOTE_ACCESS === true );
	}
}


