<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="panel">
	<div class="panel__head">
		<div class="panel__head-main">
			<h1 class="panel__title">Фильтр рекламаций</h1>
		</div>
		<div class="panel__head-controls">
			<div class="link link_type_trigger link_style_trigger js-panel-trigger" data-text="Развернуть фильтр">Свернуть фильтр</div>
		</div>
	</div>
	<div class="panel__content">
		<form class="table table_style_simple table_width_auto" action="" method="get">
			<input type="hidden" name="send_form" value="Y">
			<table class="table__main">
				<tr class="table__tr">
					<td class="table__td">
						<label class="form__line form__line_type_close form__field form__field_size-s_l">
							<div class="form__label">Номер заказа</div>
							<input type="text" value="<?=$_REQUEST["order"]?>" name="order" pattern="^[ 0-9s]+$" class="form__input js-number-field" autocomplete="off">
						</label>
					</td>
					<td class="table__td">
						<label class="form__line form__line_type_close form__field form__field_size-l_s">
							<div class="form__label">Магазин</div>
							<?=T50Html::select("shop", $arResult["INITIAL_DATA"]["SHOPS"],
								["cls" => "js-select form__select", "empty" => "-"]);?>
						</label>
					</td>
					<td class="table__td">
						<label class="form__line form__line_type_close form__field form__field_size-l_s">
							<div class="form__label">Поставщик</div>
							<?=T50Html::select("supplier", $arResult["INITIAL_DATA"]["SUPPLIERS"],
								["cls" => "js-select form__select", "empty" => "-"]);?>
						</label>
					</td>
					<td class="table__td">
						<label class="form__line form__line_type_close form__field form__field_size-l_s">
							<div class="form__label">Менеджер</div>
							<?=T50Html::select("manager", $arResult["INITIAL_DATA"]["MANAGERS"],
								["cls" => "js-select form__select", "empty" => "-"]);?>
						</label>
					</td>
					<td class="table__td">
						<label class="form__line form__line_type_close form__field form__field_size-l_s">
							<div class="form__label">Статус рекламации</div>
							<?=T50Html::select("open", $arResult["INITIAL_DATA"]["STATUS"],
								["cls" => "js-select form__select", "empty" => "-"]);?>
						</label>
					</td>
					<td class="table__td">
						<div class="form__line form__line_type_close form__field form__field_size-m_s">
							<div class="form__label">Дата обращения</div>
							<label class="form__input-wrapper">
								<input value="<?=$_REQUEST["date_request"]?>" name="date_request" class="form__input js-date" >
								<svg class="form__input-icon">
									<use xlink:href="<?=T50Html::getSvg("icon_calendar")?>"></use>
								</svg>
							</label>
						</div>
					</td>
					<td class="table__td">
						<div class="form__line form__line_type_close form__field form__field_size-m_s">
							<div class="form__label">Дата решения</div>
							<label class="form__input-wrapper">
								<input value="<?=$_REQUEST["date_result"]?>" name="date_result" class="form__input js-date">
								<svg class="form__input-icon">
									<use xlink:href="<?=T50Html::getSvg("icon_calendar")?>"></use>
								</svg>
							</label>
						</div>
					</td>
					<td class="table__td">
						<label class="form__line form__line_type_close form__field form__field_size-l_s">
							<div class="form__label">Клиент</div>
							<input placeholder="Поиск клиента…" name="client" value="<?=$_REQUEST["client"]?>" class="form__input" autocomplete="off">
						</label>
					</td>
					<td class="table__td">
						<label class="form__line form__line_type_close form__field form__field_size-s_xs">
							<div class="form__label">ID товара</div>
							<input placeholder="unid" name="unid" value="<?=$_REQUEST["unid"]?>" class="form__input" autocomplete="off">
						</label>
					</td>
					<td class="table__td">
						<div class="form__label">&nbsp;</div>
						<button class="button button_style_dark button_type_concrete" type="submit">Применить фильтр</button>
					</td>
					<td class="table__td">
						<div class="form__label">&nbsp;</div>
						<button class="button button_style_default button_type_concrete" id="claim_filter_reset" type="reset">Сбросить</button>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>