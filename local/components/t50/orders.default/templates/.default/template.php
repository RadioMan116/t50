<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<div class="grid-12__container grid-12__container_width_full">
	<div class="page__item">
		<?T50Html::inc("search_model", ["targetBlank" => true])?>
	</div>
	<div class="page__item">
		<?include __DIR__ . "/include/filter.php"?>
	</div>
	<div class="page__item">
		<?include __DIR__ . "/include/orders_list.php"?>
	</div>
	<div class="page__item">
		<?include __DIR__ . "/include/orders_total.php"?>
	</div>
</div>

<? include __DIR__ . "/include/modal_columns_editor.php" ?>
<?$APPLICATION->IncludeComponent("t50:modal", "", ["modal" => "create_new_order"]);?>
<?

// echo "<pre>\$arResult "; print_r($arResult); echo "</pre>";
?>

<script type="text/javascript">
	window.component_data = <?=CUtil::PhpToJSObject($arResult["DATA_FOR_JS"] ?? [])?>;
</script>