<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// echo "<pre>\$arResult "; print_r($arResult); echo "</pre>";
?>

<form action="" class="page__form page__item">
	<!-- begin .panel-->
	<div class="panel">
		<div class="group-editing js-group-editing-panel">
			<div class="group-editing__head">
				<div class="group-editing__title">
					<h3 class="panel__subtitle">Товары в каталоге магазина</h3>
				</div>
				<div class="group-editing__panel">
					<div class="group-editing__pill group-editing__pill_state_hidden">
						Выбрано <span class="group-editing__value">0</span> товара.
						<a href="#formula_panel" data-city="<?=$arParams["CITY"]?>" class="group-editing__link js-modal">Изменить</a>
					</div>
				</div>
			</div><!-- begin .table-->
			<div class="table table_type_reduced table_style_light">
				<table class="table__main">
					<thead class="table__thead">
						<tr class="table__tr">
							<? if( $arResult["IS_EDITABLE"] ){ ?>
							<th class="table__th">
								<div class="check-elem js-group-check" data-target=".check-elem__input" data-scope=".table"><?=T50Html::checkboxLabel("item", "Y", "check-elem__input", "", "check-elem__label")?></div>
							</th>
							<? } ?>
							<th class="table__th">Бренд</th>
							<th class="table__th">Категория</th>
							<th class="table__th">Модель</th>
							<th class="table__th">Статус наличия</th>
							<th class="table__th">РРЦ</th>
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
						<? foreach($arResult["ITEMS"] as $item){?>
							<tr class="table__tr">
								<? if( $arResult["IS_EDITABLE"] ){ ?>
									<td class="table__td">
										<div class="check-elem js-group-editing-select">
											<?=T50Html::checkboxLabel("product[]", $item["PRODUCT_PR_ID"], "check-elem__input js_selected_ids", "", "check-elem__label")?>
										</div>
									</td>
								<? } ?>
								<td class="table__td"><?=$item["BRAND"]?></td>
								<td class="table__td"><?=$item["CATEGORY"]?></td>
								<td class="table__td">
									<a class="link link_style_classic" href="<?=$item["URL"]?>">
										<?=$item["UF_MODEL_PRINT"]?>
									</a>
								</td>
								<td class="table__td">
									<?
									$APPLICATION->includeComponent("t50:hand_and_data", "", [
										"KEY" => "avail_shop",
										"PRODUCT_ID" => $item["ID"],
										"BIND_ID" => $item["PRODUCT_PR_UF_SHOP"],
										"VALUE" => $item["AVAIL"],
										"AUTO_VALUE" => $item["PRODUCT_PR_UF_AVAIL"],
										"IS_MANUAL" => (bool) $item["PRODUCT_PR_UF_MANUAL_AVAIL"],
										"COMMENT" => $item["COMMENT"],
										"CITY" => $arParams["CITY"],
									]);
									?>
								</td>
								<td class="table__td">
									<div class="form__field form__field_size-m_l">
										<input type="text" value="<?=T50Html::fnum($item["PRODUCT_PR_UF_RRC"])?>" disabled class="form__input">
									</div>
								</td>
								<td class="table__td">
									<?
										$APPLICATION->includeComponent("t50:hand_and_data", "", [
											"KEY" => "sale",
											"PRODUCT_ID" => $item["ID"],
											"BIND_ID" => $item["PRODUCT_PR_UF_SHOP"],
											"VALUE" => $item["SALE"],
											"AUTO_VALUE" => $item["PRODUCT_PR_UF_PRICE_SALE"],
											"IS_MANUAL" => (bool) $item["PRODUCT_PR_UF_MANUAL_PRICE"],
											"COMMENT" => $item["COMMENT"],
											"CITY" => $arParams["CITY"],
										]);
										?>
								</td>
								<td class="table__td">
									<div class="form__field form__field_size-m_l">
										<input type="text" value="<?=T50Html::fnum($item["PRODUCT_PR_UF_PRICE_PURCHASE"])?>" disabled class="form__input">
									</div>
								</td>
								<td class="table__td"><?=T50Html::fnum($item["COMMISSION"])?></td>
								<td class="table__td">
									<div class="form__panel form__panel_style_readonly form__panel_type_vanishing">
										<div class="form__field form__field_size-m_xxl">
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
			   ),
			   false
			);
			?>
		</div>
	</div>
</form>

<?
$APPLICATION->IncludeComponent("t50:modal", "", ["modal" => "manual"]);
$APPLICATION->IncludeComponent("t50:modal", "", ["modal" => "formula_panel"]);
?>

