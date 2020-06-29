<?php
define("CLI_FOLDER_ROOT", dirname(__FILE__));
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
set_time_limit(0);
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__)."/../..");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");