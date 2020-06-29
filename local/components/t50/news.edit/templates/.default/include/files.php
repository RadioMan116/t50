<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<ul class="files-list">
	<? foreach($arResult["FILES"] as $file){ ?>
		<li class="files-list__item">
			<a href="<?=$file["PATH"]?>" class="files-list__link"><?=$file["TITLE"]?></a>
			<a href="#" class="files-list__remove js-news_file_remove" data-prop_id="<?=$file["PROP_ID"]?>">
				<svg class="files-list__remove-icon"><use xlink:href="<?=T50Html::getSvg("icon_delete")?>"></use></svg>
			</a>
		</li>
	<? } ?>
</ul>
