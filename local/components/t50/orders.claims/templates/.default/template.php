<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="grid-12__container grid-12__container_width_full">
	<div class="page__item">
		<?T50Html::inc("search_model")?>
	</div>
	<div class="page__item">
		<? include __DIR__ . "/filter.php"?>
	</div>
	<div class="page__item">

		<div class="panel">
			<div class="panel__head">
				<div class="panel__head-main">
					<h2 class="panel__title panel__title_type_subtitle">Таблица рекламаций</h2>
				</div>
			</div>
			<div class="panel__inner">
				<form class="form">

					<div class="table table_type_reduced table_valign_top table_type_inner">
						<table class="table__main js-resize-table__for_claim">
							<thead class="table__thead">
								<tr class="table__tr">
									<th class="table__th">№ заказа</th>
									<th class="table__th">Магазин</th>
									<th class="table__th">Поставщик</th>
									<th class="table__th">Отв. менеджер</th>
									<th class="table__th">Контакты клиента</th>
									<th class="table__th">Дата доставки</th>
									<th class="table__th">Дата обращения</th>
									<th class="table__th">Причина</th>
									<th class="table__th">Требование клиента</th>
									<th class="table__th">Хронология</th>
									<th class="table__th">Ошибка магазина</th>
									<th class="table__th">Дата решения</th>
									<th class="table__th">Итог</th>
								</tr>
							</thead>
							<tbody class="table__tbody">
								<? foreach($arResult["ITEMS"] as $item){ ?>
								<tr class="table__tr">
									<td class="table__td">
										<a class="link link_style_classic" href="/orders/<?=$item["ORDER_ID"]?>/#reclamation">
											№ <?=$item["ORDER_ID"]?>
										</a>
									</td>
									<td class="table__td"><?=$item["SHOP"]?></td>
									<td class="table__td">
										<?=$item["SUPPLIER"]?>
										<?=$item["ACCOUNT"]?>
									</td>
									<td class="table__td"><?=$item["MANAGER"]?></td>
									<td class="table__td">
										<div class="collapse-panel">
											<div class="collapse-panel__content collapse-panel__content_state_collapse">
												<?=$item["CLIENT"]?>
											</div>
											<div class="collapse-panel__trigger">
												<a class="link link_style_classic js-collapse-trigger" href="#" data-text="Скрыть">Показать</a>
											</div>
										</div>
									</td>
									<td class="table__td"><?=$item["DATE_DELIVERY"]?></td>
									<td class="table__td"><?=$item["DATE_REQUEST"]?></td>
									<td class="table__td"><?=$item["REASON"]?></td>
									<td class="table__td"><?=$item["REQUIREMENT"]?></td>
									<td class="table__td">
										<div class="collapse-panel">
											<div class="collapse-panel__content collapse-panel__content_state_collapse">
												<?=$item["HISTORY"]?>
											</div>
											<div class="collapse-panel__trigger">
												<a class="link link_style_classic js-collapse-trigger" href="#" data-text="Скрыть">Показать</a>
											</div>
										</div>
									</td>
									<td class="table__td"><?=$item["ERROR"]?></td>
									<td class="table__td"><?=$item["UF_DATE_FINISH"]?></td>
									<td class="table__td"><?=$item["RESULT"]?></td>
								</tr>
								<? } ?>
							</tbody>
						</table>
					</div>
				</form>
				<?
				$APPLICATION->IncludeComponent(
				   "bitrix:main.pagenavigation",
				   "products",
				   array(
				      "NAV_OBJECT" => $arResult["NAV_OBJECT"],
				      "SEF_MODE" => "N",
				      "SHOW_COUNT" => "Y",
				      "SHOW_ALWAYS" => "Y",
				      "TRIPLE_LABEL" => ["рекламация", "рекламации", "рекламаций"],
				   ),
				   false
				);
				?>

			</div>
		</div>
	</div>
</div>