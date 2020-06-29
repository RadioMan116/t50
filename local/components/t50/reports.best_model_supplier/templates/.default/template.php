<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="grid-12__container grid-12__container_width_full">
	<div class="page__item">
		<?T50Html::inc("search_model", ["targetBlank" => true])?>
	</div>

	<?include __DIR__ . "/include/form.php"?>

	<?include __DIR__ . "/include/data.php"?>
</div>
