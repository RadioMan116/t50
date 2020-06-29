<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

AddEventHandler('main', 'OnEpilog', 'showErrorPage');

function showErrorPage() {
	if( defined('ERROR_404') && ERROR_404 == 'Y') {
		include $_SERVER["DOCUMENT_ROOT"].'/.error_pages/404.php';
		return false;
	}

	if( defined('ERROR_403') && ERROR_403 == 'Y') {
		include $_SERVER["DOCUMENT_ROOT"].'/.error_pages/access_denied.php';
		return false;
	}
}