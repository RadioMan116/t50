<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?>

<div class="filter-panel__content filter-panel__content_state_editable">

	<div class="panel__subtitle">
		<div class="panel__title panel__title_type_subtitle">Поставщики товаров</div>
	</div>

	<div class="filter-panel__list">
		<div class="showcase showcase_cols_xs-12">
			<div class="showcase__list">
				<div class="showcase__item">
				<?foreach($arResult["INITIAL_DATA"]["PROVIDER"] as $index => $item){?>

					<div class="filter-panel__item">
						<div class="check-elem">
							<?=T50Html::checkboxLabel(
								"supplier[]", $item["ID"],
								"i",
								$item["NAME"], "l"
							);?>
						</div>
					</div>

					<? if( ($index + 1) % 4 == 0 ):?></div><div class="showcase__item"><? endif; ?>

				<? } ?>
				</div>
			</div>
		</div>
	</div>

	<div class="panel__subtitle">
		<div class="panel__title panel__title_type_subtitle">Поставщики услуг</div>
	</div>

	<div class="filter-panel__list">
		<div class="showcase showcase_cols_auto">
			<div class="showcase__list">
				<div class="showcase__item">
				<?foreach($arResult["INITIAL_DATA"]["SERVICE_PROVIDER"] as $id => $title){?>
					<div class="filter-panel__item">
						<div class="check-elem">
							<?=T50Html::checkboxLabel(
								"provider[]", $id,
								"i",
								$title, "l"
							);?>
						</div>
					</div>
				<? } ?>
				</div>
			</div>
		</div>
	</div>
</div>