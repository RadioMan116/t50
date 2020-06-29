<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="modal modal_size_small" id="order_comment">
	<h3 class="modal__title modal__title_align_center">Добавить комментарий</h3>
	<div class="modal__content" id="order_comment_react"></div>
</div>
<script>
	window.themes = <?=CUtil::PhpToJSObject($arResult["THEMES"])?>
</script>

<?return?>
<div class="modal modal_size_small" id="order_comment">
	<h3 class="modal__title modal__title_align_center">Добавить комментарий</h3>
	<div class="modal__content">
		<form class="form">
			<label class="form__line">
				<div class="form__label">Тема</div>
				<?=T50Html::select("theme", $arResult["THEMES"], "js-select form__select")?>
			</label>
			<div class="form__line">
				<div class="triple-group">
					<div class="triple-group__item">
						<div class="check-elem">
							<?=T50Html::checkboxLabel("remind", "Y", "i", "напомнить", "l");?>
						</div>
					</div>
					<div class="triple-group__item">
						<div class="form__field form__field_size-m_s form__field_type_inline">
							<label class="form__input-wrapper">
								<input value="" class="form__input js-date" name="remind_date" autocomplete="off">
									<svg class="form__input-icon">
										<use xlink:href="<?=T50Html::getSvg("icon_calendar")?>"></use>
									</svg>
							</label>
						</div>
					</div>
					<div class="triple-group__item">
						<label class="form__field form__field_size-xs_s form__field_type_inline">
							<input value="" class="form__input js-time" name="remind_time" autocomplete="off">
						</label>
					</div>
				</div>
			</div>

			<label class="form__line" id="comment_react">
				<div class="form__label">Комментарий</div>
				<div class="form__wrapper" ></div>
			</label>
			<div class="form__controls form__controls_align_center modal__controls">
				<div class="form__control modal__control">
					<button class="button button_width_full" type="button" id="order_comment_save">Добавить комментарий</button>
				</div>
			</div>
		</form>
	</div>
</div>