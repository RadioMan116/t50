$(document).ready(function (){
    var $form = $("#create_new_order form");
    var $button = $("#create_new_order button");
    function checkData(){
        $button.attr("disabled", "disabled");
        $button.addClass("button_style_default");

        if( !/shop/.test($form.serialize()) )
            return ;

        $button.removeClass("button_style_default");
        $button.attr("disabled", false);
    }
    $("#create_new_order input[name='shop']").click(checkData)
    checkData();
});