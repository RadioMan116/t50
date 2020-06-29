<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="modal modal_size_small" id="create_new_order">
	<h1 class="modal__title modal__title_align_center">Создание заказа</h1>
	<div class="modal__content">
		<form class="form" method="post" action="/orders/create/">
			<?=bitrix_sessid_post()?>
			<label class="form__line">
                <label class="form__label">Выбор магазина</label>
                <? foreach($arResult["SHOPS"] as $id => $shop){ ?>
                	<?=T50Html::radioLabel("shop", $id, "i", $shop, "l")?>
                <? } ?>
            </label>
            <div class="form__controls form__controls_align_center modal__controls">
                <div class="form__control modal__control">
                    <button class="button button_width_full" disabled type="submit">Создать</button>
                </div>
            </div>
        </form>
	</div>
</div>