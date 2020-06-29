import Base from "./Base";

export default class AjaxActions extends Base
{
    constructor(){
        super();
        // $('.js-block-toggle input').change();
        $(".js_save_selected_view input").change(this.changeView.bind(this));
    }

    changeView(e: JQueryEventObject){
        let type = e.target.id.replace("page_type_", "");
        T50Ajax.postJson("common_ajax", "set_cookie", {name: "ORDERS_FILTER_TYPE", value:  type});
    }
}