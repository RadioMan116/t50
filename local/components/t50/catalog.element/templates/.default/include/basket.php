<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$paramsForCreateOrder = array_map(function ($item) use($arParams) {
	return [
		"product_price_id" => $item["ID"],
		"shop_id" => $item["UF_SHOP"],
		"price" => ( $item["SALE"] > 0 ? T50Html::fnum($item["SALE"]) . " руб." : "-" ),
		"shop_name" => $item["SHOP_NAME"],
		"city" => $arParams["CITY"],
	];
}, $arResult["SHOPS_DATA"]);
?>
<div class="modal" id="addToCart">
	<h3 class="modal__title modal__title_align_center">Добавить в корзину</h3>
	<div class="modal__content">
		<form class="form">
			<label class="form__line">
				<label class="form__label">Магазин</label>
				<select class="js-select modal__select js_product_shops">
					<? foreach($paramsForCreateOrder as $item){ ?>
						<option <?=T50Html::dataAttrs($item)?>><?=$item["shop_name"]?></option>
					<? } ?>
				</select>
			</label>
			<div class="product-simple">
				<div class="product-simple__illustration">
					<img src="" class="product-simple__image" alt="" role="presentation" />
				</div>
				<div class="product-simple__main">
					<div class="product-simple__title"><?=$arResult["PRODUCT"]["UF_TITLE"]?></div>
					<div class="product-simple__price js_product_price"></div>

					<div class="dual-panel">
						<div class="dual-panel__row">
							<div class="dual-panel__col">
								<div class="form__field form__field_size-l_l">
									<a class="button" href="#" id="add_to_cart_submit">Добавить в корзину</a>
								</div>
							</div>
							<div class="dual-panel__col">
								<div class="form__field form__field_size-s_m">
									<input autocomplete="off" class="form__input js_exist_order" type="text" placeholder="№ заказа">
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</form>
	</div>
</div>



