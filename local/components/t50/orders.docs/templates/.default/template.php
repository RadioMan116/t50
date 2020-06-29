<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? if( !empty($arResult["ERRORS"]) ){ ?>
<div class="grid-12__container grid-12__container_width_full panel">
	<? foreach($arResult["ERRORS"] as $error){ ?>
		<div class="link_style_marked "><?=$error?></div>
	<? } ?>
</div>
<? } ?>