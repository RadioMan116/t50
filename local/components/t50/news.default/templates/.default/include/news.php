<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if( empty($ITEMS) )
	return;
?>

<div class="entry-list">

	<div class="entry-list__group-title"><?=$TITLE?></div>

	<? foreach($ITEMS as $item){ ?>
		<div class="entry-list__item page__item">
			<div class="panel">
				<div class="entry-snippet">
					<h2 class="entry-snippet__title"><?=$item["TITLE"]?></h2>
					<div class="entry-snippet__header">
						<div class="entry-snippet__author"><?=$item["AUTHOR"]?></div>
						<div class="entry-snippet__date"><?=$item["DATE_CREATE"]?></div>
						<div class="entry-snippet__category">
							<ul class="pill-nav">
								<? foreach($item["GROUPS"] as $id => $title){ ?>
									<li class="pill-nav__item">
										<a href="#<?=$id?>" class="pill-nav__link"><?=$title?></a>
									</li>
								<? } ?>
							</ul>
						</div>
					</div>
					<div class="entry-snippet__header">
						<div class="entry-snippet__author"><?=$item["BRAND"]?></div>
					</div>
					<div class="entry-snippet__content formatted-text">
						<? if( !empty($item["COMMENT"]) ){ ?>
							<div class="entry-snippet__comment"><?=$item["COMMENT"]?></div>
						<? } ?>
						<div class="entry-snippet__content-wrapper"><?=$item["TEXT"]?></div>
						<div class="entry-snippet__trigger">
							<div class="link link_style_dark-trigger js-entry-content-trigger" data-text="Свернуть новость">Показать всю новость</div>
						</div>
						<div class="">
							<? foreach($item["FILES"] as $file){ ?>
								<a href="<?=$file["PATH"]?>" target="_blank"><?=$file["TITLE"]?></a> <br>
							<? } ?>
						</div>
					</div>

					<? if( $TYPE == "UNREAD" ){ ?>
						<div class="entry-snippet__field">
							<div class="check-elem">
								<?=T50Html::checkboxLabel("read", $item["ID"], "check-elem__input js-checkbox-unread", "Прочитано", "l");?>
							</div>
						</div>
					<? } ?>

					<? if( $CAN_EDIT ){ ?>
						<div class="entry-snippet__controls">
							<div class="entry-snippet__control">
								<a class="button button_width_full" target="_blank" href="<?=$item["EDIT_URL"]?>">
									Редактировать новость
								</a>
							</div>
						</div>
					<? } ?>


					<a href="#" class="entry-snippet__favorites js_favorite
						<?=( $item["IS_FAVORITE"] ? "entry-snippet__favorites_state_active" : "" )?>"
						<?=T50Html::dataAttrs([
							"id" => $item["ID"],
							"is_favorite" => $item["IS_FAVORITE"],
						])?>
					>
						<svg class="entry-snippet__favorites-icon"><use xlink:href=<?=T50Html::getSvg("icon_fav")?>></use></svg>Избранное
					</a>
				</div>
			</div>
		</div>
	<? } ?>

	<?=$NAV?>

</div>