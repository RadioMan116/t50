<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="panel">
	<div class="panel__head">
		<div class="panel__head-main">
			<h2 class="panel__title panel__title_type_subtitle panel__title_style_uppercase">
				Таблица суммирующая выбранные данные
			</h2>
		</div>
	</div>
	<div class="table table_width_auto">
		<table class="table__main js-resize-table">
			<thead class="table__thead">
				<tr class="table__tr">
					<th style="width: 248px;" class="table__th">Статусы</th>
					<th class="table__th">Товары</th>
					<th class="table__th">Заказы</th>
					<th class="table__th">Цена продажи</th>
					<th class="table__th">Цена закупки</th>
					<th class="table__th">Комиссия</th>
					<th class="table__th">Комиссия поставщика</th>
					<th class="table__th">&nbsp;</th>
				</tr>
			</thead>
			<tbody class="table__tbody">
				<? foreach($arResult["SUM"]["SUM_BY_STATUS"] as $item){ ?>
					<tr class="table__tr">
						<td class="table__td marked"><?=$item["STATUS"]?></td>
						<td class="table__td"><?=T50Html::fnum($item["PRODUCTS"])?></td>
						<td class="table__td"><?=T50Html::fnum($item["ORDERS"])?></td>
						<td class="table__td"><?=T50Html::fnum($item["SALE"])?></td>
						<td class="table__td"><?=T50Html::fnum($item["PURCHASE"])?></td>
						<td class="table__td"><?=T50Html::fnum($item["COMMISSION"])?></td>
						<td class="table__td flat-status_passed marked">
							<?=T50Html::fnum($item["SUPPL_COMMISSION"])?>
						</td>
						<td class="table__td">&nbsp;</td>
					</tr>
				<? } ?>

				<tr class="table__tr">
					<td class="table__td marked uppercase">Итого</td>
					<td class="table__td marked">
						<?=T50Html::fnum($arResult["SUM"]["SUM_TOTAL"]["PRODUCTS"])?>
					</td>
					<td class="table__td marked">
						<?=T50Html::fnum($arResult["SUM"]["SUM_TOTAL"]["ORDERS"])?>
					</td>
					<td class="table__td marked">
						<?=T50Html::fnum($arResult["SUM"]["SUM_TOTAL"]["SALE"])?>
					</td>
					<td class="table__td marked">
						<?=T50Html::fnum($arResult["SUM"]["SUM_TOTAL"]["PURCHASE"])?>
					</td>
					<td class="table__td marked">
						<?=T50Html::fnum($arResult["SUM"]["SUM_TOTAL"]["COMMISSION"])?>
					</td>
					<td class="table__td flat-status_passed marked">
						<?=T50Html::fnum($arResult["SUM"]["SUM_TOTAL"]["SUPPL_COMMISSION"])?>
					</td>
					<td class="table__td">&nbsp;</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="panel__foot panel__foot_type_full">
		<div class="panel__main">
			<a href="#" class="link link_type_import"><svg class="link__icon"><use xlink:href="<?=T50Html::getSvg("icon_download")?>"></use></svg>Экспортировать таблицы в Excel</a>
		</div>
	</div>
</div>