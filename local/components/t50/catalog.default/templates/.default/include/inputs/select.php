<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$params = ["cls" => "js-select form__select"];
$multySelect = ( $input["TYPE"] == "MSELECT" );
if( $multySelect )
	$params[] = "mult";

if( isset($input["SELECT_DEFAULT"]) )
	$params["empty"] = $input["SELECT_DEFAULT"];

$selectHTML = T50Html::select($input["NAME"], $input["DATA"], $params);

?>

<label class="form__line form__line_type_close form__field form__field_size-<?=$sizeClassSfx?>">
	<div class="form__label"><?=$input["TITLE"]?></div>
	<?=$selectHTML?>
</label>