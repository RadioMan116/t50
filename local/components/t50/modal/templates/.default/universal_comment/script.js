$(document).ready(function (){
	let textarea = $("#universal_comment textarea");
	$("#universal_comment button").click(function (){
		T50PubSub.send("universal_prompt", textarea.val());
	});
});