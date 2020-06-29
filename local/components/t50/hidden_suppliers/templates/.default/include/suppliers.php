<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="grid-12__row">
	<div class="grid-12__col grid-12__col_size_5">
		<div class="panel__label panel__label_style_light">Не учитывать поставщика по цене</div>
		<div class="filter-panel">
			<ul class="filter-panel__types filter-panel__types_type_near">
				<li class="filter-panel__type">

					<div class="link link_style_default-trigger js-group-check-trigger" data-target="input[type='checkbox']"
					 data-scope=".filter-panel" data-checked="true">Все</div>
				</li>
			</ul>
			<div class="filter-panel__list">

				<div class="showcase showcase_cols_auto">
					<div class="showcase__list">

						<? foreach($arResult["SUPPLIERS_CHUNKS"] as $chunk){ ?>
							<div class="showcase__item">
							<? foreach($chunk as $id => $title){
								$attrId = "hidden_by_price_{$id}";
								$checked = in_array($id, $arResult["DATA"]["UF_HIDDEN_BY_PRICE"]);
								$checked = ( $checked ? "checked" : "" );
								?>
								<div class="check-elem">
									<input type="checkbox" name="hidden_by_price[]" class="check-elem__input" id="<?=$attrId?>" value="<?=$id?>" <?=$checked?>>
									<label class="check-elem__label" for="<?=$attrId?>">
										<?=$title?>
									</label>
								</div>
							<? } ?>
							</div>
						<? } ?>

					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="grid-12__col grid-12__col_size_5">
		<div class="panel__label panel__label_style_light">Не учитывать поставщика по наличию</div>
		<div class="filter-panel">
			<ul class="filter-panel__types filter-panel__types_type_near">
				<li class="filter-panel__type">

					<div class="link link_style_default-trigger js-group-check-trigger" data-target="input[type='checkbox']"
					 data-scope=".filter-panel" data-checked="true">Все</div>
				</li>
			</ul>
			<div class="filter-panel__list">

				<div class="showcase showcase_cols_auto">
					<div class="showcase__list">


						<? foreach($arResult["SUPPLIERS_CHUNKS"] as $chunk){ ?>
							<div class="showcase__item">
							<? foreach($chunk as $id => $title){
								$attrId = "hidden_by_avail_{$id}";
								$checked = in_array($id, $arResult["DATA"]["UF_HIDDEN_BY_AVAIL"]);
								$checked = ( $checked ? "checked" : "" );
								?>
								<div class="check-elem">
									<input type="checkbox" name="hidden_by_avail[]" class="check-elem__input" id="<?=$attrId?>" value="<?=$id?>" <?=$checked?>>
									<label class="check-elem__label" for="<?=$attrId?>">
										<?=$title?>
									</label>
								</div>
							<? } ?>
							</div>
						<? } ?>



					</div>
				</div>
			</div>
		</div>
	</div>
</div>