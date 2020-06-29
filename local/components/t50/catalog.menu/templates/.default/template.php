<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// $arResult["SHOPS_ALPHABET"]
// $arResult["SHOPS"]
// $arResult["SECTIONS"]
// $arResult["SECTIONS_ALPHABET"]
?>

<div class="catalog-menu">
	<div class="catalog-menu__header">
		<ul class="catalog-menu__tabs">
			<li class="catalog-menu__tab-item"><a href="#ctab1" class="catalog-menu__tab-link catalog-menu__tab-link_state_active js-catalog-tab-trigger">Магазины</a></li>
			<li class="catalog-menu__tab-item"><a href="#ctab2" class="catalog-menu__tab-link js-catalog-tab-trigger">Категории</a></li>
		</ul>
		<form class="catalog-menu__form">
			<!-- begin .search-form-->
			<div class="search-form"><input placeholder="Поиск…" value="" class="search-form__input" type="text" /></div><!-- end .search-form-->
		</form>
	</div>
	<div class="catalog-menu__content">
		<div id="ctab1" class="catalog-menu__panel catalog-menu__panel_state_active">
			<div class="catalog-menu__filters">
				<!-- begin .alphabet-filter-->
				<div class="alphabet-filter">
					<div class="alphabet-filter__alphabet">
						<!-- begin .alphabet-group-->
						<form class="alphabet-group js-alphabet-filter" data-scope=".catalog-menu__panel" data-target=".list__item"
						 data-toggle-class="list__item_state_excluded">
							<ul class="alphabet-group__list">

								<li class="alphabet-group__item">
									<label class="alphabet-group__wrapper">
										<input type="radio" name="alphabet" value="reset" checked="checked" class="alphabet-group__input" />
										<span class="alphabet-group__label">Все</span>
									</label>
								</li>

								<? foreach($arResult["SHOPS_ALPHABET"] as $letter){ ?>
									<li class="alphabet-group__item">
										<label class="alphabet-group__wrapper">
											<input type="radio" name="alphabet" value="<?=$letter?>" class="alphabet-group__input" />
											<span class="alphabet-group__label"><?=$letter?></span>
										</label>
									</li>
								<? } ?>

							</ul>
						</form><!-- end .alphabet-group-->
					</div>
				</div><!-- end .alphabet-filter-->
			</div>

			<div class="showcase showcase_cols_xs-6">
				<div class="showcase__list">

					<? foreach($arResult["SHOPS"] as $item){ ?>
						<div data-letter="<?=$item["LETTER"]?>" class="showcase__item">
							<!-- begin .catalog-snippet-->
							<div class="catalog-snippet showcase__inner">
								<div class="catalog-snippet__illustration">
									<img src="<?=$item["PREVIEW_PICTURE"]?>" class="catalog-snippet__image" alt="" role="presentation" />
								</div>
								<h3 class="catalog-snippet__title">
									<?=$item["PROPERTY_OFFICIAL_NAME_VALUE"]?>
								</h3>

								<a class="link" href="/catalog/<?=$item["CODE"]?>/">
									<?=$item["HOST"]?>
								</a>


								<? if( $item["PROPERTY_OFFICIAL_VALUE"] == "Y" ){ ?>
									<svg class="catalog-snippet__avaliable-icon"><use xlink:href="<?=T50Html::getAssets("/images/icon.svg#icon_avaliable")?>"></use></svg>
								<? } ?>
							</div>
						</div>
					<? } ?>

				</div>
			</div>


		</div>
		<div id="ctab2" class="catalog-menu__panel">
			<div class="catalog-menu__filters">
				<!-- begin .alphabet-filter-->
				<div class="alphabet-filter">
					<div class="alphabet-filter__alphabet">
						<!-- begin .alphabet-group-->
						<form class="alphabet-group js-alphabet-filter" data-scope=".catalog-menu__panel" data-target=".list__item"
						 data-toggle-class="list__item_state_excluded">
							<ul class="alphabet-group__list">

								<li class="alphabet-group__item">
									<label class="alphabet-group__wrapper">
										<input type="radio" name="alphabet" value="reset" checked="checked" class="alphabet-group__input" />
										<span class="alphabet-group__label">Все</span>
									</label>
								</li>

								<? foreach($arResult["SECTIONS_ALPHABET"] as $letter){ ?>
									<li class="alphabet-group__item">
										<label class="alphabet-group__wrapper">
											<input type="radio" name="alphabet" value="<?=$letter?>" class="alphabet-group__input" />
											<span class="alphabet-group__label"><?=$letter?></span>
										</label>
									</li>
								<? } ?>

							</ul>
						</form><!-- end .alphabet-group-->
					</div>
				</div><!-- end .alphabet-filter-->
			</div>
			<div class="catalog-menu__links">
				<!-- begin .links-group-->
				<div class="links-group">
					<? foreach($arResult["SECTIONS"] as $chunk){ ?>
							<div class="links-group__item">
								<ul class="list">
									<? foreach($chunk as $item){
										$class = ( $item["HIDDEN"] ? "list__item_type_addition hidden" : "" );
									?>
										<li data-letter="с" class="list__item <?=$class?>">
											<a class="link" href="<?=$item["URL"]?>">
												<?=$item["NAME"]?>
											</a>
										</li>
									<? } ?>
								</ul>
							</div>
					<? } ?>


					<div class="links-group__controls">
						<!-- begin .button-->
						<div class="button js-toggle" data-scope=".links-group" data-toggle-class="hidden" data-toggle-target=".list__item_type_addition"
						 data-toggle-text="Свернуть">Смотреть все категории</div><!-- end .button-->
					</div>
				</div><!-- end .links-group-->
			</div>
		</div>
	</div><button type="button" class="catalog-menu__close js-dropdown-close">Закрыть</button>
</div>
