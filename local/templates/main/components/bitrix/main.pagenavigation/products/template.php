<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="panel__foot">
	<div class="panel__main">
	<? if( !empty($arResult["ITEMS"]) ){ ?>
		<ul class="pagination">
			<? foreach($arResult["ITEMS"] as $item){ ?>
				<? if( $item["is_gap"] ){ ?>
				    <li class="pagination__item">...</li>
				<? } else { ?>
					<li class="pagination__item">
						<? if( $arParams["AJAX_MODE"] == "Y" ){ ?>
							<a href="#" class="pagination__link js_pagen_link <?=( $item["active"] ? "pagination__link_state_active" : "" )?>" <?=T50Html::dataAttrs($item["ajax_data"])?>>
								<?=$item["text"]?>
							</a>
						<? } else { ?>
							<a href="<?=$item["url"]?>" class="pagination__link <?=( $item["active"] ? "pagination__link_state_active" : "" )?>" >
								<?=$item["text"]?>
							</a>
						<? } ?>
					</li>
				<? } ?>
			<? } ?>
		</ul>
	<? } ?>
	</div>
	<div class="panel__addition">
		<div class="panel__addition-label">
			Всего <?=T50Text::wordEnding($arResult["RECORD_COUNT"], $arParams["TRIPLE_LABEL"] ?? ["позиция", "позиции", "позиций"])?>
		</div>
		<select class="js-select panel__view-params js_pagen_sizes">
			<? foreach($arResult["SIZES"] as $item){ ?>
				<option <?=( $item["active"] ? "selected" : "" )?> value="<?=$item["url"]?>" <?=T50Html::dataAttrs($item["ajax_data"])?>>
					<?=$item["text"]?>
				</option>
			<? } ?>
		</select>
	</div>
</div>

<? if( $arParams["AJAX_MODE"] == "Y" ){ ?>

<script type="text/javascript">
$(document).ready(function (){
	if( window.paginationInit )
		return

	function sendPagination(data){
		T50PubSub.send("pagination_" + data.name, data)
	}
	$(document).on('change', '.js_pagen_sizes', function (e){
		var data = $(this).find("option:selected").data()
		sendPagination(data)
	});

	$(document).on('click', '.js_pagen_link', function (e) {
		var data = $(this).data()
	    e.preventDefault();
		sendPagination(data)
		return false
	});

	window.paginationInit = true
});
</script>

<? } else { ?>

<script type="text/javascript">
$(document).ready(function (){
	$(".js_pagen_sizes").change(function(){
			document.location.href = $(this).val();
	});
});
</script>

<? } ?>
