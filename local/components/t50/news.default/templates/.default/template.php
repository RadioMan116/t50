<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="grid-12__container">
	<div class="page__item">
		<?T50Html::inc("search_model", ["targetBlank" => true])?>
	</div>
	<div class="page__item">
		<div class="panel">
			<div class="panel__head">
				<div class="panel__head-main">
					<h1 class="panel__title">Новости</h1>
				</div>
			</div>
			<?include __DIR__ . "/include/form.php"?>
		</div>
	</div>
	<?
	T50Html::incPath(__DIR__ . "/include/news.php", [
		"TITLE" => "Закрепленная новость",
		"TYPE" => "FIXED",
		"CAN_EDIT" => $arResult["CAN_EDIT"],
		"ITEMS" => $arResult["NEWS_FIXED"]["ITEMS"],
		"NAV" => $arResult["NEWS_FIXED"]["NAV"],
	], false);
	?>
	<?
	T50Html::incPath(__DIR__ . "/include/news.php", [
		"TITLE" => "Непрочитанные новости",
		"TYPE" => "UNREAD",
		"CAN_EDIT" => $arResult["CAN_EDIT"],
		"ITEMS" => $arResult["NEWS_UNREAD"]["ITEMS"],
		"NAV" => $arResult["NEWS_UNREAD"]["NAV"],
	], false);
	?>
	<?
	T50Html::incPath(__DIR__ . "/include/news.php", [
		"TITLE" => "Прочитанные новости",
		"TYPE" => "READ",
		"CAN_EDIT" => $arResult["CAN_EDIT"],
		"ITEMS" => $arResult["NEWS_BY_FILTER"]["ITEMS"],
		"NAV" => $arResult["NEWS_BY_FILTER"]["NAV"],
	], false);
	?>
</div>
<?
// echo "<pre>\$arResult "; print_r($arResult); echo "</pre>";
?>