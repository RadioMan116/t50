<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="grid-12__container">
	<div class="page__item">
		<div class="panel">
			<div class="panel__centric">
				<div class="panel__head">
					<h2 class="panel__title panel__title_type_subtitle panel__title_align_center">Данные поставщика</h2>
				</div>
				<div class="entry-form">
					<div class="entry-form__head">
						<span class="entry-form__label">Поставщик:</span>
						<b class="entry-form__mark"><?=$arResult["NAME"]?></b>
						<? if( isset($arResult["DOMEN"]) ){ ?>
							<!-- <div class="entry-form__head-item"> -->
							<a class="link link_style_classic" href="<?=$arResult["SITE_URL"]?>">
								<?=$arResult["DOMEN"]?>
							</a>
							<!-- </div> -->
						<? } ?>
					</div>
					<div class="entry-form__content formatted-text"><?=$arResult["DETAIL_TEXT"]?></div>
					<div class="entry-form__foot entry-form__foot_align_center">
						<div class="entry-form__control">
							<a class="button button_style_default button_width_full" href="#">Вернуться назад</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>