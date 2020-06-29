<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
function showSortBlock($title){
	static $index = 0;
	?>
		<div class="sort <?=( $index == 0 ? "sort_align_left" : "" )?>">
			<div class="sort__wrapper"><?=$title?></div>
			<div class="sort__trigger">&nbsp;</div>
			<div class="sort__dropdown">
				<div class="sort__label">Сортировка</div>
				<div class="sort__item">
					<button type="button" data-sort="[[<?=$index?>,0]]" class="sort__link-trigger js-table-sort-trigger">По возрастанию</button></div>
				<div class="sort__item">
					<button type="button" data-sort="[[<?=$index?>,1]]" class="sort__link-trigger js-table-sort-trigger">По убыванию</button>
				</div>
				<div class="sort__item">
					<button type="button" data-sort="data-sort" class="sort__link-trigger js-table-sort-trigger">Отключить</button></div>
			</div>
		</div>
	<?
	$index ++;
}

?>
<div class="panel js-scroll-to-this">
	<div class="panel__head">
		<div class="panel__head-main">
			<h2 class="panel__title panel__title_type_subtitle panel__title_style_uppercase">
				Таблица заказов
			</h2>
		</div>
		<div class="panel__head-controls">
			<div class="panel__head-control">
				<div class="dual-panel dual-panel_width_auto">
					<div class="dual-panel__row">
						<div class="dual-panel__col">
							<a class="link link_style_marked-trigger link_type_important js-modal" href="#columns_editor">Редактировать таблицу</a>
						</div>
						<div class="dual-panel__col">
							<a class="button js-modal" href="#create_new_order">Создать заказ</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="panel__inner">
		<form class="form">
			<div class="table table_type_reduced table_valign_top table_type_inner">
				<table class="table__main js-resize-table">
					<thead class="table__thead">
						<tr class="table__tr">
							<? foreach($arResult["TABLE_VIEW"]["COLUMNS"] as $column){ ?>
								<th class="table__th">
									<?=showSortBlock($arResult["TABLE_VIEW"]["MAP"][$column])?>
								</th>
							<? } ?>
						</tr>
					</thead>
					<tbody class="table__tbody">
						<? foreach($arResult["ITEMS"] as $orderItem){
							  $installations = $orderItem["INSTALLATIONS"];
							  foreach($orderItem["BASKET"] as $basketIndex => $basketItem){
							    $showOrderItem = ( $basketIndex == 0 );
							    $basketId = $basketItem["ID"];
							    $delivery = $orderItem["DELIVERY"][$basketId];
							    $account = $orderItem["ACCOUNT"][$basketId];
							    $fine = $orderItem["FINES"]
						?>

							<tr class="table__tr">
<? foreach($arResult["TABLE_VIEW"]["COLUMNS"] as $column){ ?>
	<? switch( $column ){
		case "shop":
	?>
		<td class="table__td">
			<? if( $showOrderItem ){ ?>
				<a class="link link_style_trigger link_type_ninja" href="#">
					<?=$orderItem["SHOP"]?>
				</a>
			<? } ?>
		</td>
	<?
		break;


		case "number_source":
	?>
		<td class="table__td">
			<? if( $showOrderItem ){ ?>
				<a class="link" href="/orders/<?=$orderItem["ID"]?>/">
					№ <?=$orderItem["ID"]?>
					<? if( $orderItem["UF_REMOTE_ORDER"] > 0 ){ ?>
					    / <?=$orderItem["UF_REMOTE_ORDER"]?>
					<? } ?>
					</a>
				<br>
				<?=$orderItem["SOURCE"]?>
			<? } ?>
		</td>
	<?
		break;


		case "supplier_number":
	?>
		<td class="table__td">
			<?=$basketItem["SUPPLIER"]?>
			<br>
			<?=$orderItem["ORDER_AC_UF_ACCOUNT"]?>
		</td>
	<?
		break;


		case "status":
	?>
    	<td class="table__td marked">
    		<? if( $showOrderItem ){ ?>
    			<?=$orderItem["STATUS"]?>
    		<? } ?>
    	</td>
	<?
		break;


		case "date_create":
	?>
		<td class="table__td">
			<? if( $showOrderItem ){ ?>
				<?=$orderItem["DATE_CREATE"]?>
			<? } ?>
		</td>
	<?
		break;


		case "date_delivery":
	?>
		<td class="table__td">
			<?=$delivery["DATE"]?>
		</td>
	<?
		break;


		case "basket":
	?>
    	<td class="table__td table__td_style_strict">
			<a class="link" href="<?=$basketItem["UF_PRODUCT_URL"]?>">
				<?=$basketItem["UF_NAME"]?>
			</a>
		</td>
	<?
		break;


		case "quantity":
	?>
    	<td class="table__td">
    		<?=$basketItem["UF_QUANTITY"]?>
    	</td>
	<?
		break;


		case "site_price":
	?>
    	<td class="table__td">
    		<?=T50Html::fnum($basketItem["UF_START_PRICE_SALE"])?>
    	</td>
	<?
		break;


		case "sale":
	?>
    	<td class="table__td">
    		<?=T50Html::fnum($basketItem["UF_PRICE_SALE"])?>
    	</td>
	<?
		break;


		case "commission":
	?>
    	<td class="table__td">
    		<?=T50Html::fnum($basketItem["UF_COMMISSION"])?>
    	</td>
	<?
		break;


		case "commission_supplier":
	?>
    	<td class="table__td flat-status_passed">
    		<?=T50Html::fnum($basketItem["UF_COM_SUPPLIER"])?>
    	</td>
	<?
		break;


		case "client":
	?>
    	<td class="table__td">
    		<? if( $showOrderItem ){ ?>
    			<?=$orderItem["ORDER_CL_UF_FIO"]?>
    		<? } ?>
    	</td>
	<?
		break;


		case "manager":
	?>
    	<td class="table__td">
    		<? if( $showOrderItem ){ ?>
    			<?=$orderItem["MANAGER"]?>
    		<? } ?>
    	</td>
	<?
		break;


		case "city":
	?>
    	<td class="table__td">
    		<? if( $showOrderItem ){ ?>
    			<?=$orderItem["CITY"]?>
    		<? } ?>
    	</td>
    <?
		break;


		case "purchase":
	?>
    	<td class="table__td">
    		<?=T50Html::fnum($basketItem["UF_PRICE_PURCHASE"])?>
    	</td>
	<?
		break;


		case "agreed_client":
	?>
    	<td class="table__td">
    		<?=($orderItem["FLAGS"]["agreed_client"] ? "Да" : "Нет" )?>
    	</td>
	<?
		break;


		case "agreed_supplier":
	?>
    	<td class="table__td">
    		<?=($orderItem["FLAGS"]["agreed_supplier"] ? "Да" : "Нет" )?>
    	</td>
	<?
		break;
		case "instal":
	?>
    	<td class="table__td">
    		-
    	</td>
	<?
		break;


		case "fines":
	?>
    	<td class="table__td">
    		<? if( $showOrderItem ){ ?>
    			<?=$fine?>
    		<? } ?>
    	</td>
	<?
		break;


		case "test":
	?>
    	<td class="table__td">
    		<?=( $orderItem["UF_TEST"] ? "тест" : "" )?>
    	</td>
	<?
		break;


		case "pay_type":
	?>
    	<td class="table__td">
    		<?=$orderItem["PAYMENT_TYPE"]?>
    	</td>
	<?
		break;
	}// end switch ?>
<? } // end foreach column?>
							</tr>
							<? } // end foreach basketItem?>
						<? } // end foreach orderItem ?>
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
		      "TRIPLE_LABEL" => ["заказ", "заказа", "заказов"],
		   ),
		   false
		);
		?>
	</div>
</div>