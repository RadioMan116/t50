<?php

if (class_exists("rat_agregator"))
	return;

class rat_agregator extends CModule
{
	var $MODULE_ID = "rat.agregator";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_SORT = 0;
	var $MODULE_DESCRIPTION;
	//var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "N";
	var $PARTNER_NAME;
	var $PARTNER_URI;
	 

	function rat_agregator()
	{
		$arModuleVersion = array();
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = $arModuleVersion["MODULE_NAME"];
		$this->MODULE_DESCRIPTION = $arModuleVersion["MODULE_DESCRIPTION"];
		$this->MODULE_SORT = $arModuleVersion["MODULE_SORT"];
		$this->PARTNER_NAME = $arModuleVersion["PARTNER_NAME"];		
		$this->PARTNER_URI = $arModuleVersion["PARTNER_URI"];		
	}

	function GetModuleTasks()
	{
		return array();
	}

	function DoInstall($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		//$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/agregator/install/db/".strtolower($DB->type)."/install.sql");
		//$APPLICATION->ThrowException(implode("<br>", $this->errors));
		$this->InstallTasks();
		RegisterModule("rat.agregator");
	
		return true;
	}

	function DoUninstall($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		//$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/agregator/install/db/".strtolower($DB->type)."/install.sql");
		//$APPLICATION->ThrowException(implode("<br>", $this->errors));
		$this->InstallTasks();
		UnRegisterModule("rat.agregator");
	
		return true;
	}
	
}
