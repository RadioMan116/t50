<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// echo "<pre>\$arResult "; print_r($arResult); echo "</pre>";
?>
<div class="grid-12__container">
	<div class="page__item">
		<form action="" class="form" method="post">
			<?=bitrix_sessid_post()?>
			<input type="hidden" name="action" value="filter">
			<div class="panel">
				<div class="panel__head">
					<div class="panel__head-main">
						<h2 class="panel__title">Поставщики</h2>
					</div>
				</div>
				<div class="panel__content">
					<div class="panel__section">

						<div class="table table_style_simple table_width_auto">
							<table class="table__main">
								<td class="table__td">
									<label class="form__line form__field form__field_size-xl_xs">
										<div class="form__label">Магазин</div>
										<?=T50Html::select("shop", $arResult["SHOPS"], [
											"prepend" => ["" => "-"],
											"cls" => "js-select form__select js_select_change_submit"
										])?>
									</label>
								</td>
								<td class="table__td">
									<label class="form__line form__field form__field_size-xl_xs">
										<div class="form__label">Бренд</div>
										<?=T50Html::select("brand", $arResult["BRANDS"], [
											"prepend" => ["" => "-"],
											"cls" => "js-select form__select js_select_change_submit"
										])?>
									</label>
								</td>
								<td class="table__td">
									<label class="form__line form__field form__field_size-xl_xs">
										<div class="form__label">Категория</div>
										<?=T50Html::select("category", $arResult["CATEGORIES"], [
											"prepend" => ["" => "-"],
											"cls" => "js-select form__select js_select_change_submit"
										])?>
									</label>
								</td>
								<td class="table__td">
									<label class="form__line form__field form__field_size-xl_xs">
										<div class="form__label">Регион</div>
										<?=T50Html::select("region", $arResult["REGIONS"], [
											"cls" => "js-select form__select js_select_change_submit"
										])?>
									</label>
								</td>
							</table>
						</div>

						<?require __DIR__ . "/include/suppliers.php"?>

						<div class="table table_style_simple table_layout_fixed">
							<table class="table__main">
								<td class="table__td table__td_size-xl_xs table__td_close_bottom"><label class="form__line form__field">
										<div class="form__label">Менеджер</div>
										<input value="<?=$arResult["DATA"]["MANAGER"]?>" readonly class="form__input">
									</label>
								</td>
								<td class="table__td table__td_size-xl_xs table__td_close_bottom">
									<label class="form__line form__field">
										<div class="form__label">Дата отметки</div>
										<input value="<?=$arResult["DATA"]["DATE"]?>" readonly class="form__input">
									</label>
								</td>
								<td class="table__td table__td_close_bottom">
									<label class="form__line form__field">
										<div class="form__label">Обоснование</div>
										<input value="<?=$arResult["DATA"]["UF_COMMENT"]?>" name="comment" class="form__input">
									</label>
								</td>
							</table>
						</div>


					</div>

					<? if( $arResult["HAS_ACCESS"] ){ ?>
					<div class="panel__foot panel__foot_type_full">
						<div class="panel__main">
							<button class="button button_type_concrete" type="submit" name="action" value="update" >Сохранить</button>
						</div>
					</div>
					<? } ?>
				</div>
			</div>
		</form>
	</div>
</div>
