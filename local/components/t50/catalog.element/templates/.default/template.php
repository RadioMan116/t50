<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="grid-12__container">
	<div class="page__item">
		<?T50Html::inc("search_model", ["targetBlank" => true])?>
	</div>
	<div class="page__item">
		<div class="panel">
			<div class="product-card">
				<div class="product-card__illustration">
					<img src="<?=T50Html::getAssets("/images/content/product-card/1.png")?>" class="product-card__image"
					 alt="" role="presentation" />
				</div>
				<div class="product-card__main">
					<h1 class="product-card__title">
						<?=$arResult["PRODUCT"]["UF_TITLE"]?>
						<span class="product-card__label">
							<? if( $arResult["PRODUCT"]["FLAGS"]["new"] ){ ?>
								<span class="label">
									<span class="label__wrapper">Новинка</span>
								</span>
							<? } ?>
						</span>
					</h1>
					<form class="product-card__controls">
						<div class="product-card__control">
							<div class="product-card__control-label">Город</div>
							<?=T50Html::select("city", ["MSK" => "Москва", "SPB" => "Санкт-Петербург"], [
								"cls" => "js-select product-card__select js_change_city",
								"val" => $arParams["CITY"]
							])?>
						</div>
						<div class="product-card__control" id="">
							<div class="product-card__control-label">Снято с производства

			<? if( $arResult["OTHER_COMMENTS"]["discontinued"] && $arResult["PRODUCT"]["UF_DISCONTINUED"] ){ ?>
				<div class="product-card__control-info">
					<div class="info-tooltip">
						<div data-tooltip-content="#tooltip_product_discount" class="info-tooltip__trigger js-tooltip"><svg class="info-tooltip__icon"><use xlink:href="<?=T50Html::getSvg("icon_info")?>"></use></svg></div>
						<div id="tooltip_product_discount" class="tooltipster__content">
							<div class="info-tooltip__title">
								<?=$arResult["OTHER_COMMENTS"]["discontinued"]["MANAGER"]?>
								<br/>
								Дата установки: <?=$arResult["OTHER_COMMENTS"]["discontinued"]["DATE_CREATE"]?>
								<br/>
								Обоснование: <br/>
								 <?=$arResult["OTHER_COMMENTS"]["discontinued"]["COMMENT"]?>
							</div>
						</div>
					</div>
				</div>
			<? } ?>
							</div>
							<div class="product-card__control-wrapper">
								<div class="check-elem check-elem_type_inline">
									<input type="checkbox" class="check-elem__input" id="discontinued" <?=( $arResult["PRODUCT"]["UF_DISCONTINUED"] ? "checked" : "" )?> data-id="<?=$arResult["PRODUCT"]["ID"]?>">
									<label class="check-elem__label" for="discontinued">Да</label>
								</div>
							</div>
						</div>

						<? if( $arResult["PRODUCT"]["UF_DISCONTINUED"] ){ ?>
							<div class="product-card__control" id="product_analog" data-product_id="<?=$arResult["PRODUCT"]["ID"]?>"></div>
						<? } else { ?>
							<div class="product-card__control">
								<div class="product-card__control-label">Действия</div>
								<a class="button js-modal" href="#addToCart">Добавить в корзину</a>
							</div>
						<? } ?>

					</form>
				</div>
				<ul class="product-card__links">
					<? if( $arResult["INFO"]["STATISTICS"]["CNT_CLAIMS"] ){ ?>
						<li class="product-card__links-item">
							<a href="/orders/claims/?send_form=Y&unid=<?=$arResult["INFO"]["UNID"]?>" class="product-card__link" target="_blank">
								Товар в рекламациях (<?=$arResult["INFO"]["STATISTICS"]["CNT_CLAIMS"]?>)
							</a>
						</li>
					<? } ?>

					<? if( $arResult["INFO"]["STATISTICS"]["CNT_ORDERS"] ){ ?>
						<li class="product-card__links-item">
							<a href="/orders/?send_form=Y&all_orders_with_unid=<?=$arResult["INFO"]["UNID"]?>" class="product-card__link" target="_blank">
								<?=T50Text::wordEnding(
									$arResult["INFO"]["STATISTICS"]["CNT_ORDERS"],
									["заказ", "заказа", "заказов"]
								)?>
							</a>
						</li>
					<? } ?>
				</ul>
			</div>
		</div>
	</div>
	<div class="page__item">
		<div class="panel">
			<div class="panel__head">
				<div class="panel__head-main">
					<h2 class="panel__title">Условия продажи товара</h2>
				</div>
				<div class="panel__head-controls">
					<div class="check-elem check-elem_type_inline js-block-toggle" data-active-target="#pcardBlocks" data-target="#pcardTabs">
						<input type="checkbox" id="blocks_view" class="check-elem__input" />
						<label for="blocks_view" class="check-elem__label">Отображать блоками</label>
					</div>
				</div>
			</div>
			<div id="pcardTabs">
				<div class="tabs">
					<?$SHOW_TABS = true;?>
					<ul class="tabs__nav">
						<li class="tabs__item">
							<a href="#suppliers" class="tabs__tab tabs__tab_state_active js-tabs-trigger">Поставщики</a>
						</li>
						<li class="tabs__item">
							<a href="#shops" class="tabs__tab js-tabs-trigger">Магазины</a>
						</li>
					</ul>
					<div class="tabs__content">
						<div id="_suppliers" class="tabs__panel tabs__scope tabs__panel_state_active">
							<?include __DIR__ . "/include/suppliers.php"?>
						</div>
						<div id="_shops" class="tabs__panel">
							<?include __DIR__ . "/include/shops.php"?>
						</div>
					</div>
				</div>
			</div>
			<div id="pcardBlocks" class="hidden">
				<?$SHOW_TABS = false;?>
				<div class="panel__item tabs__scope">
					<div class="panel__head">
						<div class="panel__head-main">
							<h2 class="panel__title panel__title_type_subtitle">Поставщики</h2>
						</div>
					</div>

					<?include __DIR__ . "/include/suppliers.php"?>
				</div>
				<div class="panel__item">
					<div class="panel__head">
						<div class="panel__head-main">
							<h2 class="panel__title panel__title_type_subtitle">Магазины</h2>
						</div>
					</div>

					<?include __DIR__ . "/include/shops.php"?>
				</div>
			</div>
		</div><!-- end .panel-->
	</div>
	<div class="page__item" id="comments_block" <?=$arResult["COMMENTS_JS_DATA"]?>></div>
	<div class="page__item">
		<div class="panel">
			<div class="panel__head">
				<div class="panel__head-main">
					<h2 class="panel__title panel__title_type_subtitle">Служебная информация</h2>
				</div>
			</div>
			<div class="panel__spec">
				<ul class="spec">
					<li class="spec__item">
						<span class="spec__label">Бренд</span>:
						<span class="spec__value"><?=$arResult["INFO"]["BRAND"]?></span>
					</li>
					<li class="spec__item">
						<span class="spec__label">UNID</span>:
						<span class="spec__value"><?=$arResult["INFO"]["UNID"]?></span>
					</li>
					<li class="spec__item">
						<span class="spec__label">Магазины</span>:
						<span class="spec__value"><?=$arResult["INFO"]["SHOPS"]?></span>
					</li>
					<li class="spec__item">
						<span class="spec__label">Модель</span>:
						<span class="spec__value"><?=$arResult["INFO"]["MODEL"]?></span>
					</li>
				</ul>

				<? if( $arResult["EDITABLE"] ){ ?>
				<div class="panel__spec">
					<div class="product-card__control">
						<div class="product-card__control-label">Формула для бренда</div>
						<?=T50Html::select("formula", $arResult["INFO"]["FORMULAS"], [
							"data" => ["brand_id" => $arResult["PRODUCT"]["UF_BRAND"] ],
							"cls" => "js-select product-card__select js_change_brand_formula",
							"val" => $arResult["INFO"]["BRAND_FORMULA"]
						])?>
					</div>
				</div>
				<? } ?>
			</div>
		</div>
	</div>
</div>

<?$APPLICATION->IncludeComponent("t50:modal", "", ["modal" => "pick_up_points"]);?>
<?$APPLICATION->IncludeComponent("t50:modal", "", ["modal" => "manual"]);?>
<?$APPLICATION->IncludeComponent("t50:modal", "", ["modal" => "formula_panel"]);?>
<?$APPLICATION->IncludeComponent("t50:modal", "", ["modal" => "analogs"]);?>
<?$APPLICATION->IncludeComponent("t50:modal", "", ["modal" => "universal_comment", "COMMENT_H3" => "Снятие с производства"]);?>
<?include __DIR__ . "/include/basket.php"?>