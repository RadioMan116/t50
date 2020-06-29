<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<!-- <div class="product-card__control"> -->
	<div class="product-card__control-label">Аналоги<div class="product-card__control-info">
			<? if( $arResult["COMMENT"] && $arResult["PRODUCT"] ){ ?>

				<div class="info-tooltip">
					<div data-tooltip-content="#tooltip_analog_comment" class="info-tooltip__trigger js-tooltip">
						<svg class="info-tooltip__icon"><use xlink:href="<?=T50Html::getSvg("icon_info")?>"></use></svg>
					</div>
					<div id="tooltip_analog_comment" class="tooltipster__content">
						<?
	 					// [MANAGER] => admin main
	    				// [DATE_CREATE] => 06.03.2020
	    				// [COMMENT] => текст
						?>
						<?=$arResult["COMMENT"]["COMMENT"]?>
					</div>
				</div>
			<? } ?>
		</div>
	</div>

	<div class="product-card__control-group">
		<div class="dual-panel">
			<div class="dual-panel__row">
				<? if( $arResult["PRODUCT"] ){ ?>
					<div class="dual-panel__col">
						<a class="link link_style_classic" href="<?=$arResult["PRODUCT"]["URL"]?>">
							<?=$arResult["PRODUCT"]["UF_TITLE"]?>
						</a>
					</div>
				<? }  ?>
				<div class="dual-panel__col">
					<a class="button button_size_middle js-analog-modal" data-product_id="<?=$arResult["PRODUCT_ID"]?>"  href="#analogs"><?=$arResult["PRODUCT"] ? "Заменить" : "Добавить"?> аналог</a>
				</div>
			</div>
		</div>
	</div>
<!-- </div> -->

<!-- <script src="<?=SITE_TEMPLATE_PATH?>/assets/scripts/common.js"></script> -->
<script type="text/javascript">
$(document).ready(function (){
	var $modal = $('.js-analog-modal');
	$modal.fancybox({
		afterShow: function () {
			// jsDate();
			// jsTime();
			// jsSelectInit();
			// range();
		},
		afterLoad: function (){
			this.$content.trigger("modal_loaded", this.opts);
		},
		afterClose: function (){
			this.$content.trigger("modal_close", this.opts);
		}
	});
});
</script>