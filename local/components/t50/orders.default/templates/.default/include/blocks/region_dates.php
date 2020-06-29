<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="panel__section">
	<div class="panel__controls">
		<div class="panel__controls-group"><label class="form__line form__field form__field_size-l_m">
				<div class="form__label form__label_type_marked">Регион</div>
				<?=T50Html::select("region", $arResult["INITIAL_DATA"]["REGION"], [
					"cls" => "js-select form__select",
					"empty" => "-"
				])?>
			</label>
		</div>
		<div class="panel__controls-group">
			<div class="form__line">
				<div class="form__label form__label_type_marked">Дата заказа</div>
				<div class="form__dual">
					<div class="form__dual-item">
						<div class="form__field form__field_size-m_s">
							<label class="form__input-wrapper">
								<input value="<?=$_REQUEST["date_create_from"]?>" class="form__input js-date" name="date_create_from" autocomplete="off">
								<svg class="form__input-icon"><use xlink:href="<?=T50Html::getSvg("icon_calendar")?>"></use></svg>
							</label>
						</div>
					</div>
					<div class="form__dual-item">
						<div class="form__field form__field_size-m_s">
							<label class="form__input-wrapper">
								<input value="<?=$_REQUEST["date_create_to"]?>" class="form__input js-date" name="date_create_to" autocomplete="off">
								<svg class="form__input-icon"><use xlink:href="<?=T50Html::getSvg("icon_calendar")?>"></use></svg>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel__controls-group">
			<div class="form__line">
				<div class="form__label form__label_type_marked">Дата отгрузки заказа</div>
				<div class="form__dual">
					<div class="form__dual-item">
						<div class="form__field form__field_size-m_s">
							<label class="form__input-wrapper">
								<input value="<?=$_REQUEST["date_delivery_from"]?>" class="form__input js-date" name="date_delivery_from" autocomplete="off">
								<svg class="form__input-icon"><use xlink:href="<?=T50Html::getSvg("icon_calendar")?>"></use></svg>
							</label>
						</div>
					</div>
					<div class="form__dual-item">
						<div class="form__field form__field_size-m_s">
							<label class="form__input-wrapper">
								<input value="<?=$_REQUEST["date_delivery_to"]?>" class="form__input js-date" name="date_delivery_to" autocomplete="off">
								<svg class="form__input-icon"><use xlink:href="<?=T50Html::getSvg("icon_calendar")?>"></use></svg>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel__controls-group">
			<label class="form__line form__field form__field_size-m_s">
				<div class="form__label form__label_type_marked">&nbsp;</div>
				<div class="check-elem">
					<?=T50Html::checkboxLabel("shipment_tk", "Y", "i", "Отгрузка в ТК", "l");?>
				</div>
			</label>
		</div>
	</div>
</div>