<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if( empty($arResult["ITEMS"]) )
	return;
?>

<div class="page__item">

	<div class="panel">

		<div class="table table_style_bordered">
			<table class="table__main">
				<thead class="table__thead">
					<tr class="table__tr">
						<th class="table__th">unid</th>
						<th class="table__th">Название</th>
						<th class="table__th">Модель</th>
						<th class="table__th">Раздел</th>
						<th class="table__th">Бренд</th>
						<th class="table__th">Магазин</th>
						<th class="table__th">Формула</th>
						<th class="table__th">Продажная цена</th>
						<th class="table__th">Закупочная цена</th>
						<th class="table__th table__th_border_right">Комиссия</th>

						<? foreach($arResult["USED_SUPPLIERS"] as $supplierId){
							$supplierName = $arResult["SUPPLIERS"][$supplierId];
						?>
							<th class="table__th"><?=$supplierName?></th>

							<? if( $arResult["SHOW_COND_AND_SALE"] ){ ?>
							    <th class="table__th"><?=$supplierName?> (продажная)</th>
							    <th class="table__th table__th_border_right"><?=$supplierName?> (условие)</th>
							<? } ?>

						<? } ?>
					</tr>
				</thead>
				<tbody class="table__tbody">
					<? foreach($arResult["ITEMS"] as $item){ ?>
						<tr class="table__tr">
							<td class="table__td"><?=$item["ID"]?></td>
							<td class="table__td"><?=$item["UF_TITLE"]?></td>
							<td class="table__td"><?=$item["UF_MODEL_PRINT"]?></td>
							<td class="table__td"><?=$item["CATEGORY"]?></td>
							<td class="table__td"><?=$item["BRAND"]?></td>
							<td class="table__td"><?=$item["SHOP"]?></td>
							<td class="table__td"><?=$item["FORMULA"]?></td>
							<td class="table__td"><?=T50Html::fnum($item["SALE"])?></td>
							<td class="table__td"><?=T50Html::fnum($item["PURCHES"])?></td>
							<td class="table__td table__th_border_right">
								<?=T50Html::fnum($item["BEST_COMMISSION"])?>
							</td>

							<? foreach($arResult["USED_SUPPLIERS"] as $supplierId){
								$supplier = $item["SUPPLIERS"][$supplierId];
							?>
								<td class="table__td <?=($supplier["is_best"] ? "table__td_style_good " : "")?>">
									<?=T50Html::fnum($supplier["purches"])?>
								</td>

								<? if( $arResult["SHOW_COND_AND_SALE"] ){ ?>
									<td class="table__td"><?=T50Html::fnum($supplier["sale"])?></td>
									<td class="table__td table__th_border_right"><?=$supplier["cond"]?></td>
								<? } ?>

							<? } ?>
						</tr>
					<? } ?>
				</tbody>
			</table>
		</div>
		<div class="panel__foot panel__foot_type_full">
			<div class="panel__main">
				<div class="panel__controls panel__controls_type_super-separated">
					<div class="panel__control">
						<a href="<?=$arResult["LINK_OUT_TO_EXCEL"]?>" class="link link_type_export">
							<svg class="link__icon">
								<use xlink:href="<?=T50Html::getSvg("icon_download")?>"></use>
							</svg>
							<span class="link__wrapper">Экспортировать таблицу в Excel</span>
						</a>
					</div>
					<div class="panel__control">
						<a href="<?=$arResult["LINK_OUT_TO_CSV"]?>" class="link link_type_export-secondary">
							<svg class="link__icon">
								<use xlink:href="<?=T50Html::getSvg("icon_download")?>"></use>
							</svg>
							<span class="link__wrapper">Экспортировать таблицы в CSV</span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?//echo "<pre>\$arResult "; print_r($arResult); echo "</pre>";?>