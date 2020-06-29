$(document).ready(function (){
	$("#claim_filter_reset").click(function (){
		document.location.href = document.location.pathname;
	});

	var $resizeTable = $('.js-resize-table__for_claim');
	$resizeTable.colResizable({
		gripInnerHtml: "<div class='table__grip'></div>",
		resizeMode: 'fit'
	});
});