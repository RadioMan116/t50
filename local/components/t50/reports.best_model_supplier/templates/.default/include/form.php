<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form class="page__item">
	<input type="hidden" name="send_form" value="Y">
    <div class="panel">
        <div class="page__scroll">
            <div class="panel__content panel__content_size_big panel__content_type_clipped">
            	<div class="table table_style_simple table_width_auto">
			        <table class="table__main">
			            <tbody>
			                <tr>
			                    <td class="table__td">
			                    	<label class="form__line form__field form__field_size-xl_xs">
			                            <div class="form__label">Выбор магазина</div>
			                            <?=T50Html::select("shop", $arResult["SHOPS"], "s")?>
			                        </label>
			                    </td>
			                    <td class="table__td">
			                    	<label class="form__line form__field form__field_size-xl_xs">
			                            <div class="form__label">Выбор брендов</div>
			                            <?=T50Html::select("brands", $arResult["BRANDS"], ["cls" => "s", "mult"])?>
			                        </label>
			                    </td>
			                    <td class="table__td">
			                    	<label class="form__line form__field form__field_size-xl_xl">
			                            <div class="showcase__item">
                                            <div class="filter-panel__item">
                                                <div class="check-elem">
                                                	<?=T50Html::checkboxLabel(
                                                		"only_avail", "Y", "i", "Только в наличии", "l"
                                                	);?>
                                                </div>
                                            </div>
                                            <div class="filter-panel__item">
                                            	<div class="check-elem">
                                            		<?=T50Html::checkboxLabel(
                                            			"spb", "Y", "i", "Санкт-Петербург", "l"
                                            		);?>
	                                            </div>
                                            </div>
                                            <div class="filter-panel__item">
                                                <div class="check-elem">
                                                	<?=T50Html::checkboxLabel(
                                                		"cond_and_sale", "Y", "i",
                                                		"Условия и продажные цены", "l"
                                                	);?>
                                                </div>
                                            </div>
                                        </div>
			                        </label>
			                    </td>
			                </tr>
			            </tbody>
			        </table>
			    </div>
            </div>
        </div>
        <div class="page__scroll">
            <div class="panel__content panel__content_size_big panel__content_type_clipped">
                <div class="grid-12__row">
                    <div class="grid-12__col grid-12__col_size_12">
                		<div class="form__label">Поставщики</div>
                        <div class="filter-panel">
                            <div class="filter-panel__list">
                                <div class="showcase showcase_cols_auto">
									<div class="showcase__list">
									<? foreach(array_chunk($arResult["SUPPLIERS"], 7, true) as $suppliers){ ?>
										 <div class="showcase__item">
										<? foreach($suppliers as $id => $name){ ?>
											<div class="filter-panel__item">
                                                <div class="check-elem">
                                                	<?=T50Html::checkboxLabel(
                                                        "suppliers[]", $id, "i", $name, "l"
                                                    );?>
                                                </div>
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
            </div>
        </div>
        <div class="panel__section"></div>
        <div class="panel__controls panel__controls_type_separated">
            <div class="panel__control">
                <button class="button button_width_full button_style_dark" type="submit">Сформировать
                    отчет</button>
            </div>
            <div class="panel__control">
                <button class="button button_style_default button_width_full" type="reset" onclick="document.location.href=document.location.pathname">Сбросить</button>
            </div>
        </div>
    </div>
</form>

<?//echo "<pre>\$arResult "; print_r($arResult); echo "</pre>";?>