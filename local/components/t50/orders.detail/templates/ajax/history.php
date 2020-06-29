<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="table table_width_auto">
	<table class="table__main js-resize-table">
		<thead class="table__thead">
			<tr class="table__tr">
				<th class="table__th">
					<div class="order__field order__field_size_xxl">Свойство</div>
				</th>
				<th class="table__th">
					<div class="form__field form__field_size-m_s">До</div>
				</th>
				<th class="table__th">
					<div class="form__field form__field_size-m_s">После</div>
				</th>
				<th class="table__th">
					<div class="form__field form__field_size-m_s">Пользователь</div>
				</th>
				<th class="table__th">
					<div class="form__field form__field_size-m_s">Дата</div>
				</th>
				<th class="table__th">
					<div class="form__field form__field_size-m_s">Время</div>
				</th>
				<th class="table__th">
					<div class="order__field order__field_size_xxl">&nbsp;</div>
				</th>
			</tr>
		</thead>
		<tbody class="table__body">
			<? foreach($arResult["ITEMS"] as $item){ ?>
				<tr class="table__tr">
					<? if( $item["COMMON"] ){ ?>
					    <td class="table__td" colspan="3">
							<div class="order__field order__field_size_xxl">
								<b class="table__marked"><?=$item["COMMENT"]?></b>
							</div>
						</td>
					<? } else { ?>
						<td class="table__td">
							<div class="order__field order__field_size_xxl"><?=$item["WHERE"]?> - <b class="table__marked"><?=$item["FIELD"]?></b></div>
						</td>
						<td class="table__td">
							<div class="form__field form__field_size-m_s"><?=$item["BEFORE"]?></div>
						</td>
						<td class="table__td">
							<div class="form__field form__field_size-m_s"><?=$item["AFTER"]?></div>
						</td>
					<? } ?>

					<td class="table__td">
						<div class="form__field form__field_size-m_s"><?=$item["MANAGER"]?></div>
					</td>
					<td class="table__td">
						<div class="form__field form__field_size-m_s"><?=$item["DATE"]?></div>
					</td>
					<td class="table__td">
						<div class="form__field form__field_size-m_s"><?=$item["TIME"]?></div>
					</td>
					<td class="table__td">
						<div class="order__field order__field_size_xl">&nbsp;</div>
					</td>
				</tr>
			<? } ?>
		</tbody>
	</table>
</div>
<div class="panel__inner">
	<?
	$GLOBALS["APPLICATION"]->IncludeComponent(
	   "bitrix:main.pagenavigation",
	   "products",
	   array(
	      "NAV_OBJECT" => $arResult["NAV_OBJECT"],
	      "SEF_MODE" => "N",
	      "SHOW_COUNT" => "Y",
	      "SHOW_ALWAYS" => "Y",
	      "AJAX_MODE" => "Y",
	      "TRIPLE_LABEL" => ["изменение", "изменения", "изменений"],
	   ),
	   false
	);
	?>
</div>


<script type="text/javascript">
$(document).ready(function (){
	var pagenParams = "";
	T50PubSub.subscribe("pagination_history", function (pagenData){
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
		var data = {order_id: <?=($arResult["ORDER_ID"] ?? 0)?>};
		T50PubSub.send("load_history", T50Ajax.postHtml("orders.detail", "load_history", data, pagenParams))
	}
});
</script>