<?php
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if( !Bitrix\Main\Context::getCurrent()->getRequest()->isAjaxRequest() && DBG !== true )
	return;

$componentName = htmlspecialchars($_GET["comp"]);
$action = htmlspecialchars($_GET["action"]);
if( !isset($componentName) || !isset($action) )
	return;

$params = [];

$controller = new Agregator\Components\ControllerAjaxComponent($componentName);
print $controller->executeAction($action, $params);
