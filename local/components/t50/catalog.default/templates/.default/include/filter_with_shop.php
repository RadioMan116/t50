<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form class="page__form page__item item" method="get">
	<input type="hidden" name="send_form" value="Y">
	<div class="panel">
		<div class="illustration-group">
			<div class="illustration-group__illustration">
				<? if( $arParams["SHOP"]["PREVIEW_PICTURE"] ){ ?>
					<img src="<?=$arParams["SHOP"]["PREVIEW_PICTURE"]?>" alt="Картинка" class="illustration-group__image">
				<? } ?>
			</div>
			<div class="illustration-group__content">
				<div class="panel__head panel__head_type_close">
					<div class="panel__head-main">
						<h1 class="panel__title"><?=$arResult["TITLE_CATEGOTY"]?></h1>
					</div>
					<div class="panel__head-controls">
						<div class="link link_type_trigger link_style_trigger js-panel-trigger" data-text="Развернуть фильтр">Свернуть фильтр</div>
					</div>
				</div>
				<div class="panel__content">
					<div class="table table_style_simple table_width_auto">
						<table class="table__main">
							<tr class="table__tr">
								<td class="table__td table__td_type_close">
									<?filterInput($arResult["INITIAL_DATA"]["CITY"], "l_xs")?>
								</td>
								<td class="table__td table__td_type_close">
									<?filterInput($arResult["INITIAL_DATA"]["BRAND"], "l_xs")?>
								</td>
								<td class="table__td table__td_type_close">
									<?filterInput($arResult["INITIAL_DATA"]["CATEGORY"], "l_xs")?>
								</td>
								<td class="table__td table__td_type_close">
									<?filterInput($arResult["INITIAL_DATA"]["TYPE"], "l_xs")?>
								</td>
								<td class="table__td table__td_type_close">
									<?filterInput($arResult["INITIAL_DATA"]["AVAIL"], "m_xs")?>
								</td>
								<td class="table__td table__td_type_close">
									<?filterInput($arResult["INITIAL_DATA"]["SALE"], "xl_xs")?>
								</td>
								<td class="table__td table__td_type_close">
									<?filterInput($arResult["INITIAL_DATA"]["PURCHASE"], "xl_xs")?>
								</td>
								<td class="table__td table__td_type_close">
									<?filterInput($arResult["INITIAL_DATA"]["COMMISSION"], "xl_xs")?>
								</td>
							</tr>
						</table>
					</div>

					<div class="table table_style_simple table_width_auto">
						<table class="table__main">
							<tr class="table__tr">
								<td colspan="2" class="table__td table__td_type_close table__td_close_sbottom">
									<?filterInput($arResult["INITIAL_DATA"]["FORMULA"], "xl_m")?>
								</td>
								<td class="table__td table__td_type_close table__td_close_sbottom">
									<?filterInput($arResult["INITIAL_DATA"]["DELIVERY"], "l_xs")?>
								</td>
								<td class="table__td table__td_type_close table__td_close_sbottom">
									<?filterInput($arResult["INITIAL_DATA"]["INSTAL"], "l_xs")?>
								</td>
								<td class="table__td table__td_type_close table__td_close_sbottom">
									<?filterInput($arResult["INITIAL_DATA"]["NEW"], "l_xs")?>
								</td>
								<td class="table__td table__td_type_close table__td_close_sbottom">
									<?filterInput($arResult["INITIAL_DATA"]["PRICE_MODE"], "l_xs")?>
								</td>
								<td class="table__td table__td_type_close table__td_close_sbottom">
									<?filterInput($arResult["INITIAL_DATA"]["AVAIL_MODE"], "l_xs")?>
								</td>
								<td class="table__td table__td_type_close table__td_close_sbottom">
									<label class="form__line form__line_type_close form__field form__field_size-l_l">
										<div class="form__label">&nbsp;</div>
										<input type="text" name="model" value="<?=$_REQUEST["model"]?>" placeholder="Поиск модели..." class="form__input">
									</label>
								</td>
								<td class="table__td td-apply table__td_type_close table__td_close_sbottom">
									<div class="form__label">&nbsp;</div>
									<button class="button button_style_dark button_type_concrete" type="submit">Применить</button>
								</td>
								<td class="table__td td-reset table__td_type_close table__td_close_sbottom">
									<div class="form__label">&nbsp;</div>
									<button class="button button_style_default button_type_concrete" type="reset">Сбросить</button>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>