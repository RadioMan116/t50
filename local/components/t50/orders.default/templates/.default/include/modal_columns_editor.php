<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="modal modal_size_middle" id="columns_editor">
	<h3 class="modal__title modal__title_align_center">Редактирование таблицы заказов</h3>
	<div class="modal__content">
		<form class="form" action="" method="post">
			<?=bitrix_sessid_post()?>
			<input type="hidden" name="columns_editor" value="send">
			<div class="grid-12__row">
				<div class="grid-12__col grid-12__col_size_6">
				<?for( $i = 0; $i < 24; $i++ ){?>
					<? if( $i > 0 && $i % 12 == 0 ){ ?>
					    </div>
					    <div class="grid-12__col grid-12__col_size_6">
					<? } ?>
					<label class="form__line">
						<div class="dual-panel dual-panel_width_auto">
							<div class="dual-panel__row">
								<div class="dual-panel__col dual-panel__col_type_close">
									<span class="form__label form__label_type_close form__label_type_concrete">Столбец <?=($i + 1)?></span>
								</div>
								<div class="dual-panel__col dual-panel__col_type_close">
									<span class="form__field form__field_size-xl_m">
										<?=T50Html::select(
											"columns[]",
											$arResult["TABLE_VIEW"]["MAP"],
											[
												"cls" => "js-select modal__select",
												"empty" => "-",
												"val" => $arResult["TABLE_VIEW"]["COLUMNS"][$i]
											]
										);?>
									</span>
								</div>
							</div>
						</div>
					</label>
				<? } ?>
				</div>
			</div>
			<div class="form__controls form__controls_align_center modal__controls modal__controls modal__controls_space_top">
				<div class="form__control modal__control">
					<button class="button button_width_full button_type_concrete" type="submit">Сохранить</button>
				</div>
				<div class="form__control modal__control">
					<button class="button button_style_default button_width_full button_type_concrete" type="submit" name="reset" value="Y">Сбросить по-умолчанию</button>
				</div>
			</div>
		</form>
	</div>
</div>