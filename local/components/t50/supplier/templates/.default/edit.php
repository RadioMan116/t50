<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?>
<div class="grid-12__container">
	<div class="page__item">
		<div class="panel">
			<div class="panel__centric">
				<div class="panel__head">
					<h2 class="panel__title panel__title_type_subtitle panel__title_align_center">Данные поставщика</h2>
				</div>
				<form class="entry-form" action="" method="post">
					<?=bitrix_sessid_post()?>
					<div class="entry-form__head entry-form__head_type_close">
						<span class="entry-form__label">Поставщик:</span>
						<b class="entry-form__mark"><?=$arResult["NAME"]?></b>
						<? if( isset($arResult["DOMEN"]) ){ ?>
							<a class="link link_style_classic" href="<?=$arResult["SITE_URL"]?>">
								<?=$arResult["DOMEN"]?>
							</a>
						<? } ?>
					</div>
					<div class="form">
						<label class="form__line">
							<span class="form__label">Введите информацию по поставщику</span>
							<div class="formatted-text">
								<textarea class="form__textarea js-wysiwyg" name="text">
									<?=$arResult["DETAIL_TEXT"]?>
								</textarea>
							</div>
						</label>
					</div>
					<div class="entry-form__foot entry-form__foot_align_center entry-form__foot_type_close">
						<div class="entry-form__control">
							<a class="button button_style_default button_width_full" href="/suppliers/<?=$arResult["ID"]?>/">Вернуться назад</a>
						</div>
						<div class="entry-form__control">
							<button class="button button_width_full" type="submit" name="action" value="update">Сохранить</button>
						</div>
					</div>
					<? if( $arResult["ERROR"] ){ ?>
						<div class="entry-form__foot entry-form__foot_align_center entry-form__foot_type_close">
							<span class="color_red"><b>Ошибка</b></span>
						</div>
					<? } ?>
				</form>
			</div>
		</div>
	</div>
</div>