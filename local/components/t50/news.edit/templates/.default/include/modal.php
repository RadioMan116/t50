<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="modal" id="group_selection">
	<h3 class="modal__title modal__title_align_center">Выбор отделов</h3>
	<div class="modal__subtitle modal__subtitle_align_center">Выберите получателей новости</div>
	<div class="modal__content">
		<div class="form">
			<div class="dual-panel">
				<div class="dual-panel__row">
					<div class="dual-panel__col">
					<? foreach($arResult["GROUPS"] as $k => $item){ ?>
						<? if( $k > 0 && $k % 5 == 0 ){ ?>
						    </div>
							<div class="dual-panel__col">
						<? } ?>
						<div class="form__line form__line_type_middle">
							<div class="check-elem">
								<?php
								if( $item["ID"] == 0 ){
									echo T50Html::checkboxLabel("all_groups", null, "i", $item["NAME"], "l");
								} else {
									echo T50Html::checkboxLabel("group[]", $item["ID"], "i", $item["NAME"], "l");
								}
								?>
							</div>
						</div>
					<? } ?>
					 </div>
				</div>
			</div>
			<div class="form__controls form__controls_align_center modal__controls">
				<div class="form__control modal__control">
					<button class="button button_width_full" type="button">Подтвердить</button>
				</div>
			</div>
		</div>
	</div>
</div>