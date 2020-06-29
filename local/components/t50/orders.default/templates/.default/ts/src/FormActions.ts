import Base from "./Base";

export default class FormActions extends Base
{
    constructor(){
        super();
        $(".js-submit-filter").click(function (){
            var form = $("#filterAll, #filterTabs").not(".hidden").eq(0);
            form.submit();
        });

        $(".js-reset-filter").click(function (){
            document.location.href = document.location.pathname;
        });

    }
}