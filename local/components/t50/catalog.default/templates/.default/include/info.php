<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if( empty($arResult["SHOP"]["DETAIL_TEXT"]) )
	return;

$officialName = $arResult["SHOP"]["PROPERTY_OFFICIAL_NAME_VALUE"];
if( empty($officialName) )
	$officialName = $arResult["SHOP"]["NAME"];
?>

<div class="page__item">
	<!-- begin .panel-->
	<div class="panel" id="fullDetails">
		<div class="panel__head">
			<div class="panel__head-main">
				<h2 class="panel__title panel__title_type_subtitle">Полные реквизиты <?=$officialName?></h2>
			</div>
			<div class="panel__head-controls"><a href="#" class="link link_type_pdf"><svg class="link__icon"><use xlink:href="assets/images/icon.svg#icon_pdf"></use></svg>Скачать
					реквизиты в PDF</a></div>
		</div>
		<div class="panel__spec panel__spec_type_close">
			<?=$arResult["SHOP"]["DETAIL_TEXT"]?>
		</div>
	</div><!-- end .panel-->
</div>