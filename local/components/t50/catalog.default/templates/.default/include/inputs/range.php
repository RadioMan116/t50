<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
######################################################################
# template without calculated prices range and without js slider
######################################################################

$name = $input["NAME"];
?>

<label class="form__line form__line_type_close form__field form__field_size-<?=$sizeClassSfx?>">
	<div class="form__label"><?=$input["TITLE"]?></div>
	<div class="range">
		<div class="range__fields">
			<div class="range__field">
				<input autocomplete="off" type="text" value="<?=$input["VALUE"][0]?>" class="form__input" name="<?=$name?>_from"/>
			</div>
			<div class="range__field">
				<input autocomplete="off" type="text" value="<?=$input["VALUE"][1]?>" class="form__input" name="<?=$name?>_to" />
			</div>
		</div>
		<div class="range__slider"></div>
	</div>
</label>




<?/*
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
######################################################################
# template for use calculated prices (range min - max) and use js slider
######################################################################

$min = $input["DATA"][0];
$max = $input["DATA"][1];
$start = ( isset($input["VALUE"][0]) ? $input["VALUE"][0] : $min );
$stop = ( isset($input["VALUE"][1]) ? $input["VALUE"][1] : $max );
$name = $input["NAME"];

$jsClass = ( isset($min) ? "js-range" : "" );
?>

<label class="form__line form__line_type_close form__field form__field_size-<?=$sizeClassSfx?>">
	<div class="form__label"><?=$input["TITLE"]?></div>
	<div class="range <?=$jsClass?>" data-min="<?=$min?>" data-max="<?=$max?>" data-start="<?=$start?>" data-stop="<?=$stop?>">
		<div class="range__fields">
			<div class="range__field">
				<input type="text" value="<?=$input["VALUE"][0]?>" class="range__input range__input_type_from" name="<?=$name?>_from"/>
			</div>
			<div class="range__field">
				<input type="text" value="<?=$input["VALUE"][1]?>" class="range__input range__input_type_to" name="<?=$name?>_to" />
			</div>
		</div>
		<div class="range__slider"></div>
	</div>
</label>
/*?>