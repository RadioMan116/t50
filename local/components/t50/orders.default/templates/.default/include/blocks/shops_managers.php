<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="filter-panel__content filter-panel__content_state_editable">


	<div class="js_gr_checked">
		<div class="panel__subtitle">
			<div class="panel__title panel__title_type_subtitle">Магазины</div>
		</div>

		<ul class="filter-panel__types">
			<li class="filter-panel__type">
				<div class="link link_style_default-trigger js_gr_checked__tigger" data-group="all">Выбрать все</div>
			</li>
			<?foreach($arResult["INITIAL_DATA"]["SHOP"]["groups"] as $group => $title){?>
				<li class="filter-panel__type">
					<div class="link link_style_default-trigger js_gr_checked__tigger" data-group="<?=$group?>"><?=$title?></div>
				</li>
			<? } ?>
		</ul>

		<div class="filter-panel__list">
			<div class="showcase showcase_cols_xs-12">
				<div class="showcase__list">
					<div class="showcase__item">
					<?foreach($arResult["INITIAL_DATA"]["SHOP"]["items"] as $index => $item){?>

						<div class="filter-panel__item">
							<div class="check-elem">
								<?=T50Html::checkboxLabel(
									"shop[]", $item["ID"],
									["cls" => "i", "data" => ["group" => $item["GROUP"]]],
									$item["NAME"], "l"
								);?>
							</div>
						</div>

						<? if( ($index + 1) % 2 == 0 ):?></div><div class="showcase__item"><? endif; ?>

					<? } ?>
					</div>
				</div>
			</div>
		</div>
	</div>




	<div class="js_gr_checked">
		<div class="panel__subtitle">
			<div class="panel__title panel__title_type_subtitle">Менеджеры</div>
		</div>
		<ul class="filter-panel__types">
			<li class="filter-panel__type">
				<div class="link link_style_default-trigger js_gr_checked__tigger" data-group="all">Выбрать все</div>
			</li>
			<?foreach($arResult["INITIAL_DATA"]["MANAGER"]["groups"] as $group => $title){?>
				<li class="filter-panel__type">
					<div class="link link_style_default-trigger js_gr_checked__tigger" data-group="<?=$group?>"><?=$title?></div>
				</li>
			<? } ?>
		</ul>

		<div class="filter-panel__list">
			<div class="showcase showcase_cols_xs-9">
				<div class="showcase__list">
					<div class="showcase__item">
						<?foreach($arResult["INITIAL_DATA"]["MANAGER"]["items"] as $index => $item){?>
							<div class="filter-panel__item">
								<div class="check-elem">
									<?=T50Html::checkboxLabel(
										"manager[]", $item["ID"],
										["cls" => "i", "data" => ["group" => $item["GROUP"]]],
										$item["NAME"] , "l"
									);?>
								</div>
							</div>

							<? if( ($index + 1) % 2 == 0 ):?></div><div class="showcase__item"><? endif; ?>

						<? } ?>
					</div>
				</div>
			</div>
		</div>
	</div>




	<div class="panel__subtitle">
		<div class="panel__title panel__title_type_subtitle">Закупщики</div>
	</div>
	<div class="filter-panel__list">
			<div class="showcase showcase_cols_xs-3">
				<div class="showcase__list">
					<div class="showcase__item">
						<?foreach($arResult["INITIAL_DATA"]["PURCHASE_MANAGER"] as $index => $item){?>
							<div class="filter-panel__item">
								<div class="check-elem">
									<?=T50Html::checkboxLabel(
										"manager[]", $item["ID"],
										["cls" => "i", "data" => ["group" => $item["GROUP"]]],
										$item["NAME"] , "l"
									);?>
								</div>
							</div>

							<? if( ($index + 1) % 2 == 0 ):?></div><div class="showcase__item"><? endif; ?>

						<? } ?>
					</div>
				</div>
			</div>
		</div>
</div>