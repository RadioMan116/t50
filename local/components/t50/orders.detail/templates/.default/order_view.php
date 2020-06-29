<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="modal modal_size_big" id="order_view">
	<h3 class="modal__title modal__title_align_center">Редактирование внешнего вида заказа</h3>
	<div class="modal__content">
		<!-- begin .form-->
		<form class="form" action="" method="post">
			<input type="hidden" name="order_view" value="send">
			<?=bitrix_sessid_post()?>
			<div class="grid-12__row">
				<div class="grid-12__col grid-12__col_size_3">
					<div class="check-elem">
						<?=T50Html::radioLabel("type", "ALL",
							["cls" => "check-elem__input", "val" => $arResult["ORDER_VIEW"]["TYPE"]],
							"Выводить блоками", "check-elem__label"
						);?>
					</div>
				</div>

				<div class="grid-12__col grid-12__col_size_3">
					<div class="check-elem">
						<?=T50Html::radioLabel("type", "TABS",
							["cls" => "check-elem__input", "val" => $arResult["ORDER_VIEW"]["TYPE"]],
							"Выводить вкладками", "check-elem__label"
						);?>
					</div>
				</div>
			</div>

			<hr class="form__separator" />

			<div class="grid-12__row">
			<?
				$counter = 0;
				foreach($arResult["ORDER_VIEW"]["BLOCKS"] as $items){
			?>
				<div class="grid-12__col grid-12__col_size_4">
					<? foreach($items as $item){ ?>
						<div class="form__vgroup">
							<label class="form__line">
								<div class="form__label">Блок <?= ++$counter?></div>
								<?=T50Html::select("blocks[{$item['code']}]", $arResult["ORDER_VIEW"]["NAMES"],
									[
										"cls" => "js-select modal__select",
										"val" => $item["code"],
										( $item["code"] == "basket" ? "dis" : "" )
									]
								);?>
							</label>
							<div class="check-elem">
								<?=T50Html::checkboxLabel("ignore_tabs[{$item['code']}]", "Y",
									["cls" => "check-elem__input", "val" => ( $item["notab"] ? "Y" : "" )],
									"Игнорировать вид вкладками", "check-elem__label"
								);?>
							</div>
						</div>
					<? } ?>
				</div>
			<? } ?>
			</div>


			<div class="form__controls form__controls_align_center modal__controls modal__controls_style_separate">
				<div class="form__control modal__control">
					<button class="button button_width_full button_type_concrete" type="submit">Сохранить</button>
				</div>

				<div class="form__control modal__control">
					<button class="button button_style_default button_width_full button_type_concrete" type="submit" name="reset">Сбросить по-умолчанию</button>
				</div>
			</div>

		</form>
	</div>
</div>