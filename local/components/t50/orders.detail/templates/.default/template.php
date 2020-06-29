<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$templateType =  ( $arResult["ORDER_VIEW"]["TYPE"] == "TABS" ? "tabs" : "all" );
include __DIR__ . "/template_{$templateType}.php";
include __DIR__ . "/order_view.php";
?>
<script type="text/javascript">
	window.ORDERS_OUTER_CONFIG = <?=CUtil::PhpToJSObject([
		"TEMPLATE_TYPE" => $arResult["ORDER_VIEW"]["TYPE"]
	]);?>
</script>
<?$APPLICATION->IncludeComponent("t50:modal", "", ["modal" => "manual"]);?>
<?$APPLICATION->IncludeComponent("t50:modal", "", ["modal" => "installation_prices"]);?>
<?$APPLICATION->IncludeComponent("t50:modal", "", ["modal" => "order_comment"]);?>
<?$APPLICATION->IncludeComponent("t50:modal", "", ["modal" => "email_editor"]);?>