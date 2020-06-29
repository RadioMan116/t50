<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="filter-panel__content filter-panel__content_state_editable">
	<div class="filter-panel__list">

		<div class="showcase showcase_cols_xs-8">
			<div class="showcase__list">
				<div class="showcase__item js_block_finance_date">
					<div class="filter-panel__item">
						<div class="form__line">
							<div class="form__label form__label_type_marked">Дата выставления счета</div>
							<div class="form__dual">
								<div class="form__dual-item">
									<div class="form__field form__field_size-m_s">
										<label class="form__input-wrapper">
											<input value="<?=$_REQUEST["date_account_from"]?>" class="form__input js-date" name="date_account_from" autocomplete="off">
											<svg class="form__input-icon"><use xlink:href="<?=T50Html::getSvg("icon_calendar")?>"></use></svg>
										</label>
									</div>
								</div>
								<div class="form__dual-item">
									<div class="form__field form__field_size-m_s">
										<label class="form__input-wrapper">
											<input value="<?=$_REQUEST["date_account_to"]?>" class="form__input js-date" name="date_account_to" autocomplete="off">
											<svg class="form__input-icon"><use xlink:href="<?=T50Html::getSvg("icon_calendar")?>"></use></svg>
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="showcase__item">
					<div class="form__label form__label_type_marked">Форма оплаты</div>
					<?foreach($arResult["INITIAL_DATA"]["PAY_TYPES"] as $id => $title){?>
						<div class="filter-panel__item">
							<div class="check-elem">
								<?=T50Html::checkboxLabel("pay_type[]", $id, "i", $title, "l");?>
							</div>
						</div>
					<? } ?>
				</div>


				<div class="showcase__item">
					<div class="form__label form__label_type_marked">Официальная продажа</div>
					<?foreach(["Y" => "да", "N" => "нет", "" => "не важно"] as $code => $title){?>
						<div class="filter-panel__item">
							<div class="check-elem">
								<?=T50Html::radioLabel(
									"official", $code,
									"i",
									$title, "l"
								);?>
							</div>
						</div>
					<? } ?>
				</div>

				<div class="showcase__item">
					<div class="form__label form__label_type_marked">Комиссия получена</div>
					<?foreach(["Y" => "да", "N" => "нет", "" => "не важно"] as $code => $title){?>
						<div class="filter-panel__item">
							<div class="check-elem">
								<?=T50Html::radioLabel(
									"commission_recived", $code,
									"i",
									$title, "l"
								);?>
							</div>
						</div>
					<? } ?>
				</div>


				<div class="showcase__item">
					<div class="filter-panel__item">
						<div class="check-elem">
							<?=T50Html::checkboxLabel("agency_contract", "N", "i", "Агентский договор", "l");?>
						</div>
					</div>
				</div>


			</div>
		</div>
	</div>
</div>
