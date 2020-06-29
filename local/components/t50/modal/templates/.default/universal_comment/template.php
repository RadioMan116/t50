<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="modal" id="universal_comment<?=($arParams["ADDITIONAL"] ?? "")?>">
	<h3 class="modal__title modal__title_align_center">
		<?=($arParams["COMMENT_H3"] ?? "Комментарий")?>
	</h3>
    <div class="modal__content">
        <form class="form">
            <label class="form__line">
                <span class="form__label"><?=($arParams["COMMENT_LABEL"] ?? "Обоснование")?></span>
                <textarea class="form__textarea"></textarea>
            </label>

            <div class="form__controls form__controls_align_center modal__controls">
                <div class="form__control modal__control">
                    <button class="button button_width_full" type="button">Добавить</button>
                </div>
            </div>
        </form>
    </div>
</div>
