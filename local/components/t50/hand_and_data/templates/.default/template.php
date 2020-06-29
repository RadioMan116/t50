<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? if( !$arParams["IS_AJAX"] ){ ?>
<div id="<?=$arResult["AJAX_ID"]?>">
<? } ?>

<div class="handbrake-group handbrake-group_type_inline" >
	<div class="handbrake-group__content">
		<div class="form__field form__field_size-<?=( $arResult["TYPE_AVAIL"] ? "xl_xs" : "m_s" )?>">
			<? if( $arResult["TYPE_AVAIL"] ){ ?>
				<div class="status status_width_full <?=$arResult["VALUE"]->class?>">
					<?=$arResult["VALUE"]->text?>
				</div>
			<? } else { ?>
				<input type="text" value="<?=$arResult["VALUE"]?>" disabled="" class="form__input">
			<? } ?>

			<? if( $arParams["IS_MANUAL"] ) { ?>
				<div class="handbrake-group__info">
					<div class="info-tooltip">
						<div data-tooltip-content="#<?=$arResult["TOOLTIP_ID"]?>" class="info-tooltip__trigger js-tooltip tooltipstered">
							<svg class="info-tooltip__icon">
								<use xlink:href="<?=T50HTML::getSvg("icon_info")?>"></use>
							</svg>
						</div>
						<div id="<?=$arResult["TOOLTIP_ID"]?>" class="tooltipster__content">
							<div class="info-tooltip__title"><?=$arResult["MANAGER"]?></div>
							<? if( $arResult["TYPE_AVAIL"] ){ ?>
							    <div class="info-tooltip__title">
							    	Было: <?=$arResult["AUTO_VALUE"]->text?>
							    </div>
							    <div class="info-tooltip__title">
							    	Стало: <?=$arResult["VALUE"]->text?>
							    </div>
							<? } else {?>
							    <div class="info-tooltip__title">
							    	Было: <?=$arResult["AUTO_VALUE"]?>
							    </div>
							    <div class="info-tooltip__title">
							    	Стало: <?=$arResult["VALUE"]?>
							    </div>
							<? } ?>

							<? if( $arResult["DATE"] ){ ?>
								<div class="info-tooltip__title">
									Дата изменения: <?=$arResult["DATE"]?>
								</div>
							<? } ?>

							<div class="info-tooltip__title">
								Комментарий: <?=$arResult["COMMENT"]?>
							</div>

							<div class="info-tooltip__control">
								<? if( $arResult["IS_EDITABLE"] ){ ?>
									<a href="#manual" class="link js-modal" <?=$arResult["DATA_ATTRS"]?>>Изменить</a>
								<? } ?>
							</div>
						</div>
					</div>
				</div>
			<? } ?>
		</div>
	</div>

	<div class="handbrake-group__control">

		<? if( $arResult["IS_EDITABLE"] ){ ?>
			<a href="#manual" class="handbrake-group__button js-modal <?=( $arParams["IS_MANUAL"] ? "handbrake-group__button_state_active" : "" )?>" <?=$arResult["DATA_ATTRS"]?> id="hand_<?=$arResult["TOOLTIP_ID"]?>">
				<svg class="handbrake-group__icon">
					<use xlink:href="<?=T50Html::getSvg("icon_hand")?>"></use>
				</svg>
			</a>

		<? } elseif( $arParams["IS_MANUAL"] ) { ?>
			    <button type="button" disabled="disabled" class="handbrake-group__button handbrake-group__button_state_active">
			    	<svg class="handbrake-group__icon">
			    		<use xlink:href="<?=T50Html::getSvg("icon_hand")?>"></use>
			    	</svg>
			    </button>
		<? } ?>

	</div>
</div>

<? if( !$arParams["IS_AJAX"] ){ ?>
</div>
<? } else { ?>
	<script src="<?=SITE_TEMPLATE_PATH?>/assets/scripts/common.js"></script>
<? } ?>