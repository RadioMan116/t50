$(document).ready(function (){
	$(".js_select_change_submit").change(function (){
		var form = $(this).closest("form").submit();
	});
});