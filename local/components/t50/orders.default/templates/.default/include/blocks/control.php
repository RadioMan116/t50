<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="filter-panel__content filter-panel__content_state_editable">
	<div class="filter-panel__list">
		<div class="showcase showcase_cols_xs-12">
			<div class="showcase__list">

				<div class="showcase__item">
					<div class="form__label form__label_type_marked">Рекламация</div>
					<?foreach($arResult["INITIAL_DATA"]["COMPLAINT"] as $code => $title){?>
						<div class="filter-panel__item">
							<div class="check-elem">
								<?=T50Html::checkboxLabel(
									"complaint", $code,
									"i",
									$title, "l"
								);?>
							</div>
						</div>
					<? } ?>
				</div>


				<div class="showcase__item">
					<div class="form__label form__label_type_marked">Тест</div>
					<?foreach(["Y" => "да", "N" => "нет", "" => "не важно"] as $code => $title){?>
						<div class="filter-panel__item">
							<div class="check-elem">
								<?=T50Html::radioLabel(
									"test", $code,
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