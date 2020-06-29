import GroupChecked from "./GroupChecked";
import CommonDataActions from "./CommonDataActions";
import AjaxActions from "./AjaxActions";
import FormActions from "./FormActions";
import RelativeModifications from "./RelativeModifications";

$(document).ready(function(){
    if( document.location.hash.length == 0 )
        $(".js-tabs-trigger__first").click();

    new FormActions;
    new AjaxActions;
    new CommonDataActions;
    new GroupChecked;
    new RelativeModifications;
});
