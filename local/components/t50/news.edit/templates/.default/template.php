<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$getLabel = function($code, $title) use($arResult){
	$errorClass = ( $arResult["FIELDS_ERROR"][$code] ? "color_red" : "" );
	return "<span class=\"form__label {$errorClass}\">{$title}</span>";
};
?>

<div class="grid-12__container">
	<div class="page__item">
		<div class="panel">
			<div class="panel__centric">
				<div class="panel__head panel__head_type_close">
					<h2 class="panel__title panel__title_type_subtitle panel__title_align_center">
						<?=( $arParams["IS_NEW"] ? "Добавить новость" : "Редактирование новости" )?>
					</h2>
					<? if( $arResult["SHOW_LAST_MODIFY_INFO"] ){ ?>
						<div class="panel__head-info panel__head-info_align_center panel__head-info_style_light">Последнее редактирование: <?=$arResult["DATE_MODIFY"]?> <?=$arResult["MODIFIED_BY"]["NAME"]?></div>
					<? } ?>
				</div>
				<? if( !empty($arResult["ERRORS"])){ ?>
					<div class="panel__head-info_align_center panel__important">
						<? foreach($arResult["ERRORS"] as $error){ ?>
							<span><?=$error?><br></span>
						<? } ?>
					</div>
				<? } ?>
				<div class="entry-form">
					<form method="post">
						<input type="hidden" name="send_form" value="Y">
						<?=bitrix_sessid_post()?>
						<div class="form" >
							<div class="grid-12__row">
								<div class="grid-12__col grid-12__col_size_6">
									<label class="form__line">
										<?=$getLabel("brand", "Бренд")?>
										<?=T50Html::select(
											"brand",
											$arResult["BRANDS"],
											[
												"cls" => "js-select form__select", "empty" => "-",
												"val" => $arResult["FIELDS_VALUE"]["brand"]
											]
										)?>
									</label>
								</div>
								<div class="grid-12__col grid-12__col_size_6">
									<label class="form__line">
										<?=$getLabel("theme", "Тема")?>
										<?=T50Html::select(
											"theme",
											$arResult["THEMES"],
											[
												"cls" => "js-select form__select", "empty" => "-",
												"val" => $arResult["FIELDS_VALUE"]["theme"]
											]
										)?>
									</label>
								</div>
							</div>
							<div class="grid-12__row">
								<div class="grid-12__col grid-12__col_size_9">
									<label class="form__line">
										<?=$getLabel("title", "Заголовок новости")?>
										<input value="<?=$arResult["FIELDS_VALUE"]["title"]?>" name="title" class="form__input" type="text" autocomplete="off" />
									</label>
								</div>
								<div class="grid-12__col grid-12__col_size_3">
									<label class="form__line">
										<span class="form__label">Автор</span>
										<input value="<?=$arResult["CREATED_BY"]["NAME"]?>" readonly="readonly" class="form__input" type="text" />
									</label>
								</div>
							</div>
							<div class="form__line">
								<a class="link link_style_default-trigger js-modal <?=( $arResult["FIELDS_ERROR"]["group"] ? "color_red" : "" )?>" href="#group_selection">
									Изменить отделы
								</a>
								<div class="form__tags">
									<select multiple="multiple" class="tags-list js_selected_groups"></select>
								</div>
							</div>
							<label class="form__line">
								<?=$getLabel("text", "Текст новости")?>
								<div class="formatted-text">
									<textarea name="text" class="form__textarea js-wysiwyg form__textarea_size_middle">
										<?=$arResult["FIELDS_VALUE"]["text"]?>
									</textarea>
								</div>
							</label>

							<? if( !$arParams["IS_NEW"] ){ ?>
							<label class="form__line">
								<?=$getLabel("comment", "Комментарий")?>
								<div class="formatted-text">
									<input value="<?=$arResult["FIELDS_VALUE"]["comment"]?>" class="form__input" type="text" name="comment"/>
								</div>
							</label>
							<? } ?>

							<label class="form__line">
								<div class="entry-form__file">
									<span class="form__label">Прикрепить документы</span>
									<span class="files-field">
										<input type="file" data-id="<?=$arResult["ID"]?>" class="hidden js-input-file" />
										<span class="files-field__list js-block_files">
											<?include __DIR__ . "/include/files.php"?>
										</span>
										<span class="files-field__controls">
											<span class="files-field__control">
												<a class="link link_style_default-trigger js-file-upload-trigger" href="#">Загрузить</a>
											</span>
										</span>
									</span>
								</div>
							</label>
						</div>
						<div class="entry-form__foot entry-form__foot_type_middle entry-form__foot_type_dual">
							<div class="dual-panel dual-panel_type_close">
								<div class="dual-panel__row">
									<div class="dual-panel__col dual-panel__col_align_left dual-panel__col_type_close">
										<div class="dual-panel">
											<div class="dual-panel__row">
												<div class="dual-panel__col dual-panel__col_align_left dual-panel__col_type_auto dual-panel__col_type_close">
													<div class="check-elem">
														<?=T50Html::checkboxLabel("fix_in_header", "Y",
															["cls" => "i", "val" => $arResult["FIELDS_VALUE"]["fix_in_header"]],
															"Закрепить новость в шапке",  "l"
														);?>
													</div>
												</div>
												<? if( !empty($arResult["DELETE_LINK"]) ){ ?>
													<div class="dual-panel__col dual-panel__col_align_right dual-panel__col_type_auto dual-panel__col_type_close">
														<a class="link link_type_delete js-delete_news" href="<?=$arResult["DELETE_LINK"]?>">Удалить новость<svg class="link__icon"><use xlink:href="<?=T50Html::getSvg("icon_delete")?>"></use></svg>
														</a>
													</div>
												<? } ?>
											</div>
										</div>
									</div>
									<div class="dual-panel__col dual-panel__col_align_right dual-panel__col_type_close">
										<div class="entry-form__control">
											<a class="button button_style_default button_width_full" href="/news/">Вернуться назад</a>
										</div>
										<div class="entry-form__control">
											<button class="button button_width_full" type="submit" >Опубликовать</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?include __DIR__ . "/include/modal.php";?>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	window["NEWS_COMMON_DATA"] = <?=CUtil::PhpToJSObject($arResult["JS_COMMON_DATA"])?>;
</script>