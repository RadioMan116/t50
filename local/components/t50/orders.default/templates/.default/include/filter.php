<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$viewTabs = ( $arResult["INITIAL_DATA"]["FILTER_TYPE"] == "tabs" );
$blocksMap = array(
	["CODE" => "shops_managers", "TITLE" => "Магазины и менеджеры"],
	["CODE" => "statuses_source", "TITLE" => "Статус и источник заказа"],
	["CODE" => "region_dates", "TITLE" => "Регион и дата"],
	["CODE" => "providers", "TITLE" => "Поставщики"],
	["CODE" => "finance", "TITLE" => "Финансовая группа"],
	["CODE" => "control", "TITLE" => "Контроль"],
);
?>
<div class="panel">
	<div class="page__scroll">
		<div class="panel__section panel__section_size_big">
			<div class="filter-panel">
				<div class="filter-panel__head">
					<h2 class="filter-panel__title">Вид фильтра</h2>
				</div>
				<div class="filter-panel__content filter-panel__content_state_editable">
					<div class="filter-panel__list">
						<div class="showcase showcase_cols_xs-5">
							<div class="showcase__list js_save_selected_view">

								<div class="showcase__item">
									<div class="check-elem check-elem_type_inline js-block-toggle" data-active-target="#filterAll" data-target="#filterTabs">
										<input type="radio" name="page_type" id="page_type_all" checked="checked" class="check-elem__input" <?=( !$viewTabs ? "checked" : "" )?>/>
										<label for="page_type_all" class="check-elem__label">
											Все параметры в одном окне
										</label>
									</div>
								</div>

								<div class="showcase__item">
									<div class="check-elem check-elem_type_inline js-block-toggle" data-active-target="#filterTabs" data-target="#filterAll">
										<input type="radio" name="page_type" id="page_type_tabs" class="check-elem__input" <?=( $viewTabs ? "checked" : "" )?>/>
										<label for="page_type_tabs" class="check-elem__label">
											Параметры во вкладках
										</label>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<form id="filterAll" method="get" class="<?=( $viewTabs ? "hidden" : "" )?>">
		<input type="hidden" name="send_form" value="Y">
		<? foreach($blocksMap as $block){ ?>
			<div class="page__scroll">
				<div class="panel__section panel__section_size_big">
					<div class="filter-panel">
						<div class="filter-panel__head">
							<label for="shop_filter_group" class="filter-panel__check-label">
								<h2 class="filter-panel__title filter-panel__title_type_inline">
									<?=$block["TITLE"]?>
								</h2>
							</label>
						</div>
						<? include __DIR__ . "/blocks/" . $block["CODE"] . ".php"?>
					</div>
				</div>
			</div>
		<? } ?>
	</form>

	<form id="filterTabs"  method="get" class="<?=( $viewTabs ? "" : "hidden" )?>">
		<input type="hidden" name="send_form" value="Y">
		<div id="pcardTabs">
			<div class="page__scroll">
				<div class="panel__section panel__section_size_big panel__section_type_clipped">

					<div class="tabs">
						<ul class="tabs__nav">

							<? foreach($blocksMap as $k => $block){ ?>
							<li class="tabs__item">
								<a href="#<?=$block["CODE"]?>" class="tabs__tab js-tabs-trigger <?=( $k == 0 ? "js-tabs-trigger__first" : "" )?>">
									<?=$block["TITLE"]?>
								</a>
							</li>
							<? } ?>

						</ul>
						<div class="tabs__content">

							<? foreach($blocksMap as $k => $block){ ?>
							<div id="_<?=$block["CODE"]?>" class="tabs__panel <?=( $k == 0 ? "" : "" )?>">
								<div class="filter-panel">
									<? include __DIR__ . "/blocks/" . $block["CODE"] . ".php"?>
								</div>
							</div>
							<? } ?>

						</div>
					</div>
				</div>
			</div>
		</div>
	</form>

	<div class="panel__foot panel__foot_type_full panel__foot_valign_middle">
		<div class="panel__main">
			<div class="dual-panel dual-panel_width_auto">
				<div class="dual-panel__row">
					<div class="dual-panel__col dual-panel__col_type_auto dual-panel__col_type_close">
						<button class="button button_style_dark js-submit-filter table__td td-apply" type="button">Применить фильтр</button>
					</div>
					<div class="dual-panel__col dual-panel__col_type_auto dual-panel__col_type_close">
						<button class="button button_style_default js-reset-filter table__td td-reset" type="button">Сбросить</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
