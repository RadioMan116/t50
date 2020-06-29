<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if( $arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false) )
	return;
?>

<div class="entry-list__pagination">
	<ul class="pagination">
		<? foreach($arResult["ITEMS"] as $item){
			$linkClass = "pagination__link " . ( $item["ACTIVE"] ? "pagination__link_state_active" : "" );
		?>
			<li class="pagination__item">
				<a href="<?=$item["URL"]?>" class="js_pagen <?=$linkClass?>" data-page="<?=$item["NUM"]?>">
					<?=$item["NUM"]?>
				</a>
			</li>
		<? } ?>
	</ul>
</div>