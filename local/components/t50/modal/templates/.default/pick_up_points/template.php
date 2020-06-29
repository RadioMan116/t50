<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="modal modal_size_auto" id="pick_up_points">
	<h3 class="modal__title modal__title_align_center">Пункты самовывоза на карте</h3>
	<div class="modal__content">
		<div class="modal__map">
			<iframe src="" width="1106" height="594" frameborder="0"></iframe>
		</div>
	</div>
</div>
<script>
	$(document).ready(function (){
		$(".modal[id='pick_up_points']").on( "modal_loaded", (event, opts) => {
            let src = "https://yandex.ru/map-widget/v1/?um=constructor%3A6654de8482e4e9f3299ae65409f246a69bcccc731ac5f6c282f09841026f3d1d&amp;source=constructor";
            $("#pick_up_points iframe").attr("src", src);
        });
	})
</script>