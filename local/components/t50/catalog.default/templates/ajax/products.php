<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;
$availClassTitle = [
	AVAIL_IN_STOCK => ["status_style_in-stock", "В наличии"],
	AVAIL_BY_REQUEST => ["status_style_under-order", "Под заказ"],
	AVAIL_OUT_OF_STOCK => ["status_style_ended", "Нет в наличии"],
	AVAIL_DISCONTINUED => ["status_style_ended", "Снят с производства"],
];
// $arResult["TYPE"]
$title = "Добавление к заказу";
?>

<div class="panel__incut">
	<div class="panel__title panel__title_type_subtitle"><?=$title?></div>
	<div class="table table_style_simple table_width_auto">
		<table class="table__main js_form">
			<tr class="table__tr">
				<td class="table__td">
					<label class="form__line form__line_type_close order__field order__field_size_l">
						<div class="form__label">Магазин</div>
						<?=T50Html::select("shop", $arResult["INITIAL_DATA"]["SHOP"]["DATA"], ["cls" => "js-select form__select", "empty" => "-"])?>
					</label>
				</td>
				<td class="table__td">
					<label class="form__line form__line_type_close order__field order__field_size_l">
						<div class="form__label">Бренд</div>
						<?=T50Html::select("brand", $arResult["INITIAL_DATA"]["BRAND"]["DATA"], ["cls" => "js-select form__select", "empty" => "-"])?>
					</label>
				</td>
				<td class="table__td">
					<label class="form__line form__line_type_close order__field order__field_size_l">
						<div class="form__label">Категория</div>
						<?=T50Html::select("category", $arResult["INITIAL_DATA"]["CATEGORY"]["DATA"], ["cls" => "js-select form__select", "empty" => "-"])?>
					</label>
				</td>
				<td class="table__td">
					<div class="form__label">&nbsp;</div>
					<div class="form__field form__field_size-xxl_s">
						<input type="text" name="model" value="<?=$_REQUEST["model"]?>" class="form__input">
					</div>
				</td>
				<td class="table__td">
					<div class="form__label">&nbsp;</div>
					<input type="hidden" name="exchange_basket_id" value="<?=$_REQUEST["exchange_basket_id"]?>">
					<button class="button button_style_dark button_type_concrete js_search_products" type="button">
						Искать
					</button>
				</td>
			</tr>
		</table>
	</div>

	<div class="form">
		<div class="table table_type_reduced table_style_light">
			<table class="table__main">
				<thead class="table__thead">
					<tr class="table__tr">
						<th class="table__th">Бренд</th>
						<th class="table__th">Категория</th>
						<th class="table__th">Модель</th>
						<th class="table__th">Статус наличия</th>
						<th class="table__th">Цена продажи, руб.</th>
						<th class="table__th">Цена закупки, руб.</th>
						<th class="table__th">Комиссия, руб.</th>
						<th class="table__th">Формула цены</th>
						<th class="table__th">Доставка</th>
						<th class="table__th">Установка</th>
						<th class="table__th">Новинка</th>
						<th class="table__th">UNID</th>
					</tr>
				</thead>
				<tbody class="table__tbody">
					<? foreach($arResult["ITEMS"] as $item){ ?>
					<tr class="table__tr">
						<td class="table__td">
							<div class="check-elem">
								<?=T50Html::radioLabel("product[]", $item["PRODUCT_PR_ID"], "check-elem__input js_select_product", $item["BRAND"], "check-elem__label")?>
							</div>
						</td>
						<td class="table__td"><?=$item["CATEGORY"]?></td>
						<td class="table__td">
							<a class="link" href="<?=$item["URL"]?>"><?=$item["UF_MODEL_PRINT"]?></a>
						</td>
						<td class="table__td">
							<div class="status status_width_full <?=$availClassTitle[$item["AVAIL"]][0]?>">
								<?=$availClassTitle[$item["AVAIL"]][1]?>
							</div>
						</td>
						<td class="table__td"><?=T50Html::fnum($item["SALE"])?></td>
						<td class="table__td"><?=T50Html::fnum($item["PRODUCT_PR_UF_PRICE_PURCHASE"])?></td>
						<td class="table__td"><?=T50Html::fnum($item["COMMISSION"])?></td>
						<td class="table__td"><?=$item["FORMULA"]["TITLE"]?></td>
						<td class="table__td">
							<?=( $item["PRODUCT_PR_UF_FLAG_FREE_DELIVER"] ? "Бесплатно" : "Платно" )?>
						</td>
						<td class="table__td">
							<?=( $item["PRODUCT_PR_UF_FLAG_FREE_INSTALL"] ? "Бесплатно" : "Платно" )?>
						</td>
						<? if( $item["UF_FLAG_NEW"] ){ ?>
							<td class="table__td table__td_style_good">Новинка</td>
						<? } else { ?>
							<td class="table__td">-</td>
						<? } ?>
						<td class="table__td"><?=$item["ID"]?></td>
					</tr>
					<? } ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="panel__inner">
		<?
		$APPLICATION->IncludeComponent(
		   "bitrix:main.pagenavigation",
		   "products",
		   array(
		      "NAV_OBJECT" => $arResult["NAV_OBJECT"],
		      "SEF_MODE" => "N",
		      "SHOW_COUNT" => "Y",
		      "SHOW_ALWAYS" => "Y",
		      "AJAX_MODE" => "Y",
		   ),
		   false
		);
		?>
	</div>

</div>
<script type="text/javascript">
$(document).ready(function (){
	let data = <?=( empty($_POST) ? "{}" : CUtil::PhpToJSObject($_POST) )?>;
	let pagenParams = "";

	$(".js_search_products").click(function (){
		let form = $(this).closest(".js_form");
		$.each(form.find("input, select"), function(index, item){
			data[$(this).attr("name")] = $(this).val();
		})
		pagenParams = "products=page-1-size-<?=$arResult["NAV_OBJECT"]->getPageSize()?>";
		load()
	});
	$(".js_select_product").click(function (){
		T50PubSub.send("select_product_for_basket", {
			exchange_basket_id: $("input[name='exchange_basket_id']").val(),
			product_price_id: $(this).val(),
		})
	});
	T50PubSub.subscribe("pagination_products", function (pagenData){
		let paramsTemplate = "{name}=page-{page}-size-{size}";
		try{
			pagenParams = paramsTemplate
						.replace("{name}", pagenData.name)
						.replace("{page}", pagenData.page)
						.replace("{size}", pagenData.size);
			load()
		} catch( e ){}
	})
	function load(){
		T50PubSub.send("load_products", T50Ajax.postHtml("catalog.default", "load_products", data, pagenParams))
	}

	window.common.jsSelectInit();
});
</script>