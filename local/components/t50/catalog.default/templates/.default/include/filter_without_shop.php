<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form class="page__item" method="get">
	<input type="hidden" name="send_form" value="Y">
	<div class="panel">
		<div class="panel__head">
			<div class="panel__head-main">
				<h1 class="panel__title"><?=$arResult["TITLE_CATEGOTY"]?></h1>
			</div>
			<div class="panel__head-controls">

				<div class="link link_type_trigger link_style_trigger js-panel-trigger" data-text="Развернуть фильтр">Свернуть
					фильтр</div>
			</div>
		</div>
		<div class="panel__content">

			<div class="table table_style_simple table_width_auto">
				<table class="table__main">
					<tr class="table__tr">
						<td class="table__td">
							<?filterInput($arResult["INITIAL_DATA"]["CITY"], "l_xs")?>
						</td>
						<td class="table__td">
							<?filterInput($arResult["INITIAL_DATA"]["BRAND"], "l_xs")?>
						</td>
						<td class="table__td">
							<?filterInput($arResult["INITIAL_DATA"]["CATEGORY"], "l_xs")?>
						</td>
						<td class="table__td">
							<?filterInput($arResult["INITIAL_DATA"]["TYPE"], "l_xs")?>
						</td>
						<td class="table__td">
							<?filterInput($arResult["INITIAL_DATA"]["AVAIL"], "m_xs")?>
						</td>
						<td class="table__td">
							<?filterInput($arResult["INITIAL_DATA"]["SALE"], "xl_xs")?>
						</td>
						<td class="table__td">
							<?filterInput($arResult["INITIAL_DATA"]["PURCHASE"], "xl_xs")?>
						</td>
						<td class="table__td">
							<?filterInput($arResult["INITIAL_DATA"]["COMMISSION"], "xl_xs")?>
						</td>
						<td colspan="2" class="table__td">
							<?filterInput($arResult["INITIAL_DATA"]["FORMULA"], "")?>
						</td>
					</tr>
					<tr class="table__tr">
						<td class="table__td">
							<?filterInput($arResult["INITIAL_DATA"]["DELIVERY"], "l-xl")?>
						</td>
						<td class="table__td">
							<?filterInput($arResult["INITIAL_DATA"]["INSTAL"], "l-xl")?>
						</td>
						<td class="table__td">
							<?filterInput($arResult["INITIAL_DATA"]["NEW"], "l-xl")?>
						</td>
						<td class="table__td">
							<?filterInput($arResult["INITIAL_DATA"]["PRICE_MODE"], "l-xl")?>
						</td>
						<td class="table__td">
							<?filterInput($arResult["INITIAL_DATA"]["AVAIL_MODE"], "m-xs")?>
						</td>
						<td colspan="3" class="table__td">
							<label class="form__line form__line_type_close">
								<div class="form__label">&nbsp;</div>
								<input type="text" name="model" value="<?=$_REQUEST["model"]?>" placeholder="Поиск модели..." class="form__input">
							</label>
						</td>
						<td class="table__td td-apply">
							<div class="form__label">&nbsp;</div><button class="button button_style_dark button_type_concrete"
							 type="submit">Применить</button>
						</td>
						<td class="table__td td-reset">
							<div class="form__label">&nbsp;</div><button class="button button_style_default button_type_concrete"
							 type="reset">Сбросить</button>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</form>