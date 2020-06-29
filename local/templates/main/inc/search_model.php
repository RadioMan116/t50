<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<form class="search-form" method="get" action="/catalog/" <?=( $targetBlank ? "target='_blank'" : "" )?>>
	<input type="hidden" name="send_form" value="Y">
	<input type="hidden" name="city" value="MSK">
	<input placeholder="Поиск модели..." value="" class="search-form__input" name="only_model" type="text">
</form>