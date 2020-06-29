<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form class="form">
	<!-- begin .table-->
	<div class="table">
		<table class="table__main">
			<thead class="table__thead">
				<tr class="table__tr">
					<th class="table__th">Домен</th>
					<th class="table__th">Направление</th>
					<th class="table__th">Статус наличия</th>
					<th class="table__th">Цена сайта, руб.</th>
					<th class="table__th">Тип&nbsp;цены</th>
					<th class="table__th">Формула цены</th>
					<th class="table__th">Комиссия</th>
					<th class="table__th">Доставка</th>
					<th class="table__th">Установка</th>
					<th class="table__th">Cкидка,&nbsp;%</th>
				</tr>
			</thead>
			<tbody class="table__tbody">
				<? foreach($arResult["SHOPS_DATA"] as $item){ ?>
				<tr class="table__tr">
					<td class="table__td">
						<div class="table__name">
							<a target="_blank" class="link link_style_trigger link_type_ninja" href="<?=$item["URL"]?>"><?=$item["HOST"]?></a>
						</div>
					</td>
					<td class="table__td"><?=$item["GROUP"]?></td>
					<td class="table__td">
						<?
						$APPLICATION->includeComponent("t50:hand_and_data", "", [
							"KEY" => "avail_shop",
							"PRODUCT_ID" => $arResult["PRODUCT"]["ID"],
							"BIND_ID" => $item["UF_SHOP"],
							"VALUE" => $item["AVAIL"],
							"AUTO_VALUE" => $item["UF_AVAIL"],
							"IS_MANUAL" => (bool) $item["UF_MANUAL_AVAIL"],
							"COMMENT" => $item["COMMENT"],
							"CITY" => $arParams["CITY"],
						]);
						?>
					</td>
					<td class="table__td">
						<?
						$APPLICATION->includeComponent("t50:hand_and_data", "", [
							"KEY" => "sale",
							"PRODUCT_ID" => $arResult["PRODUCT"]["ID"],
							"BIND_ID" => $item["UF_SHOP"],
							"VALUE" => $item["SALE"],
							"AUTO_VALUE" => $item["UF_PRICE_SALE"],
							"IS_MANUAL" => (bool) $item["UF_MANUAL_PRICE"],
							"COMMENT" => $item["COMMENT"],
							"CITY" => $arParams["CITY"],
						]);
						?>
					</td>
					<td class="table__td"><?=$item["FORMULA"]["MODE"]?></td>
					<td class="table__td">
						<div class="form__panel form__panel_style_readonly form__panel_type_vanishing">
							<div class="form__field form__field_size-s_l">
								<?=$item["FORMULA"]["TITLE"]?>
							</div>
							<div class="form__info">
								<a href="#formula_panel" class="js-modal" <?=T50Html::dataAttrs($item["FORMULA"])?>>
									<svg class="info-tooltip__icon">
										<use xlink:href="<?=T50Html::getSvg("icon_info")?>"></use>
									</svg>
								</a>
							</div>
						</div>
					</td>
					<td class="table__td"><?=T50Html::fnum($item["COMMISSION"])?></td>
					<td class="table__td"><?=( $item["DELIVERY"] ? "Бесплатно" : "Платно" )?></td>
					<td class="table__td"><?=( $item["INSTALL"] ? "Бесплатно" : "Платно" )?></td>
					<td class="table__td"><?=$item["DISCONT"]?></td>
				</tr>
				<? } ?>
			</tbody>
		</table>
	</div>
</form>