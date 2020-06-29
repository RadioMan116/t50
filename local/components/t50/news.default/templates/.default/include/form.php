<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form class="form" >
	<input type="hidden" name="form_send" value="Y">
	<div class="filters-group">
		<div class="filters-group__inner">
			<div class="filters-group__item"><label class="form__line form__line_type_close">
				<label class="form__label">Бренд</label>
					<?=T50Html::select("brand", $arResult["INITIAL_DATA"]["BRANDS"], ["cls" => "s", "empty" => "Любой"])?>
				</label>
			</div>
			<div class="filters-group__item"><label class="form__line form__line_type_close">
				<label class="form__label">Тема</label>
					<?=T50Html::select("theme", $arResult["INITIAL_DATA"]["THEMES"], ["cls" => "s", "empty" => "Любая"])?>
				</label>
			</div>
			<div class="filters-group__item">
				<label class="form__line form__line_type_close">
					<label class="form__label">Отдел</label>
					<?=T50Html::select("group", $arResult["INITIAL_DATA"]["GROUPS"], ["cls" => "s", "empty" => "Все отделы"])?>
				</label>
			</div>
			<div class="filters-group__item">
				<div class="form__line form__line_type_close">
					<div class="form__label">Дата</div>
					<div class="input-group">
						<label class="input-group__item">
							<div class="form__input-wrapper">
								<input name="date_from" autocomplete="off" class="form__input js-date" type="text" value="<?=$_REQUEST["date_from"]?>"/>
								<svg class="form__input-icon"><use xlink:href=<?=T50Html::getSvg("icon_calendar")?>></use></svg>
							</div>
						</label>
						<div class="input-group__separator">-</div>
						<label class="input-group__item">
							<div class="form__input-wrapper">
								<input name="date_to" autocomplete="off" class="form__input js-date" type="text" value="<?=$_REQUEST["date_to"]?>"/>
								<svg class="form__input-icon"><use xlink:href=<?=T50Html::getSvg("icon_calendar")?>></use></svg>
							</div>
						</label>
					</div>
				</div>
			</div>
			<div class="filters-group__item">
				<div class="dual-panel">
					<div class="dual-panel__row">
						<div class="dual-panel__col dual-panel__col_type_auto dual-panel__col_type_close">
							<button class="button button_style_dark" type="submit">Применить фильтр</button>
						</div>
						<div class="dual-panel__col dual-panel__col_type_auto dual-panel__col_type_close">
							<button class="button button_style_default" type="button" onclick="document.location.href=document.location.pathname">Сбросить</button>
						</div>
					</div>
				</div>
			</div>
			<? if( $arResult["CAN_EDIT"] ){ ?>
				<div class="filters-group__item filters-group__item_align_right">
					<a class="button" href="/news/add/" target="_blank">Добавить новость</a>
				</div>
			<? } ?>
		</div>
	</div>

	<? if( $arResult["FILTER_ERROR"] ){ ?>
	    <div class="panel__important">
	    	<span>ошибка в фильтре</span>
	    </div>
	<? } ?>

</form>