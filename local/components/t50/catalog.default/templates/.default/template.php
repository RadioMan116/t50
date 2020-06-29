<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="grid-12__container grid-12__container_width_full">
	<div class="page__item">
		<?T50Html::inc("search_model")?>
	</div>

	<?include __DIR__ ."/include/". ( isset($arParams["SHOP"]) ? "filter_with_shop.php" : "filter_without_shop.php" )?>

	<?include __DIR__ ."/include/products.php"?>

	<? if( !empty($arParams["SHOP"]["DETAIL_TEXT"]) ){ ?>
		<div class="page__item">
			<div class="panel" id="fullDetails">
				<div class="panel__head">
					<div class="panel__head-main">
						<h2 class="panel__title panel__title_type_subtitle">
							Полные реквизиты <?=$arParams["SHOP"]["PROPERTY_OFFICIAL_NAME_VALUE"]?>
						</h2>
					</div>
					<div class="panel__head-controls">
						<a href="#" class="link link_type_pdf">
							<svg class="link__icon">
								<use xlink:href="<?=T50Html::getSvg("icon_pdf")?>"></use>
							</svg>
							Скачать реквизиты в PDF
						</a>
					</div>
				</div>
				<div class="panel__spec">
					<?=$arParams["~SHOP"]["DETAIL_TEXT"]?>
				</div>
			</div>
		</div>
	<? } ?>

</div>