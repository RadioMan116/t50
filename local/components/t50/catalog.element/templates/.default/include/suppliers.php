<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="panel__view-controls">
	<div class="view-controls">
		<div class="view-controls__item">
			<a href="#pick_up_points" class="link link_type_map-trigger js-modal">
				<svg class="link__icon"><use xlink:href="<?=T50Html::getSvg("icon_map")?>"></use></svg>
				<span class="link__wrapper">Пункты самовывоза на карте</span>
			</a>
		</div>
		<div class="view-controls__item">
			<div class="check-elem js-check-toggle" data-toggle-text="Скрыть все предложения" data-toggle-scope=".tabs__scope " data-toggle-class="table__tr_state_visible" data-toggle-target=".table__tr_state_collapse">
				<input type="checkbox" id="show_all_offers_<?=( $SHOW_TABS ? "all" : "" )?>" class="check-elem__input" />
				<label for="show_all_offers_<?=( $SHOW_TABS ? "all" : "" )?>" class="check-elem__label">Показать все предложения</label>
			</div>
		</div>
	</div>
</div>
<div class="table form">
	<table class="table__main">
		<thead class="table__thead">
			<tr class="table__tr">
				<th class="table__th">Название поставщика</th>
				<th class="table__th">Статус наличия</th>
				<th class="table__th">Срок поставки</th>
				<th class="table__th">Цена закупки, руб.</th>
				<th class="table__th">Цена партнеров, руб.</th>
				<th class="table__th">Обновление прайса</th>
			</tr>
		</thead>
		<tbody class="table__tbody">
			<?php foreach ($arResult["MARKET_DATA"] as $supplierId => $item){?>
			<tr class="table__tr <?=( $item["HIDE"] ? "table__tr_state_collapse" : "" )?>">
				<td class="table__td">
					<div class="table__name">
						<a class="link link_style_trigger link_type_ninja" href="<?=$item["SUPPLIER_URL"]?>" target="_blank">
							<?=$item["TITLE"]?>
						</a>
					</div>
				</td>
				<td class="table__td">
					<?
					$APPLICATION->includeComponent("t50:hand_and_data", "", [
						"KEY" => "avail_supplier",
						"PRODUCT_ID" => $arResult["PRODUCT"]["ID"],
						"BIND_ID" => $item["SUPPLIER_ID"],
						"VALUE" => $item["AVAIL"],
						"AUTO_VALUE" => $item["AUTO_AVAIL"],
						"IS_MANUAL" => (bool) $item["IS_MANUAL_AVAIL"],
						"COMMENT" => $item["COMMENT"],
						"CITY" => $arParams["CITY"],
					]);
					?>
				</td>
				<td class="table__td"><?=$item["DATE_SUPPLY"]?></td>
				<td class="table__td">
					<?
					$APPLICATION->includeComponent("t50:hand_and_data", "", [
						"KEY" => "purchase",
						"PRODUCT_ID" => $arResult["PRODUCT"]["ID"],
						"BIND_ID" => $item["SUPPLIER_ID"],
						"VALUE" => $item["PURCHASE"],
						"AUTO_VALUE" => $item["AUTO_PURCHASE"],
						"IS_MANUAL" => (bool) $item["IS_MANUAL_PURCHASE"],
						"COMMENT" => $item["COMMENT"],
						"CITY" => $arParams["CITY"],
					]);
					?>
				</td>
				<td class="table__td"><?=T50Html::fnum($item["SALE"])?></td>
				<td class="table__td"><?=$item["PRICE_UPDATE_DATE"]?></td>
			</tr>
			<? } ?>
		</tbody>
	</table>
</div>
